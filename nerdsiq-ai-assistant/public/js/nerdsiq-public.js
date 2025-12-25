/**
 * NerdsIQ AI Assistant - Public JavaScript
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

(function($) {
    'use strict';

    class NerdsIQChat {
        constructor() {
            this.conversationId = null;
            this.isOpen = false;
            this.isTyping = false;

            this.init();
        }

        init() {
            this.cacheElements();
            this.bindEvents();
            this.loadConversationFromStorage();
            this.applyPosition();
        }

        cacheElements() {
            this.$container = $('#nerdsiq-chat-container');
            this.$launchBtn = $('#nerdsiq-launch-btn');
            this.$chatWindow = $('#nerdsiq-chat-window');
            this.$closeBtn = $('#nerdsiq-close-btn');
            this.$minimizeBtn = $('#nerdsiq-minimize-btn');
            this.$newConvBtn = $('#nerdsiq-new-conversation-btn');
            this.$messages = $('#nerdsiq-messages');
            this.$form = $('#nerdsiq-message-form');
            this.$input = $('#nerdsiq-input');
            this.$sendBtn = $('#nerdsiq-send-btn');
            this.$typingIndicator = $('#nerdsiq-typing-indicator');
        }

        bindEvents() {
            this.$launchBtn.on('click', () => this.toggleChat());
            this.$closeBtn.on('click', () => this.closeChat());
            this.$minimizeBtn.on('click', () => this.closeChat());
            this.$newConvBtn.on('click', () => this.resetConversation());
            this.$form.on('submit', (e) => this.handleSubmit(e));
            this.$input.on('input', () => this.handleInput());
        }

        applyPosition() {
            const position = nerdsiq_config.position || 'bottom-right';
            this.$container.addClass('position-' + position);
        }

        toggleChat() {
            if (this.isOpen) {
                this.closeChat();
            } else {
                this.openChat();
            }
        }

        openChat() {
            this.$chatWindow.fadeIn(300);
            this.$launchBtn.hide();
            this.isOpen = true;

            // Show welcome message if first time or no history
            if (this.$messages.children().length === 0) {
                this.showWelcomeMessage();
            }

            this.$input.focus();

            // Log event
            this.logEvent('widget_opened');
        }

        closeChat() {
            this.$chatWindow.fadeOut(300);
            this.$launchBtn.fadeIn(300);
            this.isOpen = false;

            // Log event
            this.logEvent('widget_closed');
        }

        showWelcomeMessage() {
            const welcomeMsg = nerdsiq_config.welcome_message || 'Hi! How can I help you today?';

            this.addMessage('assistant', welcomeMsg);

            // Add suggested questions if available
            const suggestions = nerdsiq_config.suggested_questions || [];
            if (suggestions.length > 0) {
                let suggestionsHtml = '<div class="nerdsiq-suggestions">';
                suggestions.forEach(question => {
                    suggestionsHtml += `<button class="nerdsiq-suggestion-btn" data-question="${this.escapeHtml(question)}">${this.escapeHtml(question)}</button>`;
                });
                suggestionsHtml += '</div>';

                this.$messages.append(suggestionsHtml);

                // Bind suggestion clicks
                $('.nerdsiq-suggestion-btn').on('click', (e) => {
                    const question = $(e.currentTarget).data('question');
                    this.$input.val(question);
                    this.$form.submit();
                });
            }
        }

        handleInput() {
            const length = this.$input.val().length;
            $('#nerdsiq-char-current').text(length);

            // Auto-resize textarea
            this.$input.css('height', 'auto');
            this.$input.css('height', this.$input[0].scrollHeight + 'px');
        }

        handleSubmit(e) {
            e.preventDefault();

            const message = this.$input.val().trim();
            if (!message || this.isTyping) {
                return;
            }

            // Add user message to chat
            this.addMessage('user', message);

            // Clear input
            this.$input.val('');
            this.handleInput();

            // Send to server
            this.sendMessage(message);
        }

        sendMessage(message) {
            this.showTyping();

            $.ajax({
                url: nerdsiq_config.ajax_url,
                type: 'POST',
                data: {
                    action: 'nerdsiq_send_message',
                    nonce: nerdsiq_config.nonce,
                    message: message,
                    conversation_id: this.conversationId
                },
                success: (response) => {
                    this.hideTyping();

                    if (response.success) {
                        const data = response.data;

                        // Update conversation ID
                        this.conversationId = data.conversation_id;
                        this.saveConversationToStorage();

                        // Add AI response
                        this.addMessage('assistant', data.message, data.sources);
                    } else {
                        this.showError(response.data.message || nerdsiq_config.strings.error);
                    }
                },
                error: () => {
                    this.hideTyping();
                    this.showError(nerdsiq_config.strings.error);
                }
            });
        }

        addMessage(type, content, sources = null) {
            const messageHtml = `
                <div class="nerdsiq-message ${type}">
                    <div class="nerdsiq-message-content">
                        ${this.formatMessage(content)}
                        ${sources && nerdsiq_config.show_citations ? this.formatSources(sources) : ''}
                    </div>
                </div>
            `;

            this.$messages.append(messageHtml);
            this.scrollToBottom();
        }

        formatMessage(message) {
            // Basic markdown formatting
            message = this.escapeHtml(message);
            message = message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            message = message.replace(/\*(.*?)\*/g, '<em>$1</em>');
            message = message.replace(/`(.*?)`/g, '<code>$1</code>');
            message = message.replace(/\n/g, '<br>');

            return message;
        }

        formatSources(sources) {
            if (!sources || sources.length === 0) {
                return '';
            }

            let html = '<div class="nerdsiq-sources"><strong>' + nerdsiq_config.strings.sources + ':</strong><ul>';
            sources.forEach(source => {
                if (source.url) {
                    html += `<li><a href="${this.escapeHtml(source.url)}" target="_blank" rel="noopener">${this.escapeHtml(source.title || source.url)}</a></li>`;
                }
            });
            html += '</ul></div>';

            return html;
        }

        showTyping() {
            this.isTyping = true;
            this.$sendBtn.prop('disabled', true);

            if (nerdsiq_config.show_typing_indicator) {
                this.$typingIndicator.fadeIn(200);
                this.scrollToBottom();
            }
        }

        hideTyping() {
            this.isTyping = false;
            this.$sendBtn.prop('disabled', false);
            this.$typingIndicator.fadeOut(200);
        }

        showError(message) {
            this.addMessage('assistant', '⚠️ ' + message);
        }

        resetConversation() {
            if (confirm(nerdsiq_config.strings.new_conversation + '?')) {
                this.conversationId = null;
                this.$messages.empty();
                this.clearConversationFromStorage();
                this.showWelcomeMessage();

                // Log event
                this.logEvent('conversation_reset');
            }
        }

        scrollToBottom() {
            this.$messages.scrollTop(this.$messages[0].scrollHeight);
        }

        saveConversationToStorage() {
            if (nerdsiq_config.enable_history && this.conversationId) {
                localStorage.setItem('nerdsiq_conversation_id', this.conversationId);
            }
        }

        loadConversationFromStorage() {
            if (nerdsiq_config.enable_history) {
                const savedId = localStorage.getItem('nerdsiq_conversation_id');
                if (savedId) {
                    this.conversationId = savedId;
                }
            }
        }

        clearConversationFromStorage() {
            localStorage.removeItem('nerdsiq_conversation_id');
        }

        logEvent(action, metadata = {}) {
            $.ajax({
                url: nerdsiq_config.ajax_url,
                type: 'POST',
                data: {
                    action: 'nerdsiq_log_event',
                    nonce: nerdsiq_config.nonce,
                    action_type: action,
                    metadata: JSON.stringify(metadata)
                }
            });
        }

        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        new NerdsIQChat();
    });

})(jQuery);
