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

require('../../config.php');
require_once($CFG->dirroot . '/local/test/lib.php');
require_once($CFG->dirroot . '/local/test/classes/form/message_form.php');

$context = context_system::instance();

// Uso de PAGE para cargar el plugin.
$PAGE->set_context($context);
$PAGE->set_url('/local/test/index.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_test'));
$PAGE->set_heading(get_string('pluginname', 'local_test'));

// Cargar los elementos de la página.
echo $OUTPUT->header();


if (isloggedin()) {
    $usergreeting = local_test_get_greeting($USER);
} else {
    $usergreeting = get_string('greetinguser', 'local_test');
}

$allowpost = has_capability('local/test:postmessages', $context);
$allowview = has_capability('local/test:viewmessages', $context);
$allowdelete = has_capability('local/test:deletemessages', $context);

// Mostrar mensaje usando plantilla Mustache
$templatedata = [
    'usergreeting' => $usergreeting
];
echo $OUTPUT->render_from_template('local_test/greeting_message', $templatedata);


// Procesar eliminación de mensaje si corresponde.
if ($allowdelete && !empty($_POST['deleteid'])) {
    $deleteid = (int)$_POST['deleteid'];
    // Solo managers pueden eliminar cualquier mensaje.
    $DB->delete_records('local_test', ['id' => $deleteid]);
    // Refrescar para evitar reenvío accidental.
    redirect(new moodle_url('/local/test/index.php'));
}

$userfields = \core_user\fields::for_name()->with_identity($context);
$userfieldssql = $userfields->get_sql('u');

$sql = "SELECT m.id, m.message, m.timecreated, m.userid, u.firstname, u.lastname
    FROM {local_test} m
    LEFT JOIN {user} u ON u.id = m.userid
    ORDER BY m.timecreated DESC";
$messages = $DB->get_records_sql($sql);




// Mostrar el formulario siempre, pero solo permitir guardar si tiene permiso.
$mform = new \local_test\form\message_form();
if ($mform->is_cancelled()) {
    // Si el usuario cancela, redirigir o mostrar mensaje.
} else if ($data = $mform->get_data()) {
    // Procesar datos enviados.
    $message = required_param('inputtext', PARAM_TEXT);

    if ($allowpost) {
        if (!empty($message)) {
            $record = new stdClass();
            $record->message = $message;
            $record->timecreated = time();
            $record->userid = $USER->id;
            // Guardar el mensaje en la base de datos.
            $DB->insert_record('local_test', $record);
        }
    } else {
        echo $OUTPUT->notification('No puede publicar aquí', 'error');
    }
    // Mostrar el formulario nuevamente después de guardar o error.
    $mform = new \local_test\form\message_form();
    $mform->display();
} else {
    $mform->display();
}


// Plantilla para mostrar los mensajes guardados.

$messagesarr = [];
foreach ($messages as $msg) {
    $messagesarr[] = [
        'id' => $msg->id,
        'message' => $msg->message,
        'timecreated' => $msg->timecreated,
        'fullname' => $msg->firstname . ' ' . $msg->lastname,
        'candeleteany' => $allowdelete
    ];
}
$templatedata = [
    'messages' => $messagesarr,
    'config' => [ 'wwwroot' => $CFG->wwwroot ]
];
echo $OUTPUT->render_from_template('local_test/messages', $templatedata);


echo $OUTPUT->footer();


