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
 * Check if user has the MMI Matériel role
 *
 * @param int $userid User ID (default current user)
 * @return bool True if user has the role
 */
function local_materiel_user_has_access($userid = null) {
    global $DB, $USER;

    if ($userid === null) {
        $userid = $USER->id;
    }

    // Get system context.
    $context = context_system::instance();

    // Check if MMI_materiel role exists.
    $role = $DB->get_record('role', ['shortname' => 'mmi_materiel']);
    if (!$role) {
        return false;
    }

    // Check if user has the role assigned at system level.
    $hasrole = user_has_role_assignment($userid, $role->id, $context->id);

    return $hasrole;
}

/**
 * Add link to the plugin in the navigation menu
 *
 * @param global_navigation $navigation
 */
function local_materiel_extend_navigation(global_navigation $navigation) {
    global $PAGE, $USER;

    // Check if user has access through role assignment.
    if (local_materiel_user_has_access($USER->id)) {
        $node = $navigation->add(
            get_string('materiel', 'local_materiel'),
            new moodle_url('/local/materiel/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'local_materiel',
            new pix_icon('i/repository', '')
        );
        $node->showinflatnavigation = true;
    }
}
