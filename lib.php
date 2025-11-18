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
 * Library functions for local_materiel
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Check if user is member of MMI_materiel cohort
 *
 * @param int $userid User ID (default current user)
 * @return bool True if user is member of the cohort
 */
function local_materiel_user_has_access($userid = null) {
    global $DB, $USER;

    if ($userid === null) {
        $userid = $USER->id;
    }

    // Check if MMI_materiel cohort exists.
    $cohort = $DB->get_record('cohort', ['idnumber' => 'MMI_materiel']);
    if (!$cohort) {
        return false;
    }

    // Check if user is member of the cohort.
    $ismember = $DB->record_exists('cohort_members', [
        'cohortid' => $cohort->id,
        'userid' => $userid,
    ]);

    return $ismember;
}

/**
 * Add link to the plugin in the navigation menu
 *
 * @param global_navigation $navigation
 */
function local_materiel_extend_navigation(global_navigation $navigation) {
    global $PAGE, $USER;

    // Check if user has access through cohort membership.
    if (local_materiel_user_has_access($USER->id)) {
        $node = $navigation->add(
            get_string('materiel', 'local_materiel'),
            new moodle_url('/local/materiel/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'local_materiel',
            new pix_icon('i/item', '')
        );
        $node->showinflatnavigation = true;
    }
}
