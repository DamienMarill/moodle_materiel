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
 * Upgrade script for local_materiel
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the local_materiel plugin
 *
 * @param int $oldversion The old version of the plugin
 * @return bool
 */
function xmldb_local_materiel_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Add future upgrade steps here.

    // Version 2025111704: Fill missing actionby values in logs.
    if ($oldversion < 2025111704) {
        // Get all logs without actionby or with actionby = 0.
        $logs = $DB->get_records_select('local_materiel_logs',
            'actionby IS NULL OR actionby = 0',
            null,
            '',
            'id, actionby'
        );

        if (!empty($logs)) {
            // Get the first admin user ID to use as default.
            $admin = get_admin();
            $defaultuserid = $admin ? $admin->id : 2;

            foreach ($logs as $log) {
                $log->actionby = $defaultuserid;
                $DB->update_record('local_materiel_logs', $log);
            }

            mtrace("Updated " . count($logs) . " log entries with missing actionby field.");
        }

        upgrade_plugin_savepoint(true, 2025111704, 'local', 'materiel');
    }

    // Version 2025112302: Remove userid field from local_materiel table (use logs instead).
    if ($oldversion < 2025112302) {
        $table = new xmldb_table('local_materiel');
        $field = new xmldb_field('userid');

        // Drop foreign key if exists.
        $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        if ($dbman->find_key_name($table, $key)) {
            $dbman->drop_key($table, $key);
        }

        // Drop field if exists.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025112302, 'local', 'materiel');
    }

    // Version 2025112601: Migrate from cohort to role-based access.
    if ($oldversion < 2025112601) {
        $context = context_system::instance();

        // Check if role already exists.
        $role = $DB->get_record('role', ['shortname' => 'mmi_materiel']);

        if (!$role) {
            // Create the role.
            $roleid = create_role(
                'MMI Matériel',
                'mmi_materiel',
                'Permet d\'accéder au système de gestion de matériel MMI et d\'utiliser le sélecteur d\'utilisateur',
                'user'
            );

            // Set role context levels (system level).
            set_role_contextlevels($roleid, [CONTEXT_SYSTEM]);

            // Assign capabilities to the role.
            $capabilities = [
                'local/materiel:view' => CAP_ALLOW,
                'local/materiel:manage' => CAP_ALLOW,
                'moodle/user:viewalldetails' => CAP_ALLOW,
                'moodle/site:viewfullnames' => CAP_ALLOW,
            ];

            foreach ($capabilities as $capability => $permission) {
                assign_capability($capability, $permission, $roleid, $context->id, true);
            }

            mtrace("Created role 'MMI Matériel' with ID: {$roleid}");
        } else {
            $roleid = $role->id;
            mtrace("Role 'MMI Matériel' already exists with ID: {$roleid}");

            // Make sure the role has all required capabilities.
            $capabilities = [
                'local/materiel:view' => CAP_ALLOW,
                'local/materiel:manage' => CAP_ALLOW,
                'moodle/user:viewalldetails' => CAP_ALLOW,
                'moodle/site:viewfullnames' => CAP_ALLOW,
            ];

            foreach ($capabilities as $capability => $permission) {
                assign_capability($capability, $permission, $roleid, $context->id, true);
            }

            mtrace("Updated capabilities for role 'MMI Matériel'");
        }

        // Migrate users from cohort to role.
        $cohort = $DB->get_record('cohort', ['idnumber' => 'MMI_materiel']);

        if ($cohort) {
            // Get all cohort members.
            $members = $DB->get_records('cohort_members', ['cohortid' => $cohort->id]);

            if (!empty($members)) {
                $count = 0;
                foreach ($members as $member) {
                    // Check if user already has the role.
                    $roleassignment = $DB->get_record('role_assignments', [
                        'roleid' => $roleid,
                        'contextid' => $context->id,
                        'userid' => $member->userid,
                    ]);

                    if (!$roleassignment) {
                        // Assign role to user at system context.
                        role_assign($roleid, $member->userid, $context->id);
                        $count++;
                    }
                }

                mtrace("Assigned role to {$count} users from cohort 'MMI_materiel'");
            } else {
                mtrace("No members found in cohort 'MMI_materiel'");
            }

            // Delete cohort members first.
            $DB->delete_records('cohort_members', ['cohortid' => $cohort->id]);

            // Delete the cohort.
            cohort_delete_cohort($cohort);

            mtrace("Deleted cohort 'MMI_materiel'");
        } else {
            mtrace("Cohort 'MMI_materiel' not found, skipping migration");
        }

        upgrade_plugin_savepoint(true, 2025112601, 'local', 'materiel');
    }

    return true;
}
