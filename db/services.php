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
 * Web service definitions for AI Person Chat module.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_aipersonchat_send_message' => [
        'classname'   => 'mod_aipersonchat\external\send_message',
        'description' => 'Send a message to the AI person',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'mod/aipersonchat:chat',
    ],

    'mod_aipersonchat_get_messages' => [
        'classname'   => 'mod_aipersonchat\external\get_messages',
        'description' => 'Get chat messages for a user',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'mod/aipersonchat:view',
    ],
];

$services = [
    'AI Person Chat Services' => [
        'functions' => ['mod_aipersonchat_send_message', 'mod_aipersonchat_get_messages'],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];
