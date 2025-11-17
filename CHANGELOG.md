# Changelog - Plugin Gestion de Matériel

## [v1.4.0] - 2025-11-17

### Ajouté
- Script de mise à jour de la base de données pour remplir les valeurs manquantes du champ `actionby` dans les logs existants
- Documentation sur le suivi de l'utilisateur effectuant les actions

### Amélioré
- Le champ `actionby` (utilisateur qui effectue l'action) est maintenant correctement rempli pour tous les logs
- Les logs sans `actionby` sont automatiquement mis à jour avec l'ID de l'administrateur lors de l'upgrade

### Technique
- Version de la base de données: 2025111704
- Ajout d'un script d'upgrade pour gérer les données existantes

---

## [v1.3.0] - 2025-11-17

### Modifié
- Remplacement des 3 onglets (Tout, Disponible, En cours) par un tableau unique avec filtres
- Ajout de boutons de filtrage rapide pour tous les statuts (Disponible, En cours, Maintenance, Retiré)
- Ajout d'une barre de recherche textuelle
- Ajout du tri sur toutes les colonnes (clic sur les en-têtes)

### Ajouté
- Fichier JavaScript `materiel_table.js` pour gérer le tri et les filtres côté client
- Fichier CSS `materiel.css` pour améliorer l'apparence

### Amélioré
- Navigation plus fluide sans rechargement de page
- Meilleure expérience utilisateur avec filtres instantanés

---

## Fonctionnalités du système de logs

### Informations enregistrées pour chaque action

Pour chaque action effectuée sur le matériel (prêt, retour, maintenance, etc.), le système enregistre :

1. **Utilisateur bénéficiaire** (`userid`) : L'utilisateur à qui le matériel est confié (pour les prêts)
2. **Utilisateur exécutant** (`actionby`) : L'utilisateur qui a effectué l'action dans le système
3. **Type d'action** (`action`) : checkout, checkin, maintenance, repair, retire
4. **Date et heure** (`timecreated`) : Timestamp de l'action
5. **Notes** (`notes`) : Notes ou commentaires optionnels

### Différence entre userid et actionby

- **userid** : La personne à qui on confie le matériel (peut être différente de la personne qui enregistre l'action)
- **actionby** : La personne connectée qui effectue l'action dans le système (toujours rempli automatiquement)

### Exemple

Si Alice (gestionnaire) enregistre un prêt de matériel pour Bob :
- `userid` = Bob (bénéficiaire du matériel)
- `actionby` = Alice (personne qui a enregistré le prêt)

### Affichage dans l'historique

L'historique affiche les deux informations dans des colonnes séparées :
- **Utilisateur** : À qui le matériel est confié
- **Action par** : Qui a enregistré l'action dans le système
