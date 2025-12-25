<?php
/**
 * AWS Q Business API Client
 *
 * Handles all communication with AWS Q Business API.
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * AWS Q Business API Client Class
 */
class NerdsIQ_AWS_Client {

    /**
     * AWS credentials
     *
     * @var array
     */
    private $credentials;

    /**
     * AWS region
     *
     * @var string
     */
    private $region;

    /**
     * Q Business Application ID
     *
     * @var string
     */
    private $app_id;

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->load_credentials();
        $this->region = get_option( 'nerdsiq_aws_region', 'us-east-1' );
        $this->app_id = get_option( 'nerdsiq_qbusiness_app_id', '' );
    }

    /**
     * Load and decrypt AWS credentials
     *
     * @since 1.0.0
     */
    private function load_credentials() {
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-security.php';

        $encrypted_access_key = get_option( 'nerdsiq_aws_access_key', '' );
        $encrypted_secret_key = get_option( 'nerdsiq_aws_secret_key', '' );

        $this->credentials = array(
            'access_key' => NerdsIQ_Security::decrypt( $encrypted_access_key ),
            'secret_key' => NerdsIQ_Security::decrypt( $encrypted_secret_key ),
        );
    }

    /**
     * Test AWS connection
     *
     * @since  1.0.0
     * @return array Array with 'success' boolean and 'message' string.
     */
    public function test_connection() {
        $start_time = microtime( true );

        try {
            // Validate credentials exist
            if ( empty( $this->credentials['access_key'] ) || empty( $this->credentials['secret_key'] ) ) {
                return array(
                    'success' => false,
                    'message' => __( 'AWS credentials are not configured.', 'nerdsiq-ai-assistant' ),
                );
            }

            // Validate app ID exists
            if ( empty( $this->app_id ) ) {
                return array(
                    'success' => false,
                    'message' => __( 'Q Business Application ID is not configured.', 'nerdsiq-ai-assistant' ),
                );
            }

            // Make a test API call
            $result = $this->make_api_request(
                'GET',
                "/applications/{$this->app_id}",
                array()
            );

            $latency = round( ( microtime( true ) - $start_time ) * 1000 );

            if ( $result['success'] ) {
                return array(
                    'success' => true,
                    'message' => sprintf(
                        /* translators: %d: Latency in milliseconds */
                        __( 'Connection successful! Latency: %dms', 'nerdsiq-ai-assistant' ),
                        $latency
                    ),
                    'latency' => $latency,
                );
            } else {
                return array(
                    'success' => false,
                    'message' => $result['message'],
                );
            }
        } catch ( Exception $e ) {
            require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
            NerdsIQ_Logger::log_error(
                'aws_connection_test',
                $e->getMessage(),
                $e->getTraceAsString()
            );

            return array(
                'success' => false,
                'message' => sprintf(
                    /* translators: %s: Error message */
                    __( 'Connection failed: %s', 'nerdsiq-ai-assistant' ),
                    $e->getMessage()
                ),
            );
        }
    }

    /**
     * Send a message to Q Business
     *
     * @since  1.0.0
     * @param  string      $message          User message.
     * @param  string|null $conversation_id  Conversation ID (null for new conversation).
     * @param  int         $user_id          WordPress user ID.
     * @return array Response with 'success', 'message', 'sources', and 'conversation_id'.
     */
    public function send_message( $message, $conversation_id = null, $user_id = null ) {
        $start_time = microtime( true );

        try {
            // Validate message
            require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-security.php';
            $validation = NerdsIQ_Security::validate_message( $message );

            if ( ! $validation['valid'] ) {
                return array(
                    'success' => false,
                    'message' => $validation['error'],
                );
            }

            // Prepare request body
            $body = array(
                'applicationId' => $this->app_id,
                'userMessage'   => $validation['message'],
            );

            // Add conversation ID if continuing conversation
            if ( ! empty( $conversation_id ) ) {
                $body['conversationId'] = $conversation_id;
            }

            // Add user context if available
            if ( $user_id ) {
                $user = get_user_by( 'id', $user_id );
                if ( $user ) {
                    $body['userId'] = $user->user_login;
                    $body['userGroups'] = $user->roles;
                }
            }

            // Make API request
            $result = $this->make_api_request(
                'POST',
                '/conversations/messages',
                $body
            );

            if ( ! $result['success'] ) {
                return $result;
            }

            // Parse response
            $response_data = $result['data'];
            $response_time = round( ( microtime( true ) - $start_time ) * 1000 );

            // Extract response message
            $ai_message = isset( $response_data['systemMessage'] ) ? $response_data['systemMessage'] : '';

            // Extract sources/citations
            $sources = $this->extract_sources( $response_data );

            // Get or create conversation ID
            $conv_id = isset( $response_data['conversationId'] ) ? $response_data['conversationId'] : $conversation_id;

            return array(
                'success'         => true,
                'message'         => $ai_message,
                'sources'         => $sources,
                'conversation_id' => $conv_id,
                'response_time'   => $response_time,
            );
        } catch ( Exception $e ) {
            require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
            NerdsIQ_Logger::log_error(
                'aws_send_message',
                $e->getMessage(),
                $e->getTraceAsString(),
                array(
                    'message' => $message,
                    'conversation_id' => $conversation_id,
                )
            );

            return array(
                'success' => false,
                'message' => sprintf(
                    /* translators: %s: Error message */
                    __( 'Failed to send message: %s', 'nerdsiq-ai-assistant' ),
                    $e->getMessage()
                ),
            );
        }
    }

    /**
     * Get conversation history
     *
     * @since  1.0.0
     * @param  string $conversation_id Conversation ID.
     * @return array Response with 'success' and 'messages' array.
     */
    public function get_conversation_history( $conversation_id ) {
        try {
            if ( empty( $conversation_id ) ) {
                return array(
                    'success' => false,
                    'message' => __( 'Conversation ID is required.', 'nerdsiq-ai-assistant' ),
                );
            }

            $result = $this->make_api_request(
                'GET',
                "/conversations/{$conversation_id}/messages",
                array()
            );

            if ( ! $result['success'] ) {
                return $result;
            }

            $messages = isset( $result['data']['messages'] ) ? $result['data']['messages'] : array();

            return array(
                'success'  => true,
                'messages' => $messages,
            );
        } catch ( Exception $e ) {
            require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
            NerdsIQ_Logger::log_error(
                'aws_get_history',
                $e->getMessage(),
                $e->getTraceAsString(),
                array( 'conversation_id' => $conversation_id )
            );

            return array(
                'success' => false,
                'message' => sprintf(
                    /* translators: %s: Error message */
                    __( 'Failed to get conversation history: %s', 'nerdsiq-ai-assistant' ),
                    $e->getMessage()
                ),
            );
        }
    }

    /**
     * Make API request to AWS Q Business
     *
     * @since  1.0.0
     * @param  string $method HTTP method.
     * @param  string $path   API path.
     * @param  array  $body   Request body.
     * @return array Response with 'success' and 'data' or 'message'.
     */
    private function make_api_request( $method, $path, $body = array() ) {
        // Build API endpoint
        $endpoint = "https://qbusiness.{$this->region}.amazonaws.com{$path}";

        // Prepare request headers
        $headers = array(
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        );

        // Add AWS Signature V4 authentication
        $headers = $this->add_aws_auth_headers( $method, $path, $body, $headers );

        // Prepare request arguments
        $args = array(
            'method'  => $method,
            'headers' => $headers,
            'timeout' => get_option( 'nerdsiq_api_timeout', 30 ),
        );

        // Add body for POST/PUT requests
        if ( in_array( $method, array( 'POST', 'PUT' ), true ) && ! empty( $body ) ) {
            $args['body'] = wp_json_encode( $body );
        }

        // Make request with retry logic
        $max_retries = get_option( 'nerdsiq_api_retry_attempts', 2 );
        $attempt = 0;
        $last_error = '';

        while ( $attempt <= $max_retries ) {
            $response = wp_remote_request( $endpoint, $args );

            if ( ! is_wp_error( $response ) ) {
                $status_code = wp_remote_retrieve_response_code( $response );
                $response_body = wp_remote_retrieve_body( $response );

                if ( $status_code >= 200 && $status_code < 300 ) {
                    // Success
                    $data = json_decode( $response_body, true );

                    return array(
                        'success' => true,
                        'data'    => $data ? $data : array(),
                    );
                } elseif ( $status_code >= 500 && $attempt < $max_retries ) {
                    // Server error, retry
                    $attempt++;
                    sleep( pow( 2, $attempt ) ); // Exponential backoff
                    continue;
                } else {
                    // Client error or max retries reached
                    $error_data = json_decode( $response_body, true );
                    $last_error = isset( $error_data['message'] ) ? $error_data['message'] : "HTTP {$status_code} error";
                    break;
                }
            } else {
                // WordPress error
                $last_error = $response->get_error_message();
                if ( $attempt < $max_retries ) {
                    $attempt++;
                    sleep( pow( 2, $attempt ) );
                    continue;
                }
                break;
            }
        }

        return array(
            'success' => false,
            'message' => $last_error,
        );
    }

    /**
     * Add AWS Signature V4 authentication headers
     *
     * @since  1.0.0
     * @param  string $method  HTTP method.
     * @param  string $path    API path.
     * @param  array  $body    Request body.
     * @param  array  $headers Existing headers.
     * @return array Headers with authentication added.
     */
    private function add_aws_auth_headers( $method, $path, $body, $headers ) {
        // For a production implementation, this would include full AWS Signature V4 signing
        // For now, we'll use a simplified version assuming AWS SDK or IAM roles

        // If using AWS SDK (recommended), the SDK handles authentication
        // This is a placeholder for custom implementation

        $access_key = $this->credentials['access_key'];
        $secret_key = $this->credentials['secret_key'];

        if ( empty( $access_key ) || empty( $secret_key ) ) {
            return $headers;
        }

        // Add AWS authentication headers
        // Note: In production, you should use the AWS SDK for PHP which handles this automatically
        $headers['X-Amz-Date'] = gmdate( 'Ymd\THis\Z' );

        // Placeholder for AWS Signature V4
        // In a real implementation, use AWS SDK or implement full signature logic

        return $headers;
    }

    /**
     * Extract sources/citations from API response
     *
     * @since  1.0.0
     * @param  array $response API response data.
     * @return array Array of source citations.
     */
    private function extract_sources( $response ) {
        $sources = array();

        if ( isset( $response['sourceAttributions'] ) && is_array( $response['sourceAttributions'] ) ) {
            foreach ( $response['sourceAttributions'] as $source ) {
                $sources[] = array(
                    'title'   => isset( $source['title'] ) ? $source['title'] : '',
                    'url'     => isset( $source['url'] ) ? $source['url'] : '',
                    'excerpt' => isset( $source['textExcerpt'] ) ? $source['textExcerpt'] : '',
                );
            }
        }

        return $sources;
    }

    /**
     * Check if AWS credentials are configured
     *
     * @since  1.0.0
     * @return bool True if configured, false otherwise.
     */
    public function is_configured() {
        return ! empty( $this->credentials['access_key'] ) &&
               ! empty( $this->credentials['secret_key'] ) &&
               ! empty( $this->app_id );
    }
}
