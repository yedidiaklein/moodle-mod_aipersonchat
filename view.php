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
 * Main view page for AI Person Chat activity.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course module ID.
$a = optional_param('a', 0, PARAM_INT);   // AI Chat instance ID.

if ($id) {
    $cm = get_coursemodule_from_id('aipersonchat', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $aiperchat = $DB->get_record('aipersonchat', ['id' => $cm->instance], '*', MUST_EXIST);
} else if ($a) {
    $aiperchat = $DB->get_record('aipersonchat', ['id' => $a], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $aiperchat->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('aipersonchat', $aiperchat->id, $course->id, false, MUST_EXIST);
} else {
    throw new moodle_exception('missingparameter');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

// Trigger module viewed event.
$event = \mod_aipersonchat\event\course_module_viewed::create([
    'objectid' => $aiperchat->id,
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('aipersonchat', $aiperchat);
$event->trigger();

// Set up the page.
$PAGE->set_url('/mod/aipersonchat/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($aiperchat->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Include JavaScript for chat functionality.
$PAGE->requires->js_call_amd('mod_aipersonchat/chat', 'init', [$cm->id, $aiperchat->id]);

echo $OUTPUT->header();

// Show activity name and description.
echo $OUTPUT->heading(format_string($aiperchat->name));

if (trim(strip_tags($aiperchat->intro))) {
    echo $OUTPUT->box_start('mod_introbox');
    echo format_module_intro('aipersonchat', $aiperchat, $cm->id);
    echo $OUTPUT->box_end();
}

// Display person information.
echo $OUTPUT->box_start('generalbox aipersonchat-person-info');
echo html_writer::tag('h3', get_string('chatting_with', 'mod_aipersonchat', $aiperchat->personname));

// Display person image if available.
if (!empty($aiperchat->imageurl)) {
    // Check if the URL points to an image.
    $imageurl = $aiperchat->imageurl;
    $isimage = preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', parse_url($imageurl, PHP_URL_PATH));

    if ($isimage) {
        echo html_writer::img($imageurl, $aiperchat->personname, ['class' => 'aipersonchat-person-image']);
    } else {
        echo html_writer::tag('p', get_string('invalidimageurl', 'mod_aipersonchat'));
    }
}

// Display person information.
if (!empty($aiperchat->personera)) {
    echo html_writer::tag('p', get_string('era', 'mod_aipersonchat') . ': ' . format_text($aiperchat->personera));
}

if (!empty($aiperchat->personurl)) {
    $personurl = $aiperchat->personurl;
    $isimage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', parse_url($personurl, PHP_URL_PATH));
    if ($isimage) {
        echo html_writer::tag('p', get_string('learnmore', 'mod_aipersonchat') . ': ');
        echo html_writer::img($personurl, $aiperchat->personname, [
            'class' => 'aipersonchat-person-image',
            'style' => 'max-width:250px;',
        ]);
    } else {
        echo html_writer::tag('p',
            get_string('learnmore', 'mod_aipersonchat') . ': ' .
            html_writer::link($personurl, $personurl, ['target' => '_blank'])
        );
    }
}

echo $OUTPUT->box_end();

// Chat interface.
echo $OUTPUT->box_start('generalbox aipersonchat-chat-container');
echo html_writer::tag('h3', get_string('chat', 'mod_aipersonchat'));

// Chat messages container.
echo html_writer::div('', 'aipersonchat-messages', ['id' => 'aipersonchat-messages']);

// Chat input form.
echo html_writer::start_div('aipersonchat-input-container');
echo html_writer::start_tag('form', ['id' => 'aipersonchat-form']);
echo html_writer::tag('input', '', [
    'type' => 'text',
    'id' => 'aipersonchat-input',
    'placeholder' => get_string('typemessage', 'mod_aipersonchat'),
    'maxlength' => 500,
    'class' => 'form-control',
]);
echo html_writer::tag('button', get_string('send', 'mod_aipersonchat'), [
    'type' => 'submit',
    'id' => 'aipersonchat-send',
    'class' => 'btn btn-primary',
]);
echo html_writer::end_tag('form');
echo html_writer::end_div();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
