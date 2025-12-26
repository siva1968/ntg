<?php
/**
 * AWS Q Business API Client
 *
 * Handles all communication with AWS Q Business API using AWS SDK.
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Load AWS SDK autoloader
$aws_autoloader = NERDSIQ_PLUGIN_DIR . 'vendor/autoload.php';
if ( file_exists( $aws_autoloader ) ) {
    require_once $aws_autoloader;
}

use Aws\QBusiness\QBusinessClient;
use Aws\Exception\AwsException;

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
     * Q Business Client instance
     *
     * @var QBusinessClient|null
     */
    private $client = null;

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
     * Get the AWS Q Business client instance
     *
     * @since  1.0.0
     * @return QBusinessClient|null
     */
    private function get_client() {
        if ( $this->client !== null ) {
            return $this->client;
        }

        if ( empty( $this->credentials['access_key'] ) || empty( $this->credentials['secret_key'] ) ) {
            return null;
        }

        try {
            $this->client = new QBusinessClient([
                'version'     => 'latest',
                'region'      => $this->region,
                'credentials' => [
                    'key'    => $this->credentials['access_key'],
                    'secret' => $this->credentials['secret_key'],
                ],
            ]);

            return $this->client;
        } catch ( Exception $e ) {
            require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
            NerdsIQ_Logger::log_error(
                'aws_client_init',
                $e->getMessage(),
                $e->getTraceAsString()
            );
            return null;
        }
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

            // Validate access key format (should start with AKIA, ASIA, or AIDA)
            $access_key = $this->credentials['access_key'];
            if ( ! preg_match( '/^(AKIA|ASIA|AIDA|AROA|AIPA|ANPA|ANVA|AGPA)[A-Z0-9]{16}$/', $access_key ) ) {
                // Check if keys might be swapped
                $secret_key = $this->credentials['secret_key'];
                if ( preg_match( '/^(AKIA|ASIA|AIDA|AROA|AIPA|ANPA|ANVA|AGPA)[A-Z0-9]{16}$/', $secret_key ) ) {
                    return array(
                        'success' => false,
                        'message' => __( 'Error: Access Key and Secret Key appear to be swapped. Please re-enter your credentials with Access Key ID in the first field (starts with AKIA) and Secret Access Key in the second field.', 'nerdsiq-ai-assistant' ),
                    );
                }
                return array(
                    'success' => false,
                    'message' => sprintf(
                        __( 'Invalid Access Key format. AWS Access Key IDs start with AKIA and are 20 characters. Your key starts with: %s', 'nerdsiq-ai-assistant' ),
                        substr( $access_key, 0, 4 ) . '...'
                    ),
                );
            }

            // Validate app ID exists
            if ( empty( $this->app_id ) ) {
                return array(
                    'success' => false,
                    'message' => __( 'Q Business Application ID is not configured.', 'nerdsiq-ai-assistant' ),
                );
            }

            $client = $this->get_client();
            if ( ! $client ) {
                return array(
                    'success' => false,
                    'message' => __( 'Failed to initialize AWS client.', 'nerdsiq-ai-assistant' ),
                );
            }

            // Make a test API call to get application details
            $result = $client->getApplication([
                'applicationId' => $this->app_id,
            ]);

            $latency = round( ( microtime( true ) - $start_time ) * 1000 );

            return array(
                'success' => true,
                'message' => sprintf(
                    /* translators: %d: Latency in milliseconds */
                    __( 'Connection successful! Latency: %dms', 'nerdsiq-ai-assistant' ),
                    $latency
                ),
                'latency' => $latency,
                'app_name' => isset( $result['displayName'] ) ? $result['displayName'] : '',
            );

        } catch ( AwsException $e ) {
            require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
            NerdsIQ_Logger::log_error(
                'aws_connection_test',
                $e->getAwsErrorMessage() ?: $e->getMessage(),
                $e->getTraceAsString()
            );

            $error_code = $e->getAwsErrorCode();
            $error_message = $e->getAwsErrorMessage() ?: $e->getMessage();

            // Provide helpful error messages
            if ( $error_code === 'UnrecognizedClientException' || $error_code === 'InvalidSignatureException' ) {
                $error_message = __( 'Invalid AWS credentials. Please check your Access Key ID and Secret Access Key.', 'nerdsiq-ai-assistant' );
            } elseif ( $error_code === 'AccessDeniedException' ) {
                $error_message = __( 'Access denied. Please ensure your IAM user has permissions for Q Business.', 'nerdsiq-ai-assistant' );
            } elseif ( $error_code === 'ResourceNotFoundException' ) {
                $error_message = __( 'Q Business Application not found. Please check the Application ID.', 'nerdsiq-ai-assistant' );
            }

            return array(
                'success' => false,
                'message' => sprintf(
                    /* translators: %s: Error message */
                    __( 'Connection failed: %s', 'nerdsiq-ai-assistant' ),
                    $error_message
                ),
            );
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

            $client = $this->get_client();
            if ( ! $client ) {
                return array(
                    'success' => false,
                    'message' => __( 'AWS client not configured.', 'nerdsiq-ai-assistant' ),
                );
            }

            // Prepare request parameters
            $params = array(
                'applicationId'      => $this->app_id,
                'userMessage'        => $validation['message'],
            );

            // Note: Conversation continuation is disabled for ANONYMOUS identity apps
            // Each message starts a new conversation to avoid message ID mismatch errors
            // AWS Q Business ANONYMOUS apps don't support multi-turn conversations properly

            // Note: userId is NOT sent for ANONYMOUS identity type applications
            // AWS Q Business requires no userId for anonymous apps

            // Make API request using chatSync
            $result = $client->chatSync( $params );

            $response_time = round( ( microtime( true ) - $start_time ) * 1000 );

            // Extract response message
            $ai_message = isset( $result['systemMessage'] ) ? $result['systemMessage'] : '';

            // Extract sources/citations
            $sources = $this->extract_sources( $result->toArray() );

            // Get conversation ID
            $conv_id = isset( $result['conversationId'] ) ? $result['conversationId'] : $conversation_id;

            return array(
                'success'         => true,
                'message'         => $ai_message,
                'sources'         => $sources,
                'conversation_id' => $conv_id,
                'response_time'   => $response_time,
            );

        } catch ( AwsException $e ) {
            require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
            NerdsIQ_Logger::log_error(
                'aws_send_message',
                $e->getAwsErrorMessage() ?: $e->getMessage(),
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
                    $e->getAwsErrorMessage() ?: $e->getMessage()
                ),
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

            $client = $this->get_client();
            if ( ! $client ) {
                return array(
                    'success' => false,
                    'message' => __( 'AWS client not configured.', 'nerdsiq-ai-assistant' ),
                );
            }

            $result = $client->listMessages([
                'applicationId'  => $this->app_id,
                'conversationId' => $conversation_id,
            ]);

            $messages = isset( $result['messages'] ) ? $result['messages'] : array();

            return array(
                'success'  => true,
                'messages' => $messages,
            );
        } catch ( AwsException $e ) {
            require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
            NerdsIQ_Logger::log_error(
                'aws_get_history',
                $e->getAwsErrorMessage() ?: $e->getMessage(),
                $e->getTraceAsString(),
                array( 'conversation_id' => $conversation_id )
            );

            return array(
                'success' => false,
                'message' => sprintf(
                    /* translators: %s: Error message */
                    __( 'Failed to get conversation history: %s', 'nerdsiq-ai-assistant' ),
                    $e->getAwsErrorMessage() ?: $e->getMessage()
                ),
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
