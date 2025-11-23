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
 * Checkin materiel page
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

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/local/materiel/checkin.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_materiel'));
$PAGE->set_heading(get_string('pluginname', 'local_materiel'));

$returnurl = new moodle_url('/local/materiel/index.php');

// Load materiel.
$materiel = new \local_materiel\materiel($id);

if (!$materiel->id) {
    throw new moodle_exception('materiel_not_found', 'local_materiel');
}

if ($materiel->status != \local_materiel\materiel::STATUS_IN_USE) {
    redirect($returnurl, get_string('materiel_not_in_use', 'local_materiel'), null, \core\output\notification::NOTIFY_ERROR);
}

if ($confirm && confirm_sesskey()) {
    // Update materiel status.
    $materiel->status = \local_materiel\materiel::STATUS_AVAILABLE;
    $materiel->save();

    // Create log entry.
    \local_materiel\materiel_log::create_checkin($materiel->id);

    redirect($returnurl, get_string('checkin_success', 'local_materiel'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('checkin', 'local_materiel') . ': ' . $materiel->name);

$confirmurl = new moodle_url('/local/materiel/checkin.php', ['id' => $id, 'confirm' => 1, 'sesskey' => sesskey()]);
echo $OUTPUT->confirm(
    get_string('checkin_confirm', 'local_materiel'),
    $confirmurl,
    $returnurl
);

echo $OUTPUT->footer();
