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
 * Activity module configuration form for AI Person Chat.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Activity configuration form for AI Person Chat module.
 */
class mod_aipersonchat_mod_form extends moodleform_mod {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('aipersonchatname', 'mod_aipersonchat'), ['size' => '64']);
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Person configuration section.
        $mform->addElement('header', 'personconfig', get_string('personconfig', 'mod_aipersonchat'));

        // Person name.
        $mform->addElement('text', 'personname', get_string('personname', 'mod_aipersonchat'), ['size' => '64']);
        $mform->setType('personname', PARAM_TEXT);
        $mform->addRule('personname', null, 'required', null, 'client');
        $mform->addHelpButton('personname', 'personname', 'mod_aipersonchat');

        // Person biography/information URL (Wikipedia link).
        $mform->addElement('url', 'personurl', get_string('personurl', 'mod_aipersonchat'), ['size' => '100']);
        $mform->setType('personurl', PARAM_URL);
        $mform->addRule('personurl', null, 'required', null, 'client');
        $mform->addHelpButton('personurl', 'personurl', 'mod_aipersonchat');

        // Person image URL.
        $mform->addElement('text', 'imageurl', get_string('imageurl', 'mod_aipersonchat'), ['size' => '64']);
        $mform->setType('imageurl', PARAM_URL);
        $mform->addHelpButton('imageurl', 'imageurl', 'mod_aipersonchat');

        // Person time period/era.
        $mform->addElement('text', 'personera', get_string('personera', 'mod_aipersonchat'), ['size' => '64']);
        $mform->setType('personera', PARAM_TEXT);
        $mform->addHelpButton('personera', 'personera', 'mod_aipersonchat');

        // Chat behavior settings.
        $mform->addElement('header', 'chatbehavior', get_string('chatbehavior', 'mod_aipersonchat'));

        // Enable chat restriction to person topic only.
        $mform->addElement('advcheckbox', 'restricttopic', get_string('restricttopic', 'mod_aipersonchat'));
        $mform->setDefault('restricttopic', 1);
        $mform->addHelpButton('restricttopic', 'restricttopic', 'mod_aipersonchat');

        // Maximum messages per student.
        $mform->addElement('text', 'maxmessages', get_string('maxmessages', 'mod_aipersonchat'), ['size' => '10']);
        $mform->setType('maxmessages', PARAM_INT);
        $mform->setDefault('maxmessages', 50);
        $mform->addHelpButton('maxmessages', 'maxmessages', 'mod_aipersonchat');

        // Standard elements.
        $this->standard_coursemodule_elements();

        // Standard buttons.
        $this->add_action_buttons();
    }

    /**
     * Perform custom validation.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate person URL is a valid URL.
        if (!empty($data['personurl']) && !filter_var($data['personurl'], FILTER_VALIDATE_URL)) {
            $errors['personurl'] = get_string('invalidurl', 'mod_aipersonchat');
        }

        // Validate image URL is a valid URL.
        if (!empty($data['imageurl']) && !filter_var($data['imageurl'], FILTER_VALIDATE_URL)) {
            $errors['imageurl'] = get_string('invalidurl', 'mod_aipersonchat');
        }

        // Validate max messages is positive.
        if (!empty($data['maxmessages']) && $data['maxmessages'] < 1) {
            $errors['maxmessages'] = get_string('invalidmaxmessages', 'mod_aipersonchat');
        }

        return $errors;
    }
}
