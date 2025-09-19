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
 * Privacy subsystem implementation for mod_aipersonchat.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_aipersonchat\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\helper;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy subsystem implementation for mod_aipersonchat.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta-data about this system.
     *
     * @param collection $collection The collection to add metadata to.
     * @return collection The collection of metadata.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'aipersonchat_messages',
            [
                'userid' => 'privacy:metadata:aipersonchat_messages:userid',
                'message' => 'privacy:metadata:aipersonchat_messages:message',
                'response' => 'privacy:metadata:aipersonchat_messages:response',
                'timecreated' => 'privacy:metadata:aipersonchat_messages:timecreated',
                'timeresponse' => 'privacy:metadata:aipersonchat_messages:timeresponse',
            ],
            'privacy:metadata:aipersonchat_messages'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {aipersonchat} a ON a.id = cm.instance
            INNER JOIN {aipersonchat_messages} am ON am.aipersonchatid = a.id
                 WHERE am.userid = :userid";

        $params = [
            'modname' => 'aipersonchat',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $params = [
            'cmid' => $context->instanceid,
            'modname' => 'aipersonchat',
        ];

        $sql = "SELECT am.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {aipersonchat} a ON a.id = cm.instance
                  JOIN {aipersonchat_messages} am ON am.aipersonchatid = a.id
                 WHERE cm.id = :cmid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT cm.id AS cmid,
                       am.id,
                       am.message,
                       am.response,
                       am.timecreated,
                       am.timeresponse
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid
            INNER JOIN {aipersonchat} a ON a.id = cm.instance
            INNER JOIN {aipersonchat_messages} am ON am.aipersonchatid = a.id
                 WHERE c.id {$contextsql}
                       AND am.userid = :userid
              ORDER BY cm.id, am.timecreated";

        $params = ['userid' => $user->id] + $contextparams;

        $recordset = $DB->get_recordset_sql($sql, $params);
        self::export_user_data_from_recordset($recordset, $contextlist);
        $recordset->close();
    }

    /**
     * Export user data from recordset.
     *
     * @param \moodle_recordset $recordset The recordset to export.
     * @param approved_contextlist $contextlist The approved contexts.
     */
    protected static function export_user_data_from_recordset(\moodle_recordset $recordset, approved_contextlist $contextlist) {
        $currentcmid = null;
        $messages = [];

        foreach ($recordset as $record) {
            if ($currentcmid && $record->cmid != $currentcmid) {
                $context = \context_module::instance($currentcmid);
                self::export_messages($context, $messages);
                $messages = [];
            }

            $messages[] = [
                'message' => $record->message,
                'response' => $record->response,
                'timecreated' => \core_privacy\local\request\transform::datetime($record->timecreated),
                'timeresponse' => $record->timeresponse ?
                    \core_privacy\local\request\transform::datetime($record->timeresponse) : null,
            ];

            $currentcmid = $record->cmid;
        }

        if (!empty($messages)) {
            $context = \context_module::instance($currentcmid);
            self::export_messages($context, $messages);
        }
    }

    /**
     * Export messages for a context.
     *
     * @param \context $context The context.
     * @param array $messages The messages to export.
     */
    protected static function export_messages(\context $context, array $messages) {
        $data = (object) [
            'messages' => $messages,
        ];
        writer::with_context($context)->export_data([], $data);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        if ($cm = get_coursemodule_from_id('aipersonchat', $context->instanceid)) {
            $DB->delete_records('aipersonchat_messages', ['aipersonchatid' => $cm->instance]);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }

            $cm = get_coursemodule_from_id('aipersonchat', $context->instanceid);
            if (!$cm) {
                continue;
            }

            $DB->delete_records('aipersonchat_messages', [
                'aipersonchatid' => $cm->instance,
                'userid' => $userid,
            ]);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('aipersonchat', $context->instanceid);
        if (!$cm) {
            return;
        }

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $DB->delete_records_select(
            'aipersonchat_messages',
            "aipersonchatid = :aipersonchatid AND userid {$usersql}",
            ['aipersonchatid' => $cm->instance] + $userparams
        );
    }
}
