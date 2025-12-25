<?php
/**
 * Chat Widget HTML Template
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

$logo_url = get_option( 'nerdsiq_logo_url', '' );
$header_title = get_option( 'nerdsiq_header_title', 'NerdsIQ AI Assistant' );
?>

<!-- NerdsIQ AI Assistant Chat Widget -->
<div id="nerdsiq-chat-container" class="nerdsiq-chat-container">
    <!-- Launch Button -->
    <button id="nerdsiq-launch-btn" class="nerdsiq-launch-btn" aria-label="<?php esc_attr_e( 'Open NerdsIQ AI Assistant', 'nerdsiq-ai-assistant' ); ?>">
        <?php if ( ! empty( $logo_url ) ) : ?>
            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $header_title ); ?>" class="nerdsiq-btn-logo" style="width: 28px; height: 28px; object-fit: contain;" />
        <?php else : ?>
            <svg class="nerdsiq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill="currentColor"/>
                <path d="M7 9H17V11H7V9Z" fill="currentColor"/>
                <path d="M7 12H14V14H7V12Z" fill="currentColor"/>
            </svg>
        <?php endif; ?>
        <span class="nerdsiq-btn-text"><?php echo esc_html( get_option( 'nerdsiq_button_text', 'Ask NerdsIQ' ) ); ?></span>
    </button>

    <!-- Chat Window -->
    <div id="nerdsiq-chat-window" class="nerdsiq-chat-window" style="display: none;" role="dialog" aria-labelledby="nerdsiq-header-title" aria-modal="true">
        <!-- Header -->
        <div class="nerdsiq-header">
            <div class="nerdsiq-header-content">
                <?php if ( ! empty( $logo_url ) ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $header_title ); ?>" class="nerdsiq-header-logo" style="width: 32px; height: 32px; object-fit: contain; margin-right: 10px;" />
                <?php else : ?>
                    <svg class="nerdsiq-header-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="currentColor"/>
                        <path d="M12 6C8.69 6 6 8.69 6 12C6 15.31 8.69 18 12 18C15.31 18 18 15.31 18 12C18 8.69 15.31 6 12 6ZM12 16C9.79 16 8 14.21 8 12C8 9.79 9.79 8 12 8C14.21 8 16 9.79 16 12C16 14.21 14.21 16 12 16Z" fill="currentColor"/>
                    </svg>
                <?php endif; ?>
                <h3 id="nerdsiq-header-title" class="nerdsiq-header-title"><?php echo esc_html( $header_title ); ?></h3>
            </div>
            <div class="nerdsiq-header-actions">
                <button id="nerdsiq-new-conversation-btn" class="nerdsiq-icon-btn" aria-label="<?php esc_attr_e( 'Start new conversation', 'nerdsiq-ai-assistant' ); ?>" title="<?php esc_attr_e( 'New Conversation', 'nerdsiq-ai-assistant' ); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z" fill="currentColor"/>
                    </svg>
                </button>
                <button id="nerdsiq-maximize-btn" class="nerdsiq-icon-btn" aria-label="<?php esc_attr_e( 'Maximize chat', 'nerdsiq-ai-assistant' ); ?>" title="<?php esc_attr_e( 'Maximize', 'nerdsiq-ai-assistant' ); ?>">
                    <svg class="nerdsiq-maximize-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 14H5V19H10V17H7V14ZM5 10H7V7H10V5H5V10ZM17 17H14V19H19V14H17V17ZM14 5V7H17V10H19V5H14Z" fill="currentColor"/>
                    </svg>
                    <svg class="nerdsiq-restore-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                        <path d="M5 16H8V19H10V14H5V16ZM8 8H5V10H10V5H8V8ZM14 19H16V16H19V14H14V19ZM16 8V5H14V10H19V8H16Z" fill="currentColor"/>
                    </svg>
                </button>
                <button id="nerdsiq-minimize-btn" class="nerdsiq-icon-btn" aria-label="<?php esc_attr_e( 'Minimize chat', 'nerdsiq-ai-assistant' ); ?>" title="<?php esc_attr_e( 'Minimize', 'nerdsiq-ai-assistant' ); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 13H5V11H19V13Z" fill="currentColor"/>
                    </svg>
                </button>
                <button id="nerdsiq-close-btn" class="nerdsiq-icon-btn" aria-label="<?php esc_attr_e( 'Close chat', 'nerdsiq-ai-assistant' ); ?>" title="<?php esc_attr_e( 'Close', 'nerdsiq-ai-assistant' ); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="nerdsiq-messages" class="nerdsiq-messages" role="log" aria-live="polite" aria-relevant="additions">
            <!-- Welcome message will be inserted here by JavaScript -->
        </div>

        <!-- Typing Indicator -->
        <div id="nerdsiq-typing-indicator" class="nerdsiq-typing-indicator" style="display: none;">
            <div class="nerdsiq-typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <span class="nerdsiq-typing-text"><?php esc_html_e( 'NerdsIQ is typing...', 'nerdsiq-ai-assistant' ); ?></span>
        </div>

        <!-- Input Area -->
        <div class="nerdsiq-input-area">
            <form id="nerdsiq-message-form" class="nerdsiq-message-form">
                <textarea
                    id="nerdsiq-input"
                    class="nerdsiq-input"
                    placeholder="<?php esc_attr_e( 'Type your message...', 'nerdsiq-ai-assistant' ); ?>"
                    rows="1"
                    maxlength="2000"
                    aria-label="<?php esc_attr_e( 'Message input', 'nerdsiq-ai-assistant' ); ?>"
                ></textarea>
                <button type="submit" id="nerdsiq-send-btn" class="nerdsiq-send-btn" aria-label="<?php esc_attr_e( 'Send message', 'nerdsiq-ai-assistant' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01 21L23 12L2.01 3L2 10L17 12L2 14L2.01 21Z" fill="currentColor"/>
                    </svg>
                </button>
            </form>
            <div id="nerdsiq-char-count" class="nerdsiq-char-count">
                <span id="nerdsiq-char-current">0</span> / 2000
            </div>
        </div>
    </div>
</div>
