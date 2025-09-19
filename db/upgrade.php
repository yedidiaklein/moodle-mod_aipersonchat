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
 * Upgrade script for AI Person Chat activity module.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to upgrade mod_aipersonchat
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_aipersonchat_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025091801) {

        // Define field imageurl to be added to aipersonchat.
        $table = new xmldb_table('aipersonchat');
        $field = new xmldb_field('imageurl', XMLDB_TYPE_TEXT, null, null, null, null, null, 'personurl');

        // Conditionally launch add field imageurl.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Aipersonchat savepoint reached.
        upgrade_mod_savepoint(true, 2025091801, 'aipersonchat');
    }

    return true;
}
