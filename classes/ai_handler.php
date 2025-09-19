<?php
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
 * AI communication handler for the AI Person Chat module.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_aipersonchat;

/**
 * Class responsible for communicating with the AI subsystem.
 */
class ai_handler {

    /**
     * Send message to AI and get response.
     *
     * @param int $contextid The context ID
     * @param string $message The user message
     * @param \stdClass $aipersonchat The AI person chat instance
     * @param array $chathistory Previous chat messages for context
     * @return array The AI response data
     */
    public static function send_message(int $contextid, string $message, \stdClass $aipersonchat, array $chathistory = []): array {
        global $USER;

        // Build conversation history for context.
        $history = '';
        foreach ($chathistory as $historyitem) {
            $history .= "Student: " . $historyitem->message . "\n";
            if (!empty($historyitem->response)) {
                $history .= $aipersonchat->personname . ": " . $historyitem->response . "\n";
            }
        }

        // Create the prompt with context.
        $promptdata = (object) [
            'url' => $aipersonchat->personurl,
            'personname' => $aipersonchat->personname,
            'era' => $aipersonchat->personera ?? 'historical times',
            'history' => $history,
            'message' => $message,
        ];

        $systemprompt = get_string('ai_system_prompt', 'mod_aipersonchat', $aipersonchat);
        $contextprompt = get_string('ai_context_prompt', 'mod_aipersonchat', $promptdata);

        $fullprompt = $systemprompt . "\n\n" . $contextprompt;

        // Check if topic restriction is enabled.
        if ($aipersonchat->restricttopic) {
            $topiccheck = self::check_topic_relevance($message, $aipersonchat);
            if (!$topiccheck['relevant']) {
                return [
                    'success' => true,
                    'generatedcontent' => get_string('ai_topic_restriction', 'mod_aipersonchat', $aipersonchat),
                    'finishreason' => 'topic_restriction',
                    'errorcode' => null,
                    'error' => null,
                    'timecreated' => time(),
                    'prompttext' => $fullprompt,
                ];
            }
        }

        // Context validation and permission check.
        $context = \core\context::instance_by_id($contextid);

        // Prepare the AI action.
        $action = new \core_ai\aiactions\generate_text(
            contextid: $contextid,
            userid: $USER->id,
            prompttext: $fullprompt,
        );

        // Send the action to the AI manager.
        $manager = \core\di::get(\core_ai\manager::class);
        $response = $manager->process_action($action);

        // Return the response.
        return [
            'success' => $response->get_success(),
            'generatedcontent' => $response->get_response_data()['generatedcontent'] ?? '',
            'finishreason' => $response->get_response_data()['finishreason'] ?? '',
            'errorcode' => $response->get_errorcode(),
            'error' => $response->get_errormessage(),
            'timecreated' => $response->get_timecreated(),
            'prompttext' => $fullprompt,
        ];
    }

    /**
     * Check if a message is relevant to the person's topic.
     *
     * @param string $message The message to check
     * @param \stdClass $aipersonchat The AI person chat instance
     * @return array Result with relevance check
     */
    private static function check_topic_relevance(string $message, \stdClass $aipersonchat): array {
        // Simple keyword-based check - in a real implementation, this could use AI
        // to determine topic relevance more sophisticatedly.
        $messagelower = strtolower($message);

        // Check for obviously off-topic content.
        $offtopickeywords = [
            'weather today', 'current events', 'modern technology', 'internet',
            'smartphone', 'computer', 'covid', 'today\'s news', 'youtube',
            'facebook', 'twitter', 'instagram', 'tiktok', 'moodle',
        ];

        foreach ($offtopickeywords as $keyword) {
            if (strpos($messagelower, $keyword) !== false) {
                return ['relevant' => false, 'reason' => 'modern_topic'];
            }
        }

        // For now, assume most messages are relevant unless obviously off-topic.
        return ['relevant' => true, 'reason' => 'acceptable'];
    }

    /**
     * Fetch information from the provided URL to enhance AI context.
     *
     * @param string $url The URL to fetch information from
     * @return string Extracted text content
     */
    public static function fetch_url_content(string $url): string {
        // This is a simplified implementation - in production you might want
        // to use more sophisticated content extraction or caching.
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Moodle AI Chat Module',
                ],
            ]);

            $content = file_get_contents($url, false, $context);
            if ($content === false) {
                return '';
            }

            // Basic HTML stripping - in production you might want more sophisticated parsing.
            $text = strip_tags($content);
            $text = preg_replace('/\s+/', ' ', $text);

            // Limit content length to avoid overwhelming the AI.
            return substr(trim($text), 0, 5000);
        } catch (\Exception $e) {
            return '';
        }
    }
}
