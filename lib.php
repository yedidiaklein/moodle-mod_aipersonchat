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
 * Library of interface functions and constants.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Library of interface functions for mod_aipersonchat
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return whether the given feature is supported by this module.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool True if feature is supported, false otherwise
 */
function aipersonchat_supports($feature) {
    return match ($feature) {
        FEATURE_IDNUMBER => true,
        FEATURE_GROUPS => false,
        FEATURE_GROUPINGS => false,
        FEATURE_MOD_INTRO => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_GRADE_HAS_GRADE => false,
        FEATURE_GRADE_OUTCOMES => false,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_ADVANCED_GRADING => false,
        FEATURE_PLAGIARISM => false,
        FEATURE_COMMENT => false,
        default => null,
    };
}

/**
 * Saves a new instance of the aipersonchat into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $data
 * @param mod_aipersonchat_mod_form $form
 * @return int The id of the newly inserted aipersonchat record
 */
function aipersonchat_add_instance(stdClass $data, ?mod_aipersonchat_mod_form $form = null) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;

    // Force introformat to FORMAT_HTML (1) if not set or is null.
    if (!isset($data->introformat) || $data->introformat === null) {
        $data->introformat = FORMAT_HTML;
    }
    $data->introformat = (int)($data->introformat ?? FORMAT_HTML);

    $id = $DB->insert_record('aipersonchat', $data);

    // Handle standard editor for intro field.
    if ($form) {
        $data->id = $id;

        $data = file_postupdate_standard_editor($data, 'intro',
            aipersonchat_get_editor_options(), $form->get_context(),
            'mod_aipersonchat', 'intro', 0);

        // Ensure intro and introformat are valid before update.
        if (!isset($data->intro) || $data->intro === null) {
            $data->intro = '';
        }
        if (!isset($data->introformat) || $data->introformat === null) {
            $data->introformat = FORMAT_HTML;
        }
        $data->introformat = (int)($data->introformat ?? FORMAT_HTML);

        // Update the record with any changes from file processing.
        $DB->update_record('aipersonchat', $data);
    }

    return $id;
}

/**
 * Updates an instance of the aipersonchat in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $data
 * @param mod_aipersonchat_mod_form $form
 * @return boolean Success/Fail
 */
function aipersonchat_update_instance(stdClass $data, ?mod_aipersonchat_mod_form $form = null) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    // Ensure intro and introformat have default values if not set.
    if (!isset($data->intro)) {
        $data->intro = '';
    }
    if (!isset($data->introformat) || $data->introformat === null) {
        $data->introformat = FORMAT_HTML;
    }
    $data->introformat = (int)($data->introformat ?? FORMAT_HTML);

    // Handle file manager for intro only.
    if ($form) {
        $data = file_postupdate_standard_editor($data, 'intro',
            aipersonchat_get_editor_options(), $form->get_context(),
            'mod_aipersonchat', 'intro', $data->id);

        // Ensure introformat is still valid after editor processing.
        if (!isset($data->introformat) || $data->introformat === null) {
            $data->introformat = FORMAT_HTML;
        }
        $data->introformat = (int)($data->introformat ?? FORMAT_HTML);
    }

    return $DB->update_record('aipersonchat', $data);
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every aipersonchat event in the site is checked, else
 * only aipersonchat events belonging to the course specified are checked.
 * This is only required if the module is generating calendar events.
 *
 * @param int $courseid Course ID
 * @return bool
 */
function aipersonchat_refresh_events($courseid = 0) {
    global $DB;

    if ($courseid == 0) {
        if (!$aiperchats = $DB->get_records('aipersonchat')) {
            return true;
        }
    } else {
        if (!$aiperchats = $DB->get_records('aipersonchat', ['course' => $courseid])) {
            return true;
        }
    }

    foreach ($aiperchats as $aiperchat) {
        // This can be implemented in the future for calendar event updates.
        // For now, just continue to the next iteration.
        continue;
    }

    return true;
}

/**
 * Removes an instance of the aipersonchat from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function aipersonchat_delete_instance($id) {
    global $DB;

    if (!$aiperchat = $DB->get_record('aipersonchat', ['id' => $id])) {
        return false;
    }

    // Delete any dependent records here.
    $DB->delete_records('aipersonchat_messages', ['aipersonchatid' => $aiperchat->id]);

    // Delete the instance itself.
    $result = $DB->delete_record('aipersonchat', ['id' => $aiperchat->id]);

    return $result;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in aipersonchat activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function aipersonchat_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link aipersonchat_print_recent_mod_activity()}.
 *
 * Please see {@link get_recent_mod_activity()} for the expected format of
 * the data structure.
 *
 * @param array $activities sequentially indexed array of objects with added 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 */
function aipersonchat_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0) {
}

/**
 * Prints single activity item prepared by {@link aipersonchat_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function aipersonchat_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function aipersonchat_cron() {
    return true;
}

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function aipersonchat_get_coursemodule_info($coursemodule) {
    global $DB;

    $dbparams = ['id' => $coursemodule->instance];
    if (!$aiperchat = $DB->get_record('aipersonchat', $dbparams)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $aiperchat->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $result->content = format_module_intro('aipersonchat', $aiperchat, $coursemodule->id, false);
    }

    return $result;
}

/**
 * Serves the files from the aipersonchat file areas
 *
 * @package mod_aipersonchat
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the aipersonchat's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function aipersonchat_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options = []) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    if (!has_capability('mod/aipersonchat:view', $context)) {
        send_file_not_found();
    }

    if ($filearea !== 'intro' && $filearea !== 'personimage') {
        send_file_not_found();
    }

    $fs = get_file_storage();

    if ($filearea === 'intro') {
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_aipersonchat/$filearea/0/$relativepath";
        $file = $fs->get_file_by_hash(sha1($fullpath));
    } else if ($filearea === 'personimage') {
        $itemid = array_shift($args);
        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';

        $file = $fs->get_file($context->id, 'mod_aipersonchat', $filearea, 0, $filepath, $filename);
    }

    if (!$file || $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, null, 0, $forcedownload, $options);
}

/**
 * Get editor options for intro field
 *
 * @return array
 */
function aipersonchat_get_editor_options() {
    global $CFG;
    return [
        'subdirs' => 1,
        'maxbytes' => $CFG->maxbytes,
        'maxfiles' => -1,
        'changeformat' => 1,
        'context' => null,
        'noclean' => 1,
        'trusttext' => 0,
    ];
}
