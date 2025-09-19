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
 * External web service for sending messages to AI.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_aipersonchat\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/externallib.php');

use context_module;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use mod_aipersonchat\ai_handler;

/**
 * External function for sending chat messages.
 */
class send_message extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module ID'),
            'message' => new external_value(PARAM_RAW, 'Message to send'),
        ]);
    }

    /**
     * Send message to AI and return response.
     *
     * @param int $cmid Course module ID
     * @param string $message Message content
     * @return array Response data
     */
    public static function execute(int $cmid, string $message): array {
        global $DB, $USER;

        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'message' => $message,
        ]);

        // Get course module and check permissions.
        $cm = get_coursemodule_from_id('aipersonchat', $params['cmid'], 0, false, MUST_EXIST);
        $aiperchat = $DB->get_record('aipersonchat', ['id' => $cm->instance], '*', MUST_EXIST);
        $context = context_module::instance($cm->id);

        // Require login and check capabilities.
        require_login($cm->course, false, $cm);
        require_capability('mod/aipersonchat:chat', $context);

        // Validate message length.
        if (strlen($params['message']) > 500) {
            throw new \invalid_parameter_exception(get_string('message_too_long', 'mod_aipersonchat', 500));
        }

        if (empty(trim($params['message']))) {
            throw new \invalid_parameter_exception('Message cannot be empty');
        }

        // Check message limit.
        $messagecount = $DB->count_records('aipersonchat_messages', [
            'aipersonchatid' => $aiperchat->id,
            'userid' => $USER->id,
        ]);

        if ($messagecount >= $aiperchat->maxmessages) {
            throw new \moodle_exception('max_messages_reached', 'mod_aipersonchat');
        }

        // Get chat history for context.
        $chathistory = $DB->get_records('aipersonchat_messages', [
            'aipersonchatid' => $aiperchat->id,
            'userid' => $USER->id,
        ], 'timecreated ASC', '*', 0, 10); // Last 10 messages for context.

        // Send message to AI.
        $airesponse = ai_handler::send_message($context->id, $params['message'], $aiperchat, $chathistory);

        $response = '';
        $success = false;
        $error = '';

        if ($airesponse['success'] && !empty($airesponse['generatedcontent'])) {
            $response = $airesponse['generatedcontent'];
            $success = true;
        } else {
            $error = $airesponse['error'] ?? 'Unknown error occurred';
        }

        // Save message to database.
        $messagerecord = new \stdClass();
        $messagerecord->aipersonchatid = $aiperchat->id;
        $messagerecord->userid = $USER->id;
        $messagerecord->message = $params['message'];
        $messagerecord->response = $response;
        $messagerecord->timecreated = time();
        $messagerecord->timeresponse = $success ? time() : null;

        $DB->insert_record('aipersonchat_messages', $messagerecord);

        // Trigger events.
        $eventparams = [
            'objectid' => $aiperchat->id,
            'context' => $context,
            'other' => [
                'message' => $params['message'],
                'success' => $success,
            ],
        ];

        $event = \mod_aipersonchat\event\message_sent::create($eventparams);
        $event->trigger();

        if ($success) {
            $responseevent = \mod_aipersonchat\event\response_received::create($eventparams);
            $responseevent->trigger();
        }

        return [
            'success' => $success,
            'response' => $response,
            'error' => $error,
            'timestamp' => time(),
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the request was successful'),
            'response' => new external_value(PARAM_RAW, 'AI response message'),
            'error' => new external_value(PARAM_TEXT, 'Error message if any'),
            'timestamp' => new external_value(PARAM_INT, 'Response timestamp'),
        ]);
    }
}
