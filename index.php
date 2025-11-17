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
 * Main page for local_materiel
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/local/materiel/lib.php');

require_login();

// Check if user has access through cohort membership.
if (!local_materiel_user_has_access()) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('materiel', 'local_materiel'));
}

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/local/materiel/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_materiel'));
$PAGE->set_heading(get_string('pluginname', 'local_materiel'));
$PAGE->requires->js(new moodle_url('/local/materiel/js/materiel_table.js'));
$PAGE->requires->css(new moodle_url('/local/materiel/styles/materiel.css'));

// Get filter parameters.
$filtertype = optional_param('type', 0, PARAM_INT);

echo $OUTPUT->header();

// Action buttons.
echo html_writer::start_div('mb-3');
echo html_writer::link(
    new moodle_url('/local/materiel/edit_materiel.php'),
    get_string('add_materiel', 'local_materiel'),
    ['class' => 'btn btn-primary mr-2']
);
echo html_writer::link(
    new moodle_url('/local/materiel/edit_type.php'),
    get_string('add_type', 'local_materiel'),
    ['class' => 'btn btn-secondary mr-2']
);
echo html_writer::link(
    new moodle_url('/local/materiel/manage_types.php'),
    get_string('manage_types', 'local_materiel'),
    ['class' => 'btn btn-secondary']
);
echo html_writer::end_div();

// Filter buttons and search.
echo html_writer::start_div('mb-3');
echo html_writer::tag('label', get_string('filter_by_status', 'local_materiel') . ':', ['class' => 'mr-2']);
echo html_writer::tag('button', get_string('all_materiel', 'local_materiel'),
    ['class' => 'btn btn-sm btn-outline-primary mr-2 filter-btn active', 'data-filter' => 'all']);
echo html_writer::tag('button', get_string('materiel_available', 'local_materiel'),
    ['class' => 'btn btn-sm btn-outline-success mr-2 filter-btn', 'data-filter' => 'available']);
echo html_writer::tag('button', get_string('materiel_in_use', 'local_materiel'),
    ['class' => 'btn btn-sm btn-outline-warning mr-2 filter-btn', 'data-filter' => 'in_use']);
echo html_writer::tag('button', get_string('status_maintenance', 'local_materiel'),
    ['class' => 'btn btn-sm btn-outline-info mr-2 filter-btn', 'data-filter' => 'maintenance']);
echo html_writer::tag('button', get_string('status_retired', 'local_materiel'),
    ['class' => 'btn btn-sm btn-outline-secondary filter-btn', 'data-filter' => 'retired']);
echo html_writer::end_div();

echo html_writer::start_div('mb-3');
echo html_writer::tag('label', get_string('search', 'moodle') . ':', ['class' => 'mr-2', 'for' => 'materiel-search']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'id' => 'materiel-search',
    'class' => 'form-control d-inline-block',
    'style' => 'width: 300px;',
    'placeholder' => get_string('search', 'moodle') . '...'
]);
echo html_writer::end_div();

// Get materiel items - no status filter, get all.
$filters = [];
if ($filtertype) {
    $filters['typeid'] = $filtertype;
}

$materiels = \local_materiel\materiel::get_all($filters);

// Display materiel table.
if (empty($materiels)) {
    echo html_writer::tag('p', get_string('no_materiel', 'local_materiel'), ['class' => 'alert alert-info']);
} else {
    $table = new html_table();
    $table->head = [
        html_writer::tag('span', get_string('identifier', 'local_materiel'), ['class' => 'sortable', 'data-column' => '0']),
        html_writer::tag('span', get_string('name', 'local_materiel'), ['class' => 'sortable', 'data-column' => '1']),
        html_writer::tag('span', get_string('type', 'local_materiel'), ['class' => 'sortable', 'data-column' => '2']),
        html_writer::tag('span', get_string('status', 'local_materiel'), ['class' => 'sortable', 'data-column' => '3']),
        html_writer::tag('span', get_string('current_user', 'local_materiel'), ['class' => 'sortable', 'data-column' => '4']),
        get_string('actions', 'local_materiel'),
    ];
    $table->attributes['class'] = 'generaltable materiel-table';
    $table->attributes['id'] = 'materiel-table';

    foreach ($materiels as $materiel) {
        $type = new \local_materiel\materiel_type($materiel->typeid);

        // Get current user if in use.
        $currentuser = '';
        if ($materiel->status == \local_materiel\materiel::STATUS_IN_USE) {
            $logs = \local_materiel\materiel_log::get_by_materiel($materiel->id, 1);
            if (!empty($logs) && $logs[0]->action == \local_materiel\materiel_log::ACTION_CHECKOUT && $logs[0]->userid) {
                $user = $DB->get_record('user', ['id' => $logs[0]->userid]);
                if ($user) {
                    $currentuser = fullname($user);
                }
            }
        }

        // Actions.
        $actions = [];

        // Edit.
        $actions[] = html_writer::link(
            new moodle_url('/local/materiel/edit_materiel.php', ['id' => $materiel->id]),
            $OUTPUT->pix_icon('t/edit', get_string('edit')),
            ['title' => get_string('edit')]
        );

        // Checkout/Checkin.
        if ($materiel->status == \local_materiel\materiel::STATUS_AVAILABLE) {
            $actions[] = html_writer::link(
                new moodle_url('/local/materiel/checkout.php', ['id' => $materiel->id]),
                $OUTPUT->pix_icon('fa-hand-holding', get_string('checkout', 'local_materiel'), 'fontawesome'),
                ['title' => get_string('checkout', 'local_materiel')]
            );
        } else if ($materiel->status == \local_materiel\materiel::STATUS_IN_USE) {
            $actions[] = html_writer::link(
                new moodle_url('/local/materiel/checkin.php', ['id' => $materiel->id]),
                $OUTPUT->pix_icon('t/left', get_string('checkin', 'local_materiel')),
                ['title' => get_string('checkin', 'local_materiel')]
            );
        }

        // History.
        $actions[] = html_writer::link(
            new moodle_url('/local/materiel/history.php', ['id' => $materiel->id]),
            $OUTPUT->pix_icon('i/report', get_string('history', 'local_materiel')),
            ['title' => get_string('history', 'local_materiel')]
        );

        // Delete.
        $actions[] = html_writer::link(
            new moodle_url('/local/materiel/delete.php', ['id' => $materiel->id]),
            $OUTPUT->pix_icon('t/delete', get_string('delete')),
            ['title' => get_string('delete')]
        );

        $statusclass = 'badge badge-';
        switch ($materiel->status) {
            case \local_materiel\materiel::STATUS_AVAILABLE:
                $statusclass .= 'success';
                break;
            case \local_materiel\materiel::STATUS_IN_USE:
                $statusclass .= 'warning';
                break;
            case \local_materiel\materiel::STATUS_MAINTENANCE:
                $statusclass .= 'info';
                break;
            case \local_materiel\materiel::STATUS_RETIRED:
                $statusclass .= 'secondary';
                break;
        }

        $row = new html_table_row([
            html_writer::tag('strong', $materiel->identifier),
            $materiel->name,
            $type->name,
            html_writer::tag('span', get_string('status_' . $materiel->status, 'local_materiel'), ['class' => $statusclass]),
            $currentuser,
            implode(' ', $actions),
        ]);

        // Add data attributes for filtering and sorting.
        $row->attributes['data-status'] = $materiel->status;
        $row->attributes['data-type'] = $type->name;
        $row->attributes['data-identifier'] = strtolower($materiel->identifier);
        $row->attributes['data-name'] = strtolower($materiel->name);
        $row->attributes['data-user'] = strtolower($currentuser);

        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
