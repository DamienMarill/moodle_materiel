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
 * Materiel log class
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_materiel;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for managing materiel logs
 */
class materiel_log {

    /** @var int Log ID */
    public $id;

    /** @var int Materiel ID */
    public $materielid;

    /** @var int User ID (to whom the materiel is assigned) */
    public $userid;

    /** @var string Action type */
    public $action;

    /** @var string Notes */
    public $notes;

    /** @var int User ID who performed the action */
    public $actionby;

    /** @var int Creation timestamp */
    public $timecreated;

    /** Action constants */
    const ACTION_CHECKOUT = 'checkout';
    const ACTION_CHECKIN = 'checkin';
    const ACTION_MAINTENANCE = 'maintenance';
    const ACTION_REPAIR = 'repair';
    const ACTION_RETIRE = 'retire';

    /**
     * Constructor
     *
     * @param int $id Log ID (0 for new log)
     */
    public function __construct($id = 0) {
        if ($id) {
            $this->load($id);
        }
    }

    /**
     * Load log from database
     *
     * @param int $id Log ID
     * @return bool Success
     */
    public function load($id) {
        global $DB;

        if ($record = $DB->get_record('local_materiel_logs', ['id' => $id])) {
            $this->id = $record->id;
            $this->materielid = $record->materielid;
            $this->userid = $record->userid;
            $this->action = $record->action;
            $this->notes = $record->notes;
            $this->actionby = $record->actionby;
            $this->timecreated = $record->timecreated;
            return true;
        }
        return false;
    }

    /**
     * Save log to database
     *
     * @return bool Success
     */
    public function save() {
        global $DB, $USER;

        $now = time();
        $record = new \stdClass();
        $record->materielid = $this->materielid;
        $record->userid = $this->userid;
        $record->action = $this->action;
        $record->notes = $this->notes;
        $record->actionby = !empty($this->actionby) ? $this->actionby : $USER->id;
        $record->timecreated = $now;

        if (!empty($this->id)) {
            $record->id = $this->id;
            return $DB->update_record('local_materiel_logs', $record);
        } else {
            $this->id = $DB->insert_record('local_materiel_logs', $record);
            $this->timecreated = $now;
            return !empty($this->id);
        }
    }

    /**
     * Get logs for a specific materiel
     *
     * @param int $materielid Materiel ID
     * @param int $limit Maximum number of logs to return (0 for all)
     * @return array Array of materiel_log objects
     */
    public static function get_by_materiel($materielid, $limit = 0) {
        global $DB;

        $records = $DB->get_records('local_materiel_logs',
            ['materielid' => $materielid],
            'timecreated DESC',
            '*',
            0,
            $limit
        );

        $logs = [];
        foreach ($records as $record) {
            $log = new self();
            $log->id = $record->id;
            $log->materielid = $record->materielid;
            $log->userid = $record->userid;
            $log->action = $record->action;
            $log->notes = $record->notes;
            $log->actionby = $record->actionby;
            $log->timecreated = $record->timecreated;
            $logs[] = $log;
        }

        return $logs;
    }

    /**
     * Get logs for a specific user
     *
     * @param int $userid User ID
     * @param int $limit Maximum number of logs to return (0 for all)
     * @return array Array of materiel_log objects
     */
    public static function get_by_user($userid, $limit = 0) {
        global $DB;

        $records = $DB->get_records('local_materiel_logs',
            ['userid' => $userid],
            'timecreated DESC',
            '*',
            0,
            $limit
        );

        $logs = [];
        foreach ($records as $record) {
            $log = new self();
            $log->id = $record->id;
            $log->materielid = $record->materielid;
            $log->userid = $record->userid;
            $log->action = $record->action;
            $log->notes = $record->notes;
            $log->actionby = $record->actionby;
            $log->timecreated = $record->timecreated;
            $logs[] = $log;
        }

        return $logs;
    }

    /**
     * Create a checkout log entry
     *
     * @param int $materielid Materiel ID
     * @param int $userid User ID
     * @param string $notes Optional notes
     * @return bool Success
     */
    public static function create_checkout($materielid, $userid, $notes = '') {
        $log = new self();
        $log->materielid = $materielid;
        $log->userid = $userid;
        $log->action = self::ACTION_CHECKOUT;
        $log->notes = $notes;
        return $log->save();
    }

    /**
     * Create a checkin log entry
     *
     * @param int $materielid Materiel ID
     * @param string $notes Optional notes
     * @return bool Success
     */
    public static function create_checkin($materielid, $notes = '') {
        $log = new self();
        $log->materielid = $materielid;
        $log->userid = null;
        $log->action = self::ACTION_CHECKIN;
        $log->notes = $notes;
        return $log->save();
    }

    /**
     * Get current user for a materiel (from latest checkout log)
     *
     * @param int $materielid Materiel ID
     * @return int|null User ID or null if not checked out
     */
    public static function get_current_user($materielid) {
        global $DB;

        // Get the most recent checkout log for this materiel.
        $sql = "SELECT userid
                FROM {local_materiel_logs}
                WHERE materielid = :materielid
                  AND action = :action
                ORDER BY timecreated DESC
                LIMIT 1";

        $params = [
            'materielid' => $materielid,
            'action' => self::ACTION_CHECKOUT,
        ];

        $record = $DB->get_record_sql($sql, $params);
        return $record ? $record->userid : null;
    }

    /**
     * Get available action values
     *
     * @return array Action values
     */
    public static function get_action_options() {
        return [
            self::ACTION_CHECKOUT => get_string('action_checkout', 'local_materiel'),
            self::ACTION_CHECKIN => get_string('action_checkin', 'local_materiel'),
            self::ACTION_MAINTENANCE => get_string('action_maintenance', 'local_materiel'),
            self::ACTION_REPAIR => get_string('action_repair', 'local_materiel'),
            self::ACTION_RETIRE => get_string('action_retire', 'local_materiel'),
        ];
    }
}
