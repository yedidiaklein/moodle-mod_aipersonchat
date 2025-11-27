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
 * AI Person Chat activity module version information.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'mod_aipersonchat';       // Full name of the plugin (used for diagnostics).
$plugin->version   = 2025091801;         // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2024100700;         // Requires Moodle 4.5 or later.
$plugin->maturity  = MATURITY_ALPHA;     // This is an alpha version.
$plugin->release   = '1.0.0';            // This is the plugin release version.
