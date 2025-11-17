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
 * Chaînes de langue françaises pour local_materiel
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Gestion de matériel';
$string['materiel'] = 'Matériel';
$string['materiel:view'] = 'Voir le matériel';
$string['materiel:manage'] = 'Gérer le matériel';
$string['materiel:admin'] = 'Administrer le matériel';

// Statuts.
$string['status_available'] = 'Disponible';
$string['status_in_use'] = 'En cours d\'utilisation';
$string['status_maintenance'] = 'En maintenance';
$string['status_retired'] = 'Retiré';

// Actions.
$string['action_checkout'] = 'Sortie';
$string['action_checkin'] = 'Retour';
$string['action_maintenance'] = 'Maintenance';
$string['action_repair'] = 'Réparation';
$string['action_retire'] = 'Mise au rebut';

// Champs.
$string['type'] = 'Type';
$string['identifier'] = 'Identifiant';
$string['identifier_help'] = 'Identifiant unique du matériel (numéro de série, code-barres, etc.)';
$string['name'] = 'Nom';
$string['description'] = 'Description';
$string['status'] = 'Statut';
$string['notes'] = 'Notes';
$string['user'] = 'Utilisateur';
$string['action'] = 'Action';
$string['date'] = 'Date';
$string['actions'] = 'Actions';
$string['current_user'] = 'Utilisateur actuel';
$string['action_by'] = 'Action effectuée par';

// Boutons et liens.
$string['add_materiel'] = 'Ajouter du matériel';
$string['edit_materiel'] = 'Modifier le matériel';
$string['add_type'] = 'Ajouter un type';
$string['edit_type'] = 'Modifier le type';
$string['manage_types'] = 'Gérer les types';
$string['checkout'] = 'Prêter';
$string['checkin'] = 'Récupérer';
$string['history'] = 'Historique';
$string['back_to_list'] = 'Retour à la liste';

// Messages.
$string['no_materiel'] = 'Aucun matériel disponible';
$string['no_types'] = 'Aucun type défini';
$string['no_types_available'] = 'Aucun type disponible. Veuillez d\'abord créer un type.';
$string['no_history'] = 'Aucun historique disponible pour ce matériel';
$string['materiel_saved'] = 'Matériel enregistré avec succès';
$string['type_saved'] = 'Type enregistré avec succès';
$string['error_saving'] = 'Erreur lors de l\'enregistrement';
$string['materiel_deleted'] = 'Matériel supprimé avec succès';
$string['type_deleted'] = 'Type supprimé avec succès';
$string['error_deleting'] = 'Erreur lors de la suppression';
$string['checkout_success'] = 'Matériel prêté avec succès';
$string['checkin_success'] = 'Matériel récupéré avec succès';
$string['materiel_not_found'] = 'Matériel introuvable';
$string['type_not_found'] = 'Type introuvable';
$string['materiel_not_available'] = 'Le matériel n\'est pas disponible pour le prêt';
$string['materiel_not_in_use'] = 'Le matériel n\'est pas actuellement en cours d\'utilisation';
$string['type_in_use'] = 'Ce type ne peut pas être supprimé car il est utilisé par du matériel';
$string['identifier_exists'] = 'Cet identifiant existe déjà';

// Confirmations.
$string['delete_confirm'] = 'Êtes-vous sûr de vouloir supprimer ce matériel : {$a} ?';
$string['delete_type_confirm'] = 'Êtes-vous sûr de vouloir supprimer ce type : {$a} ?';
$string['checkin_confirm'] = 'Êtes-vous sûr de vouloir récupérer ce matériel ?';

// Descriptions.
$string['checkout_description'] = 'Sélectionnez un utilisateur à qui confier ce matériel.';

// Filtres et recherche.
$string['filter_by_status'] = 'Filtrer par statut';
$string['all_materiel'] = 'Tout le matériel';
$string['materiel_in_use'] = 'En cours d\'utilisation';
$string['materiel_available'] = 'Disponible';
