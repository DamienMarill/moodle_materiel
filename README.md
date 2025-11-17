# Moodle Matériel Plugin

Plugin Moodle pour la gestion de matériel avec système de prêt et historique.

## Description

Ce plugin permet de gérer le matériel dans Moodle. Il s'agit d'un plugin de type "local" qui ajoute des fonctionnalités personnalisées à votre installation Moodle.

### Fonctionnalités principales

- **Gestion des types de matériel** : Créer et organiser différentes catégories
- **Inventaire complet** : Suivi de chaque équipement avec identifiant unique
- **Système de prêt** : Prêter du matériel à des utilisateurs et le récupérer
- **Historique complet** : Traçabilité de tous les mouvements
- **Gestion par cohorte** : Seuls les membres de la cohorte MMI_materiel ont accès
- **Interface intuitive** : Tableaux avec filtres, badges de statut colorés

## Installation

### Option 1 : Depuis une release GitHub (recommandé)

1. Téléchargez la dernière release depuis la page [Releases](https://github.com/DamienMarill/moodle_materiel/releases)
2. Extrayez le fichier zip dans `/local/materiel/` de votre installation Moodle
3. Connectez-vous en tant qu'administrateur
4. Accédez à "Administration du site > Notifications"
5. Suivez les instructions d'installation

### Option 2 : Depuis le code source

1. Clonez le dépôt dans `/local/materiel/` : `git clone https://github.com/DamienMarill/moodle_materiel.git materiel`
2. Connectez-vous en tant qu'administrateur
3. Accédez à "Administration du site > Notifications"
4. Suivez les instructions d'installation

## Configuration

### Ajout d'utilisateurs à la cohorte

1. Accédez à "Administration du site > Utilisateurs > Cohortes"
2. Recherchez la cohorte "MMI Matériel" (idnumber: MMI_materiel)
3. Ajoutez les utilisateurs qui doivent avoir accès à la gestion du matériel

## Utilisation

Une fois installé, le plugin ajoute un onglet "Matériel" dans le menu de navigation (barre latérale droite) pour les utilisateurs membres de la cohorte MMI_materiel.

### Gestion du matériel

- **Ajouter du matériel** : Bouton "Ajouter du matériel" sur la page principale
- **Créer des types** : Bouton "Ajouter un type" pour créer des catégories
- **Prêter** : Cliquez sur l'icône de prêt pour assigner du matériel à un utilisateur
- **Récupérer** : Cliquez sur l'icône de retour pour marquer le matériel comme disponible
- **Consulter l'historique** : Cliquez sur l'icône d'historique pour voir tous les mouvements

## Permissions

Le plugin définit trois capacités :

- `local/materiel:view` - Permet de voir le matériel
- `local/materiel:manage` - Permet de gérer le matériel
- `local/materiel:admin` - Permet d'administrer le matériel

**Note** : L'accès au plugin est également contrôlé par l'appartenance à la cohorte MMI_materiel.

## Développement

### Structure du plugin

```
local/materiel/
├── version.php                     # Informations sur la version
├── lib.php                         # Fonctions principales
├── index.php                       # Page principale
├── edit_materiel.php               # Ajouter/modifier du matériel
├── edit_type.php                   # Ajouter/modifier un type
├── manage_types.php                # Gestion des types
├── checkout.php                    # Prêter du matériel
├── checkin.php                     # Récupérer du matériel
├── delete.php                      # Supprimer du matériel
├── delete_type.php                 # Supprimer un type
├── history.php                     # Historique des mouvements
├── classes/
│   ├── materiel.php               # Classe matériel
│   ├── materiel_type.php          # Classe type
│   ├── materiel_log.php           # Classe log
│   └── form/
│       ├── materiel_form.php      # Formulaire matériel
│       ├── type_form.php          # Formulaire type
│       └── checkout_form.php      # Formulaire prêt
├── lang/                           # Fichiers de langue
│   ├── en/local_materiel.php
│   └── fr/local_materiel.php
├── db/
│   ├── install.xml                # Schéma de base de données
│   ├── install.php                # Script d'installation
│   ├── upgrade.php                # Script de mise à jour
│   └── access.php                 # Définitions des capacités
└── .github/workflows/
    └── release.yml                # CI/CD pour releases automatiques
```

### Releases automatiques

Le projet utilise GitHub Actions pour créer automatiquement des releases lors des commits sur la branche `main`.

#### Processus de release

1. Mettez à jour la version dans `version.php` :
   ```php
   $plugin->version = 2025111703;  // YYYYMMDDXX
   $plugin->release = 'v1.3.0';
   ```

2. Commitez les changements sur `main` :
   ```bash
   git commit -am "Release v1.3.0"
   git push origin main
   ```

3. Le workflow GitHub Actions :
   - Extrait la version depuis `version.php`
   - Crée un package zip du plugin
   - Génère un changelog depuis les commits
   - Crée une release GitHub avec le zip en pièce jointe

#### Développement local

Pour contribuer au développement :

```bash
# Cloner le dépôt
git clone https://github.com/DamienMarill/moodle_materiel.git

# Créer une branche de fonctionnalité
git checkout -b feature/ma-fonctionnalite

# Développer et tester

# Committer les changements
git commit -am "Ajout de ma fonctionnalité"

# Pousser et créer une pull request
git push origin feature/ma-fonctionnalite
```

## Compatibilité

- Moodle 3.11 ou supérieur
- PHP 7.3 ou supérieur

## Licence

GNU GPL v3 ou ultérieure

## Auteur

2025 Your Name
