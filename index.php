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
 * List of all AI Chat activities in course.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT); // Course ID.

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

require_login($course);
$PAGE->set_pagelayout('incourse');

$params = ['id' => $id];
$PAGE->set_url('/mod/aipersonchat/index.php', $params);
$PAGE->set_title(get_string('modulenameplural', 'mod_aipersonchat'));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('modulenameplural', 'mod_aipersonchat'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_aipersonchat'));

if (!$aipersonchats = get_all_instances_in_course('aipersonchat', $course)) {
    notice(get_string('noaipersonchats', 'mod_aipersonchat'), new moodle_url('/course/view.php', ['id' => $course->id]));
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_' . $course->format);
    $table->head = [$strsectionname, get_string('name'), get_string('personname', 'mod_aipersonchat')];
    $table->align = ['center', 'left', 'left'];
} else {
    $table->head = [get_string('name'), get_string('personname', 'mod_aipersonchat')];
    $table->align = ['left', 'left'];
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';

foreach ($aipersonchats as $aipersonchat) {
    $cm = $modinfo->cms[$aipersonchat->coursemodule];

    if (!$cm->uservisible) {
        continue;
    }

    $row = [];

    if ($usesections) {
        if ($aipersonchat->section !== $currentsection) {
            if ($aipersonchat->section) {
                $row[] = get_section_name($course, $aipersonchat->section);
            } else {
                $row[] = '';
            }
            $currentsection = $aipersonchat->section;
        } else {
            $row[] = '';
        }
    }

    $class = $aipersonchat->visible ? '' : 'class="dimmed"'; // Hidden modules are dimmed.
    $row[] = html_writer::link(
    new moodle_url('/mod/aipersonchat/view.php', ['id' => $cm->id]),
    format_string($aipersonchat->name),
        ['class' => $class]
    );

    $row[] = format_string($aipersonchat->personname);

    $table->data[] = $row;
}

echo html_writer::table($table);

echo $OUTPUT->footer();
