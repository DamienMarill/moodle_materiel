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

    return true;
}
