// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * AI Person Chat interface functionality.
 *
 * @module     mod_aipersonchat/chat
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, ajax, str, notification) {
    
    var Chat = {
        cmid: null,
        aipersonchatid: null,
        maxMessages: 50,
        currentMessageCount: 0,
        personName: '',
        
        /**
         * Initialize the chat interface.
         * @param {int} cmid Course module ID
         * @param {int} aipersonchatid AI Person Chat instance ID
         */
    init: function(cmid, aipersonchatid) {
            this.cmid = cmid;
            this.aipersonchatid = aipersonchatid;
            
            this.bindEvents();
            this.loadMessages();
        },
        
        /**
         * Bind event handlers.
         */
        bindEvents: function() {
            var self = this;
            
            // Handle form submission
            $('#aipersonchat-form').on('submit', function(e) {
                e.preventDefault();
                self.sendMessage();
            });
            
            // Handle enter key in input
            $('#aipersonchat-input').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    self.sendMessage();
                }
            });
            
            // Auto-resize input
            $('#aipersonchat-input').on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        },
        
        /**
         * Load existing messages.
         */
        loadMessages: function() {
            var self = this;
            
            ajax.call([{
                methodname: 'mod_aipersonchat_get_messages',
                args: { cmid: this.cmid, since: 0 }
            }])[0].done(function(response) {
                self.maxMessages = response.maxmessages;
                self.currentMessageCount = response.messagecount;
                self.personName = response.personname;
                
                self.displayMessages(response.messages);
                self.updateMessageCounter();
            }).fail(function(error) {
                notification.exception(error);
            });
        },
        
        /**
         * Send a message to the AI.
         */
        sendMessage: function() {
            var self = this;
            var input = $('#aipersonchat-input');
            var message = input.val().trim();
            
            if (!message) {
                return;
            }
            
            if (this.currentMessageCount >= this.maxMessages) {
                str.get_string('max_messages_reached', 'mod_aipersonchat').done(function(string) {
                    notification.alert('', string);
                });
                return;
            }
            
            // Disable input during processing
            input.prop('disabled', true);
            $('#aipersonchat-send').prop('disabled', true);
            
            // Add user message to chat immediately
            this.addMessageToChat(message, 'user');
            
            // Show loading indicator for AI response
            var loadingId = this.addLoadingMessage();
            
            // Clear input
            input.val('');
            
            ajax.call([{
                methodname: 'mod_aipersonchat_send_message',
                args: { 
                    cmid: this.cmid, 
                    message: message 
                }
            }])[0].done(function(response) {
                // Remove loading indicator
                self.removeLoadingMessage(loadingId);
                
                if (response.success) {
                    self.addMessageToChat(response.response, 'ai');
                    self.currentMessageCount++;
                    self.updateMessageCounter();
                } else {
                    self.addErrorMessage(response.error);
                }
            }).fail(function(error) {
                self.removeLoadingMessage(loadingId);
                self.addErrorMessage(error.message || 'An error occurred');
            }).always(function() {
                // Re-enable input
                input.prop('disabled', false);
                $('#aipersonchat-send').prop('disabled', false);
                input.focus();
            });
        },
        
        /**
         * Display messages in the chat interface.
         * @param {Array} messages Array of message objects
         */
        displayMessages: function(messages) {
            var container = $('#aipersonchat-messages');
            container.empty();
            
            for (var i = 0; i < messages.length; i++) {
                var msg = messages[i];
                this.addMessageToChat(msg.message, 'user', false);
                if (msg.hasresponse) {
                    this.addMessageToChat(msg.response, 'ai', false);
                }
            }
            
            this.scrollToBottom();
        },
        
        /**
         * Add a message to the chat interface.
         * @param {string} message Message content
         * @param {string} type Message type ('user' or 'ai')
         * @param {boolean} scroll Whether to scroll to bottom
         */
        addMessageToChat: function(message, type, scroll) {
            if (scroll === undefined) {
                scroll = true;
            }
            
            var container = $('#aipersonchat-messages');
            var messageClass = type === 'user' ? 'aipersonchat-message-user' : 'aipersonchat-message-ai';
            var sender = type === 'user' ? 'You' : this.personName;
            
            var messageHtml = '<div class="aipersonchat-message ' + messageClass + '">' +
                '<div class="aipersonchat-message-sender">' + sender + '</div>' +
                '<div class="aipersonchat-message-content">' + this.escapeHtml(message) + '</div>' +
                '<div class="aipersonchat-message-time">' + this.formatTime(new Date()) + '</div>' +
                '</div>';
            
            container.append(messageHtml);
            
            if (scroll) {
                this.scrollToBottom();
            }
        },
        
        /**
         * Add a loading message while waiting for AI response.
         * @return {string} Loading message ID
         */
        addLoadingMessage: function() {
            var container = $('#aipersonchat-messages');
            var loadingId = 'loading-' + Date.now();
            
            var loadingHtml = '<div class="aipersonchat-message aipersonchat-message-ai aipersonchat-loading" id="' + loadingId + '">' +
                '<div class="aipersonchat-message-sender">' + this.personName + '</div>' +
                '<div class="aipersonchat-message-content">' +
                '<span class="aipersonchat-typing-indicator">' +
                '<span></span><span></span><span></span>' +
                '</span>' +
                '</div>' +
                '</div>';
            
            container.append(loadingHtml);
            this.scrollToBottom();
            
            return loadingId;
        },
        
        /**
         * Remove loading message.
         * @param {string} loadingId Loading message ID
         */
        removeLoadingMessage: function(loadingId) {
            $('#' + loadingId).remove();
        },
        
        /**
         * Add error message to chat.
         * @param {string} error Error message
         */
        addErrorMessage: function(error) {
            var container = $('#aipersonchat-messages');
            
            var errorHtml = '<div class="aipersonchat-message aipersonchat-message-error">' +
                '<div class="aipersonchat-message-content">' +
                '<i class="fa fa-exclamation-triangle"></i> ' + this.escapeHtml(error) +
                '</div>' +
                '</div>';
            
            container.append(errorHtml);
            this.scrollToBottom();
        },
        
        /**
         * Update message counter display.
         */
        updateMessageCounter: function() {
            var counterText = this.currentMessageCount + ' / ' + this.maxMessages + ' messages used';
            $('.aipersonchat-message-counter').text(counterText);
            
            // Add counter if it doesn't exist
            if ($('.aipersonchat-message-counter').length === 0) {
                $('.aipersonchat-input-container').before(
                    '<div class="aipersonchat-message-counter">' + counterText + '</div>'
                );
            }
        },
        
        /**
         * Scroll chat to bottom.
         */
        scrollToBottom: function() {
            var container = $('#aipersonchat-messages');
            container.scrollTop(container[0].scrollHeight);
        },
        
        /**
         * Format time for display.
         * @param {Date} date Date object
         * @return {string} Formatted time
         */
        formatTime: function(date) {
            return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        },
        
        /**
         * Escape HTML to prevent XSS.
         * @param {string} text Text to escape
         * @return {string} Escaped text
         */
        escapeHtml: function(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };
    
    return Chat;
});
