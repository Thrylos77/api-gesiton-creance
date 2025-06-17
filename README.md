# API de Gestion de Créances

## Description du Projet

Cette API est conçue pour la gestion complète des créances, permettant de suivre les dettes, les paiements, les clients, et les commerciaux. Elle offre une interface robuste pour l'enregistrement, la consultation, la modification et la suppression des informations relatives aux créances.

## Fonctionnalités

*   Gestion des clients (personnes physiques et morales)
*   Création et suivi des créances
*   Enregistrement des encaissements
*   Gestion des garanties associées aux créances
*   Suivi des recouvrements et des échéances
*   Gestion des commerciaux et de leurs notes
*   Authentification des utilisateurs (administrateurs)

## Technologies Utilisées

*   **Langage**: PHP
*   **Base de données**: MySQL (ou compatible)
*   **Serveur Web**: Apache/Nginx (recommandé)

## Installation

Suivez ces étapes pour configurer le projet localement :

1.  **Cloner le dépôt** :
    ```bash
    git clone https://github.com/votre-utilisateur/API-GESTION_CREANCE.git
    cd API-GESTION_CREANCE
    ```

2.  **Configuration de la Base de Données** :
    *   Créez une base de données MySQL vide (ex: `gestion_creance`).
    *   Importez le schéma de la base de données en exécutant le script `creation db/createdb.php` (ou en important le fichier SQL si disponible).
    *   Mettez à jour les informations de connexion à la base de données dans `config/Database.php` :

        ```php
        // ... existing code ...
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'gestion_creance');
        define('DB_USER', 'votre_utilisateur_db');
        define('DB_PASS', 'votre_mot_de_passe_db');
        // ... existing code ...
        ```

3.  **Dépendances PHP (si applicable)** :
    Si votre projet utilise Composer, exécutez :
    ```bash
    composer install
    ```
    *(Si Composer n'est pas utilisé, cette étape peut être ignorée.)*

4.  **Configuration du Serveur Web** :
    Configurez votre serveur web (Apache ou Nginx) pour pointer le dossier racine de votre projet (souvent le dossier `public/` ou la racine du dépôt si l'API est directement exposée).

## Utilisation de l'API (Endpoints)

Voici quelques exemples d'endpoints de l'API. Pour une liste complète, veuillez consulter le code source des contrôleurs dans le dossier `controllers/`.

*   **Authentification**
    *   `POST /auth/login` : Connecter un utilisateur.

*   **Clients**
    *   `GET /clients` : Récupérer tous les clients.
    *   `GET /clients/{id}` : Récupérer un client par son ID.
    *   `POST /clients` : Ajouter un nouveau client.

*   **Créances**
    *   `GET /creances` : Récupérer toutes les créances.
    *   `POST /creances` : Enregistrer une nouvelle créance.

*(Ajoutez d'autres endpoints pertinents ici)*

## Structure du Projet

*   `config/`: Contient les fichiers de configuration, notamment la connexion à la base de données.
*   `controllers/`: Gère la logique métier et les requêtes HTTP (ex: `ClientControl.php`, `CreControl.php`).
*   `creation db/`: Contient les scripts pour la création de la base de données.
*   `models/`: Définit les structures de données et interagit avec la base de données (ex: `Client.php`, `Creance.php`).

## Base de Données

Le schéma de la base de données est défini dans le script `creation db/createdb.php`. Il inclut des tables pour les administrateurs, clients (personnes physiques et morales), commerciaux, créances, encaissements, garanties, notes, portefeuilles et recouvrements.

## Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails. *(Créez un fichier LICENSE si vous n'en avez pas encore un)*

## Contact

Pour toute question ou suggestion, n'hésitez pas à me contacter via [junivdns@gmail.com ou Lien LinkedIn/GitHub].
