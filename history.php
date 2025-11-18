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
 * Materiel history page
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

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/local/materiel/history.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_materiel'));
$PAGE->set_heading(get_string('pluginname', 'local_materiel'));

// Load materiel.
$materiel = new \local_materiel\materiel($id);

if (!$materiel->id) {
    throw new moodle_exception('materiel_not_found', 'local_materiel');
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('history', 'local_materiel') . ': ' . $materiel->name);

// Get logs.
$logs = \local_materiel\materiel_log::get_by_materiel($materiel->id);

if (empty($logs)) {
    echo html_writer::tag('p', get_string('no_history', 'local_materiel'), ['class' => 'alert alert-info']);
} else {
    $table = new html_table();
    $table->head = [
        get_string('date', 'local_materiel'),
        get_string('action', 'local_materiel'),
        get_string('user', 'local_materiel'),
        get_string('action_by', 'local_materiel'),
        get_string('notes', 'local_materiel'),
    ];
    $table->attributes['class'] = 'generaltable';

    foreach ($logs as $log) {
        $user = '';
        if ($log->userid) {
            $userrecord = $DB->get_record('user', ['id' => $log->userid]);
            if ($userrecord) {
                $userpicture = $OUTPUT->user_picture($userrecord, ['size' => 35, 'class' => 'mr-2']);
                $user = $userpicture . ' ' . fullname($userrecord);
            }
        }

        $actionby = '';
        if ($log->actionby) {
            $actionbyrecord = $DB->get_record('user', ['id' => $log->actionby]);
            if ($actionbyrecord) {
                $actionbypicture = $OUTPUT->user_picture($actionbyrecord, ['size' => 35, 'class' => 'mr-2']);
                $actionby = $actionbypicture . ' ' . fullname($actionbyrecord);
            }
        }

        $row = [
            userdate($log->timecreated, get_string('strftimedatetime', 'langconfig')),
            get_string('action_' . $log->action, 'local_materiel'),
            $user,
            $actionby,
            $log->notes,
        ];

        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

echo html_writer::div(
    html_writer::link(new moodle_url('/local/materiel/index.php'), get_string('back_to_list', 'local_materiel')),
    'mt-3'
);

echo $OUTPUT->footer();
