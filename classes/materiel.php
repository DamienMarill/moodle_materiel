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
 * Materiel class
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_materiel;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for managing materiel items
 */
class materiel {

    /** @var int Materiel ID */
    public $id;

    /** @var int Type ID */
    public $typeid;

    /** @var string Unique identifier */
    public $identifier;

    /** @var string Materiel name */
    public $name;

    /** @var string Status (available, in_use, maintenance, retired) */
    public $status;

    /** @var int User ID (current user if in use) */
    public $userid;

    /** @var string Notes */
    public $notes;

    /** @var int Creation timestamp */
    public $timecreated;

    /** @var int Last modification timestamp */
    public $timemodified;

    /** Status constants */
    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_USE = 'in_use';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_RETIRED = 'retired';

    /**
     * Constructor
     *
     * @param int $id Materiel ID (0 for new materiel)
     */
    public function __construct($id = 0) {
        if ($id) {
            $this->load($id);
        } else {
            $this->status = self::STATUS_AVAILABLE;
        }
    }

    /**
     * Load materiel from database
     *
     * @param int $id Materiel ID
     * @return bool Success
     */
    public function load($id) {
        global $DB;

        if ($record = $DB->get_record('local_materiel', ['id' => $id])) {
            $this->id = $record->id;
            $this->typeid = $record->typeid;
            $this->identifier = $record->identifier;
            $this->name = $record->name;
            $this->status = $record->status;
            $this->userid = $record->userid ?? null;
            $this->notes = $record->notes;
            $this->timecreated = $record->timecreated;
            $this->timemodified = $record->timemodified;
            return true;
        }
        return false;
    }

    /**
     * Save materiel to database
     *
     * @return bool Success
     */
    public function save() {
        global $DB;

        $now = time();
        $record = new \stdClass();
        $record->typeid = $this->typeid;
        $record->identifier = $this->identifier;
        $record->name = $this->name;
        $record->status = $this->status;
        $record->userid = $this->userid ?? null;
        $record->notes = $this->notes;
        $record->timemodified = $now;

        if (!empty($this->id)) {
            $record->id = $this->id;
            return $DB->update_record('local_materiel', $record);
        } else {
            $record->timecreated = $now;
            $this->id = $DB->insert_record('local_materiel', $record);
            $this->timecreated = $now;
            $this->timemodified = $now;
            return !empty($this->id);
        }
    }

    /**
     * Delete materiel from database
     *
     * @return bool Success
     */
    public function delete() {
        global $DB;

        if (!empty($this->id)) {
            return $DB->delete_records('local_materiel', ['id' => $this->id]);
        }
        return false;
    }

    /**
     * Get all materiel items
     *
     * @param array $filters Optional filters (status, typeid, search, sort, order)
     * @return array Array of materiel objects
     */
    public static function get_all($filters = []) {
        global $DB;

        $params = [];
        $where = [];

        // Status filter
        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        // Type filter
        if (!empty($filters['typeid'])) {
            $where[] = "typeid = :typeid";
            $params['typeid'] = $filters['typeid'];
        }

        // Search filter
        if (!empty($filters['search'])) {
            $search = '%' . $DB->sql_like_escape($filters['search']) . '%';
            $where[] = $DB->sql_like('identifier', ':search1', false) . ' OR ' .
                       $DB->sql_like('name', ':search2', false);
            $params['search1'] = $search;
            $params['search2'] = $search;
        }

        // Build WHERE clause
        $sql = "SELECT * FROM {local_materiel}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', array_map(function($w) {
                return "(" . $w . ")";
            }, $where));
        }

        // Sorting
        $sort = !empty($filters['sort']) ? $filters['sort'] : 'name';
        $order = !empty($filters['order']) && $filters['order'] === 'desc' ? 'DESC' : 'ASC';

        // Validate sort column to prevent SQL injection
        $validcolumns = ['identifier', 'name', 'status', 'timecreated', 'timemodified'];
        if (!in_array($sort, $validcolumns)) {
            $sort = 'name';
        }

        $sql .= " ORDER BY {$sort} {$order}";

        $records = $DB->get_records_sql($sql, $params);
        $items = [];

        foreach ($records as $record) {
            $item = new self();
            $item->id = $record->id;
            $item->typeid = $record->typeid;
            $item->identifier = $record->identifier;
            $item->name = $record->name;
            $item->status = $record->status;
            $item->userid = $record->userid ?? null;
            $item->notes = $record->notes;
            $item->timecreated = $record->timecreated;
            $item->timemodified = $record->timemodified;
            $items[] = $item;
        }

        return $items;
    }

    /**
     * Get materiel by identifier
     *
     * @param string $identifier Unique identifier
     * @return materiel|false Materiel object or false
     */
    public static function get_by_identifier($identifier) {
        global $DB;

        if ($record = $DB->get_record('local_materiel', ['identifier' => $identifier])) {
            $item = new self();
            $item->id = $record->id;
            $item->typeid = $record->typeid;
            $item->identifier = $record->identifier;
            $item->name = $record->name;
            $item->status = $record->status;
            $item->userid = $record->userid ?? null;
            $item->notes = $record->notes;
            $item->timecreated = $record->timecreated;
            $item->timemodified = $record->timemodified;
            return $item;
        }
        return false;
    }

    /**
     * Get available status values
     *
     * @return array Status values
     */
    public static function get_status_options() {
        return [
            self::STATUS_AVAILABLE => get_string('status_available', 'local_materiel'),
            self::STATUS_IN_USE => get_string('status_in_use', 'local_materiel'),
            self::STATUS_MAINTENANCE => get_string('status_maintenance', 'local_materiel'),
            self::STATUS_RETIRED => get_string('status_retired', 'local_materiel'),
        ];
    }
}
