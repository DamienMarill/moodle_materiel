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
 * Language strings for local_materiel
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Equipment Management';
$string['materiel'] = 'Equipment';
$string['materiel:view'] = 'View equipment';
$string['materiel:manage'] = 'Manage equipment';
$string['materiel:admin'] = 'Administer equipment';

// Statuses.
$string['status_available'] = 'Available';
$string['status_in_use'] = 'In use';
$string['status_maintenance'] = 'Maintenance';
$string['status_retired'] = 'Retired';

// Actions.
$string['action_checkout'] = 'Check out';
$string['action_checkin'] = 'Check in';
$string['action_maintenance'] = 'Maintenance';
$string['action_repair'] = 'Repair';
$string['action_retire'] = 'Retire';

// Fields.
$string['type'] = 'Type';
$string['identifier'] = 'Identifier';
$string['identifier_help'] = 'Unique identifier for the equipment (serial number, barcode, etc.)';
$string['name'] = 'Name';
$string['description'] = 'Description';
$string['status'] = 'Status';
$string['notes'] = 'Notes';
$string['user'] = 'User';
$string['action'] = 'Action';
$string['date'] = 'Date';
$string['actions'] = 'Actions';
$string['current_user'] = 'Current user';
$string['action_by'] = 'Action by';

// Buttons and links.
$string['add_materiel'] = 'Add equipment';
$string['edit_materiel'] = 'Edit equipment';
$string['add_type'] = 'Add type';
$string['edit_type'] = 'Edit type';
$string['manage_types'] = 'Manage types';
$string['checkout'] = 'Check out';
$string['checkin'] = 'Check in';
$string['history'] = 'History';
$string['back_to_list'] = 'Back to list';

// Messages.
$string['no_materiel'] = 'No equipment available';
$string['no_types'] = 'No types defined';
$string['no_types_available'] = 'No types available. Please create a type first.';
$string['no_history'] = 'No history available for this equipment';
$string['materiel_saved'] = 'Equipment saved successfully';
$string['type_saved'] = 'Type saved successfully';
$string['error_saving'] = 'Error saving';
$string['materiel_deleted'] = 'Equipment deleted successfully';
$string['type_deleted'] = 'Type deleted successfully';
$string['error_deleting'] = 'Error deleting';
$string['checkout_success'] = 'Equipment checked out successfully';
$string['checkin_success'] = 'Equipment checked in successfully';
$string['materiel_not_found'] = 'Equipment not found';
$string['type_not_found'] = 'Type not found';
$string['materiel_not_available'] = 'Equipment is not available for checkout';
$string['materiel_not_in_use'] = 'Equipment is not currently in use';
$string['type_in_use'] = 'This type cannot be deleted because it is used by equipment';
$string['identifier_exists'] = 'This identifier already exists';

// Confirmations.
$string['delete_confirm'] = 'Are you sure you want to delete this equipment: {$a}?';
$string['delete_type_confirm'] = 'Are you sure you want to delete this type: {$a}?';
$string['checkin_confirm'] = 'Are you sure you want to check in this equipment?';

// Descriptions.
$string['checkout_description'] = 'Select a user to assign this equipment to.';

// Filters and search.
$string['filter_by_status'] = 'Filter by status';
$string['all_materiel'] = 'All equipment';
$string['materiel_in_use'] = 'In use';
$string['materiel_available'] = 'Available';
