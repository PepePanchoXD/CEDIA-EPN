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
 * Version metadata for the repository_pluginname plugin.
 *
 * @package   repository_pluginname
 * @copyright 2025, author_fullname <author_link>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


function xmldb_local_test_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025082802) {

        // Define field userid to be added to local_test.
        $table = new xmldb_table('local_test');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1', 'timecreated');

        // Conditionally launch add field userid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define key greetings-user-foreign-key (foreign) to be added to local_test.
        $table = new xmldb_table('local_test');
        $key = new xmldb_key('greetings-user-foreign-key', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Launch add key greetings-user-foreign-key.
        $dbman->add_key($table, $key);

        // Test savepoint reached.
        upgrade_plugin_savepoint(true, 2025082802, 'local', 'test');

    }

    if ($oldversion < 2025082803) {
        // (Opcional) Aquí puedes agregar cambios para la versión 2025082803.
        upgrade_plugin_savepoint(true, 2025082803, 'local', 'test');
    }

    if ($oldversion < 2025082804) {
        // (Opcional) Aquí puedes agregar cambios para la versión 2025082804.
        upgrade_plugin_savepoint(true, 2025082804, 'local', 'test');
    }
}