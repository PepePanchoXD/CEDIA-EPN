<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin functions for the repository_pluginname plugin.
 *
 * @package   repository_pluginname
 * @copyright 2025, author_fullname <author_link>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function local_test_get_greeting($user) {
    if ($user == null || !is_object($user)) {
        return get_string('greetinguser', 'local_test');
    }
    
    $country = $user->country;
    $username = fullname($user);
    
    // Si no tiene país configurado, usar greeting genérico
    if (empty($country)) {
        return get_string('greetingloggedinuser', 'local_test', $username);
    }
    
    switch ($country) {
        case 'ES': // España
            $langstr = 'greetinguseres';
            break;
        case 'EC': // Ecuador
            $langstr = 'greetinguserec';
            break;
        case 'AU': // Australia
            $langstr = 'greetinguserau';
            break;
        case 'FJ': // Fiji
            $langstr = 'greetinguserfj';
            break;
        case 'NZ': // Nueva Zelanda
            $langstr = 'greetingusernz';
            break;
        default:
            $langstr = 'greetingloggedinuser';
            break;
    }
    
    return get_string($langstr, 'local_test', fullname($user));
}

function local_test_extend_navigation_frontpage(navigation_node $frontpage) {
    if (isloggedin() && !isguestuser()) {
        $frontpage->add(
            get_string('pluginname', 'local_test'),
            new moodle_url('/local/test/index.php'),  // ← CORREGIDO: moodle_url en lugar de _module_url
            navigation_node::TYPE_CUSTOM
        );
    }
}