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

// Get filter and sort parameters.
$filterstatus = optional_param('status', '', PARAM_ALPHA);
$filtertype = optional_param('type', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$sort = optional_param('sort', 'name', PARAM_ALPHA);
$order = optional_param('order', 'asc', PARAM_ALPHA);

// Build URL for this page with current parameters.
$baseurl = new moodle_url('/local/materiel/index.php');
$currentparams = [];
if ($filterstatus) {
    $currentparams['status'] = $filterstatus;
}
if ($filtertype) {
    $currentparams['type'] = $filtertype;
}
if ($search) {
    $currentparams['search'] = $search;
}
if ($sort) {
    $currentparams['sort'] = $sort;
}
if ($order) {
    $currentparams['order'] = $order;
}

$PAGE->set_url($baseurl, $currentparams);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_materiel'));
$PAGE->set_heading(get_string('pluginname', 'local_materiel'));
$PAGE->requires->css(new moodle_url('/local/materiel/styles/materiel.css'));

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

// Separator between management buttons and filters.
echo html_writer::tag('hr', '', ['class' => 'my-4']);

// Filter buttons using links.
echo html_writer::start_div('mb-3');
echo html_writer::tag('label', get_string('filter_by_status', 'local_materiel') . ':', ['class' => 'mr-2']);

// Helper function to build filter URL.
$buildfilterurl = function($status) use ($search, $sort, $order, $filtertype) {
    $params = [];
    if ($status) {
        $params['status'] = $status;
    }
    if ($search) {
        $params['search'] = $search;
    }
    if ($sort) {
        $params['sort'] = $sort;
    }
    if ($order) {
        $params['order'] = $order;
    }
    if ($filtertype) {
        $params['type'] = $filtertype;
    }
    return new moodle_url('/local/materiel/index.php', $params);
};

// All materials (no status filter).
$classes = 'btn btn-sm btn-outline-primary mr-2';
if (empty($filterstatus)) {
    $classes .= ' active';
}
echo html_writer::link($buildfilterurl(''), get_string('all_materiel', 'local_materiel'), ['class' => $classes]);

// Available.
$classes = 'btn btn-sm btn-outline-success mr-2';
if ($filterstatus === 'available') {
    $classes .= ' active';
}
echo html_writer::link($buildfilterurl('available'), get_string('materiel_available', 'local_materiel'), ['class' => $classes]);

// In use.
$classes = 'btn btn-sm btn-outline-warning mr-2';
if ($filterstatus === 'in_use') {
    $classes .= ' active';
}
echo html_writer::link($buildfilterurl('in_use'), get_string('materiel_in_use', 'local_materiel'), ['class' => $classes]);

// Maintenance.
$classes = 'btn btn-sm btn-outline-info mr-2';
if ($filterstatus === 'maintenance') {
    $classes .= ' active';
}
echo html_writer::link($buildfilterurl('maintenance'), get_string('status_maintenance', 'local_materiel'), ['class' => $classes]);

// Retired.
$classes = 'btn btn-sm btn-outline-secondary';
if ($filterstatus === 'retired') {
    $classes .= ' active';
}
echo html_writer::link($buildfilterurl('retired'), get_string('status_retired', 'local_materiel'), ['class' => $classes]);

echo html_writer::end_div();

// Type filter with select dropdown.
$typeformurl = new moodle_url('/local/materiel/index.php');
echo html_writer::start_tag('form', ['method' => 'get', 'action' => $typeformurl->out_omit_querystring(), 'class' => 'mb-3']);
echo html_writer::tag('label', get_string('type', 'local_materiel') . ':', ['class' => 'mr-2', 'for' => 'type-filter']);

// Get all types.
$types = \local_materiel\materiel_type::get_all();
$typeoptions = [0 => get_string('all_types', 'local_materiel')];
foreach ($types as $type) {
    $typeoptions[$type->id] = $type->name;
}

// Build select.
echo html_writer::select(
    $typeoptions,
    'type',
    $filtertype,
    false,
    ['id' => 'type-filter', 'class' => 'custom-select d-inline-block', 'style' => 'width: auto;', 'onchange' => 'this.form.submit();']
);

// Preserve other parameters.
if ($filterstatus) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'status', 'value' => $filterstatus]);
}
if ($search) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'search', 'value' => $search]);
}
if ($sort) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sort', 'value' => $sort]);
}
if ($order) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'order', 'value' => $order]);
}
echo html_writer::end_tag('form');

