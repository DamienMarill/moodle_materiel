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
 * Materiel form
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_materiel\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing materiel
 */
class materiel_form extends \moodleform {

    /**
     * Form definition
     */
    public function definition() {
        $mform = $this->_form;

        // Hidden ID field.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Type selection.
        $types = \local_materiel\materiel_type::get_all();
        $typeoptions = [];
        foreach ($types as $type) {
            $typeoptions[$type->id] = $type->name;
        }

        if (empty($typeoptions)) {
            $mform->addElement('static', 'notypes', '', get_string('no_types_available', 'local_materiel'));
        } else {
            $mform->addElement('select', 'typeid', get_string('type', 'local_materiel'), $typeoptions);
            $mform->addRule('typeid', get_string('required'), 'required', null, 'client');
        }

        // Identifier.
        $mform->addElement('text', 'identifier', get_string('identifier', 'local_materiel'), ['size' => 50]);
        $mform->setType('identifier', PARAM_TEXT);
        $mform->addRule('identifier', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('identifier', 'identifier', 'local_materiel');

        // Name.
        $mform->addElement('text', 'name', get_string('name', 'local_materiel'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        // Status.
        $statusoptions = \local_materiel\materiel::get_status_options();
        $mform->addElement('select', 'status', get_string('status', 'local_materiel'), $statusoptions);
        $mform->setDefault('status', \local_materiel\materiel::STATUS_AVAILABLE);

        // User autocomplete (shown only when status is 'in_use').
        $options = [
            'ajax' => 'core_user/form_user_selector',
            'multiple' => false,
            'valuehtmlcallback' => function($userid) {
                global $DB, $OUTPUT;
                if (empty($userid)) {
                    return '';
                }
                $user = $DB->get_record('user', ['id' => $userid]);
                if (!$user) {
                    return '';
                }
                $useroptiondata = [
                    'fullname' => fullname($user),
                    'email' => $user->email,
                ];
                return $OUTPUT->render_from_template('core_user/form_user_selector_suggestion', $useroptiondata);
            },
        ];

        $mform->addElement('autocomplete', 'userid', get_string('user', 'local_materiel'), [], $options);
        $mform->hideIf('userid', 'status', 'neq', \local_materiel\materiel::STATUS_IN_USE);

        // Notes.
        $mform->addElement('textarea', 'notes', get_string('notes', 'local_materiel'), ['rows' => 5, 'cols' => 50]);
        $mform->setType('notes', PARAM_TEXT);

        // Action buttons.
        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array Errors
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        // Check if identifier is unique.
        $params = ['identifier' => $data['identifier']];
        if (!empty($data['id'])) {
            $sql = "SELECT * FROM {local_materiel} WHERE identifier = :identifier AND id != :id";
            $params['id'] = $data['id'];
        } else {
            $sql = "SELECT * FROM {local_materiel} WHERE identifier = :identifier";
        }

        if ($DB->record_exists_sql($sql, $params)) {
            $errors['identifier'] = get_string('identifier_exists', 'local_materiel');
        }

        // If status is 'in_use', user must be selected.
        if ($data['status'] === \local_materiel\materiel::STATUS_IN_USE && empty($data['userid'])) {
            $errors['userid'] = get_string('required');
        }

        // If status is not 'in_use', clear userid.
        if ($data['status'] !== \local_materiel\materiel::STATUS_IN_USE) {
            $data['userid'] = null;
        }

        return $errors;
    }
}
