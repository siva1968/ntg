<?php
/**
 * Security and Encryption Handler
 *
 * Handles encryption/decryption of sensitive data like AWS credentials.
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Security and Encryption Class
 */
class NerdsIQ_Security {

    /**
     * Encryption method
     *
     * @var string
     */
    private static $cipher = 'AES-256-CBC';

    /**
     * Get encryption key
     *
     * @since  1.0.0
     * @return string
     */
    private static function get_encryption_key() {
        // Use WordPress salts to generate encryption key
        $key = AUTH_KEY . SECURE_AUTH_KEY;

        // Hash to get consistent length
        return hash( 'sha256', $key, true );
    }

    /**
     * Encrypt data
     *
     * @since  1.0.0
     * @param  string $data Data to encrypt.
     * @return string|false Encrypted data or false on failure.
     */
    public static function encrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }

        try {
            // Generate initialization vector
            $iv_length = openssl_cipher_iv_length( self::$cipher );
            $iv = openssl_random_pseudo_bytes( $iv_length );

            // Encrypt the data
            $encrypted = openssl_encrypt(
                $data,
                self::$cipher,
                self::get_encryption_key(),
                OPENSSL_RAW_DATA,
                $iv
            );

            if ( false === $encrypted ) {
                return false;
            }

            // Combine IV and encrypted data
            $result = base64_encode( $iv . $encrypted );

            return $result;
        } catch ( Exception $e ) {
            error_log( 'NerdsIQ Encryption Error: ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Decrypt data
     *
     * @since  1.0.0
     * @param  string $data Encrypted data.
     * @return string|false Decrypted data or false on failure.
     */
    public static function decrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }

        try {
            // Decode the data
            $decoded = base64_decode( $data );

            if ( false === $decoded ) {
                return false;
            }

            // Extract IV
            $iv_length = openssl_cipher_iv_length( self::$cipher );
            $iv = substr( $decoded, 0, $iv_length );
            $encrypted = substr( $decoded, $iv_length );

            // Decrypt the data
            $decrypted = openssl_decrypt(
                $encrypted,
                self::$cipher,
                self::get_encryption_key(),
                OPENSSL_RAW_DATA,
                $iv
            );

            return $decrypted;
        } catch ( Exception $e ) {
            error_log( 'NerdsIQ Decryption Error: ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Hash data (one-way)
     *
     * @since  1.0.0
     * @param  string $data Data to hash.
     * @return string Hashed data.
     */
    public static function hash( $data ) {
        return hash( 'sha256', $data );
    }

    /**
     * Generate secure random token
     *
     * @since  1.0.0
     * @param  int $length Length of token.
     * @return string Random token.
     */
    public static function generate_token( $length = 32 ) {
        return bin2hex( random_bytes( $length / 2 ) );
    }

    /**
     * Sanitize user input
     *
     * @since  1.0.0
     * @param  string $input User input.
     * @return string Sanitized input.
     */
    public static function sanitize_input( $input ) {
        // Remove HTML tags
        $input = wp_strip_all_tags( $input );

        // Remove null bytes
        $input = str_replace( chr( 0 ), '', $input );

        // Trim whitespace
        $input = trim( $input );

        return $input;
    }

    /**
     * Validate and sanitize message content
     *
     * @since  1.0.0
     * @param  string $message Message content.
     * @param  int    $max_length Maximum allowed length.
     * @return array Array with 'valid' boolean and 'message' string.
     */
    public static function validate_message( $message, $max_length = 2000 ) {
        $result = array(
            'valid'   => false,
            'message' => '',
            'error'   => '',
        );

        // Sanitize input
        $message = self::sanitize_input( $message );

        // Check if empty
        if ( empty( $message ) ) {
            $result['error'] = __( 'Message cannot be empty.', 'nerdsiq-ai-assistant' );
            return $result;
        }

        // Check length
        if ( strlen( $message ) > $max_length ) {
            $result['error'] = sprintf(
                /* translators: %d: Maximum message length */
                __( 'Message exceeds maximum length of %d characters.', 'nerdsiq-ai-assistant' ),
                $max_length
            );
            return $result;
        }

        // Check for suspicious patterns
        $suspicious_patterns = array(
            '/<script/i',
            '/<iframe/i',
            '/javascript:/i',
            '/onerror=/i',
            '/onclick=/i',
        );

        foreach ( $suspicious_patterns as $pattern ) {
            if ( preg_match( $pattern, $message ) ) {
                $result['error'] = __( 'Message contains invalid content.', 'nerdsiq-ai-assistant' );
                return $result;
            }
        }

        $result['valid'] = true;
        $result['message'] = $message;

        return $result;
    }

    /**
     * Get user IP address
     *
     * @since  1.0.0
     * @return string User IP address.
     */
    public static function get_user_ip() {
        $ip = '';

        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Validate IP
        if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
            return $ip;
        }

        return '0.0.0.0';
    }

    /**
     * Redact PII from text
     *
     * @since  1.0.0
     * @param  string $text Text to redact.
     * @return string Redacted text.
     */
    public static function redact_pii( $text ) {
        // Redact email addresses
        $text = preg_replace( '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[EMAIL_REDACTED]', $text );

        // Redact phone numbers (various formats)
        $text = preg_replace( '/\b\d{3}[-.]?\d{3}[-.]?\d{4}\b/', '[PHONE_REDACTED]', $text );

        // Redact SSN-like patterns
        $text = preg_replace( '/\b\d{3}-\d{2}-\d{4}\b/', '[SSN_REDACTED]', $text );

        // Redact credit card numbers
        $text = preg_replace( '/\b\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}\b/', '[CARD_REDACTED]', $text );

        return $text;
    }
}