// Search form using GET.
$searchformurl = new moodle_url('/local/materiel/index.php');
echo html_writer::start_tag('form', ['method' => 'get', 'action' => $searchformurl->out_omit_querystring(), 'class' => 'mb-3']);
echo html_writer::tag('label', get_string('search', 'moodle') . ':', ['class' => 'mr-2', 'for' => 'materiel-search']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'id' => 'materiel-search',
    'name' => 'search',
    'class' => 'form-control d-inline-block',
    'style' => 'width: 300px;',
    'placeholder' => get_string('search', 'moodle') . '...',
    'value' => $search
]);
echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('search', 'moodle'), 'class' => 'btn btn-primary ml-2']);
// Preserve other parameters.
if ($filterstatus) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'status', 'value' => $filterstatus]);
}
if ($filtertype) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'type', 'value' => $filtertype]);
}
if ($sort) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sort', 'value' => $sort]);
}
if ($order) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'order', 'value' => $order]);
}
echo html_writer::end_tag('form');

// Get materiel items with filters, sorting and search.
$filters = [];
if ($filterstatus) {
    $filters['status'] = $filterstatus;
}
if ($filtertype) {
    $filters['typeid'] = $filtertype;
}
if ($search) {
    $filters['search'] = $search;
}
if ($sort) {
    $filters['sort'] = $sort;
}
if ($order) {
    $filters['order'] = $order;
}

$materiels = \local_materiel\materiel::get_all($filters);

// Display materiel table.
if (empty($materiels)) {
    echo html_writer::tag('p', get_string('no_materiel', 'local_materiel'), ['class' => 'alert alert-info']);
} else {
    // Helper function to build sort URL.
    $buildsorturl = function($column) use ($filterstatus, $search, $sort, $order, $filtertype) {
        $params = [];
        if ($filterstatus) {
            $params['status'] = $filterstatus;
        }
        if ($search) {
            $params['search'] = $search;
        }
        if ($filtertype) {
            $params['type'] = $filtertype;
        }
        $params['sort'] = $column;
        // Toggle order if clicking current sort column.
        if ($sort === $column) {
            $params['order'] = ($order === 'asc') ? 'desc' : 'asc';
        } else {
            $params['order'] = 'asc';
        }
        return new moodle_url('/local/materiel/index.php', $params);
    };

    // Helper function to get sort indicator.
    $getsortindicator = function($column) use ($sort, $order) {
        if ($sort === $column) {
            return ($order === 'asc') ? ' ▲' : ' ▼';
        }
        return '';
    };

    $table = new html_table();
    $table->head = [
        html_writer::link($buildsorturl('identifier'), get_string('identifier', 'local_materiel') . $getsortindicator('identifier')),
        html_writer::link($buildsorturl('name'), get_string('name', 'local_materiel') . $getsortindicator('name')),
        get_string('type', 'local_materiel'),
        html_writer::link($buildsorturl('status'), get_string('status', 'local_materiel') . $getsortindicator('status')),
        get_string('current_user', 'local_materiel'),
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
                    $userpicture = $OUTPUT->user_picture($user, ['size' => 35, 'class' => 'mr-2']);
                    $currentuser = $userpicture . ' ' . fullname($user);
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
                $OUTPUT->pix_icon('t/assignroles', get_string('checkout', 'local_materiel')),
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

        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
