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
 * External web service for retrieving chat messages.
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
use external_multiple_structure;
use external_single_structure;
use external_value;

/**
 * External function for getting chat messages.
 */
class get_messages extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module ID'),
            'since' => new external_value(PARAM_INT, 'Get messages since this timestamp', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Get chat messages for the current user.
     *
     * @param int $cmid Course module ID
     * @param int $since Timestamp to get messages since
     * @return array Messages data
     */
    public static function execute(int $cmid, int $since = 0): array {
        global $DB, $USER;

        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'since' => $since,
        ]);

        // Get course module and check permissions.
        $cm = get_coursemodule_from_id('aipersonchat', $params['cmid'], 0, false, MUST_EXIST);
        $aiperchat = $DB->get_record('aipersonchat', ['id' => $cm->instance], '*', MUST_EXIST);
        $context = context_module::instance($cm->id);

        // Require login and check capabilities.
        require_login($cm->course, false, $cm);
        require_capability('mod/aipersonchat:view', $context);

        // Build query conditions.
        $conditions = [
            'aipersonchatid' => $aiperchat->id,
            'userid' => $USER->id,
        ];

        if ($params['since'] > 0) {
            $conditions['timecreated'] = ['>', $params['since']];
        }

        // Get messages.
        $messages = $DB->get_records('aipersonchat_messages', $conditions, 'timecreated ASC');

        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'id' => $message->id,
                'message' => $message->message,
                'response' => $message->response ?? '',
                'timecreated' => $message->timecreated,
                'timeresponse' => $message->timeresponse ?? 0,
                'hasresponse' => !empty($message->response),
            ];
        }

        return [
            'messages' => $result,
            'personname' => $aiperchat->personname,
            'maxmessages' => $aiperchat->maxmessages,
            'messagecount' => $DB->count_records('aipersonchat_messages', [
                'aipersonchatid' => $aiperchat->id,
                'userid' => $USER->id,
            ]),
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'messages' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Message ID'),
                    'message' => new external_value(PARAM_RAW, 'User message'),
                    'response' => new external_value(PARAM_RAW, 'AI response'),
                    'timecreated' => new external_value(PARAM_INT, 'Message creation time'),
                    'timeresponse' => new external_value(PARAM_INT, 'Response time'),
                    'hasresponse' => new external_value(PARAM_BOOL, 'Whether message has response'),
                ])
            ),
            'personname' => new external_value(PARAM_TEXT, 'Historic person name'),
            'maxmessages' => new external_value(PARAM_INT, 'Maximum messages allowed'),
            'messagecount' => new external_value(PARAM_INT, 'Current message count for user'),
        ]);
    }
}
