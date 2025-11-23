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
 * Edit materiel page
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/local/materiel/lib.php');

require_login();

if (!local_materiel_user_has_access()) {
    throw new moodle_exception('nopermissions', 'error');
}

$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/local/materiel/edit_materiel.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_materiel'));
$PAGE->set_heading(get_string('pluginname', 'local_materiel'));

$returnurl = new moodle_url('/local/materiel/index.php');

// Load materiel if editing.
$materiel = new \local_materiel\materiel($id);

// Create form.
$mform = new \local_materiel\form\materiel_form();

// Set form data.
if ($id) {
    // Get current user from logs if material is in use.
    $currentuserid = null;
    if ($materiel->status === \local_materiel\materiel::STATUS_IN_USE) {
        $currentuserid = \local_materiel\materiel_log::get_current_user($materiel->id);
    }

    $formdata = [
        'id' => $materiel->id,
        'typeid' => $materiel->typeid,
        'identifier' => $materiel->identifier,
        'name' => $materiel->name,
        'status' => $materiel->status,
        'userid' => $currentuserid,
        'notes' => $materiel->notes,
    ];
    $mform->set_data($formdata);
}

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    $oldstatus = $materiel->status;
    $olduserid = $id ? \local_materiel\materiel_log::get_current_user($materiel->id) : null;

    $materiel->typeid = $data->typeid;
    $materiel->identifier = $data->identifier;
    $materiel->name = $data->name;
    $materiel->status = $data->status;
    $materiel->notes = $data->notes;

    if ($materiel->save()) {
        // Handle user assignment changes via logs.
        if ($data->status === \local_materiel\materiel::STATUS_IN_USE) {
            $newuserid = $data->userid ?? null;
            // Only create a log if the user has changed or if this is a new checkout.
            if ($newuserid && ($oldstatus !== \local_materiel\materiel::STATUS_IN_USE || $olduserid != $newuserid)) {
                \local_materiel\materiel_log::create_checkout($materiel->id, $newuserid, '');
            }
        } else if ($oldstatus === \local_materiel\materiel::STATUS_IN_USE && $data->status !== \local_materiel\materiel::STATUS_IN_USE) {
            // Status changed from in_use to something else - create checkin log.
            \local_materiel\materiel_log::create_checkin($materiel->id, '');
        }

        redirect($returnurl, get_string('materiel_saved', 'local_materiel'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect($returnurl, get_string('error_saving', 'local_materiel'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($id ? get_string('edit_materiel', 'local_materiel') : get_string('add_materiel', 'local_materiel'));
$mform->display();
echo $OUTPUT->footer();
