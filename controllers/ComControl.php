<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

require_once '../config/Database.php';
require_once '../models/Commerciaux.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet commerciaux
$commercial = new Commerciaux($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées

    //Ajout d'un commercial
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->matriculeCom) && !empty($data->nomCom) && !empty($data->prenomCom) && !empty($data->dateNaissCom) && !empty($data->telCom) && !empty($data->adresseCom) && !empty($data->emailCom) && !empty($data->usernameCom) && !empty($data->passwordCom)) {
        // On hydrate l'objet commercial
        $commercial->matriculeCom = htmlspecialchars($data->matriculeCom);
        $commercial->nomCom = htmlspecialchars($data->nomCom);
        $commercial->prenomCom = htmlspecialchars($data->prenomCom);
        $commercial->dateNaissCom = htmlspecialchars($data->dateNaissCom);
        $commercial->telCom = htmlspecialchars($data->telCom);
        $commercial->adresseCom = htmlspecialchars($data->adresseCom);
        $commercial->emailCom = htmlspecialchars($data->emailCom);
        $commercial->usernameCom = htmlspecialchars($data->usernameCom);
        $commercial->passwordCom = htmlspecialchars($data->passwordCom);

        $result = $commercial->createCom();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "Le commercial a été ajouté avec succès"]);
        } else {
            http_response_code(300);
            echo json_encode(['message' => "L'ajout du commercial a échoué"]);
        }
    }
    
    // Authentification d'un commercial
    elseif (!empty($data->usernameCom) && !empty($data->passwordCom)) {
        $commercial->usernameCom = htmlspecialchars($data->usernameCom);
        $commercial->passwordCom = htmlspecialchars($data->passwordCom);

        $matricule = $commercial->getMatriculeComByEmailAndPassword();

        if ($matricule) {
            http_response_code(200);
            echo json_encode([
                $matricule
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => "L'authentification a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
}

//Suppression d'un commercial
if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->matriculeCom)) {
        $commercial->matriculeCom = htmlspecialchars($data->matriculeCom);
        if ($commercial->deleteCom()) {
            http_response_code(200);
            echo json_encode(['message' => "La suppression a été effectuée avec succès"]);
        } else {
            http_response_code(300);
            echo json_encode(['message' => "La suppression n'a pas été effectuée"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Vous devez préciser l'identifiant du commercial"]);
    }
}
//Visualisation des informations d'un commercial par matricule
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    
    $commercialInfo = $commercial->getComByMatricule($matriculeCom);

    if ($commercialInfo) {
        http_response_code(200);
        echo json_encode($commercialInfo);
    } else {
        http_response_code(404);
        echo json_encode(['message' => "Aucune information trouvée pour le commercial avec le matricule $matriculeCom"]);
    }
}

//Visualisation des commerciaux
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action']) && $_GET['action'] === 'readAll' ) {
    // Récupération des données
    $statement = $commercial->readCom();

    if ($statement->rowCount() > 0) {
        $data = $statement->fetchAll();

        // on renvoie ses données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune donnée à renvoyer"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action']) && $_GET['action'] === 'getTotalCom') {
    // Récupération du total de commerciaux
    $totalCommerciaux = $commercial->getTotalCom();

    if ($totalCommerciaux > 0) {
        // Renvoi du total sous format JSON
        http_response_code(200);
        echo json_encode([$totalCommerciaux]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun commercial trouvé"]);
    }
}

//Visualisation des matricules commerciaux
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action']) && $_GET['action'] === 'readAllMatricule' ) {
    // Récupération des données
    $statement = $commercial->getAllMatriculeCom();

    if ($statement->rowCount() > 0) {
        $data = $statement->fetchAll();

        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune donnée à renvoyer"]);
    }
}

//Modification du mot de passe commmercial
elseif ($_SERVER['REQUEST_METHOD'] === "PUT" && isset($_GET['matriculeCom']) && isset($_GET['passwordCom'])) {
    // On récupère les infos envoyées
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $oldPassword = htmlspecialchars($_GET['passwordCom']);
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->passwordCom)) {
        $newPassword = htmlspecialchars($data->passwordCom);

        $credentialsVerified = $commercial->verifyComCredentials($matriculeCom, $oldPassword);

        $commercial->passwordCom = $data->passwordCom;
        
        if ($credentialsVerified) {
            // Mettre à jour le mot de passe
            $success = $commercial->updateComPwd($matriculeCom);

            if ($success) {
                http_response_code(201);
                echo json_encode(['message' => "Le mot de passe a été mis à jour avec succès"]);
            } else {
                http_response_code(300);
                echo json_encode(['message' => "La modification du mot de passe a échoué"]);
            }
        } else {
            http_response_code(300);
            echo json_encode(['message' => "Les informations d'identification sont incorrectes"]);
        }
    } else {
        echo json_encode(['message' => "Les données ne sont au complet"]);
    }
}

//Modification d'un commercial
elseif ($_SERVER['REQUEST_METHOD'] === "PUT" && isset($_GET['action']) && $_GET['action']==='updateCom') {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->matriculeCom) && !empty($data->nomCom) && !empty($data->prenomCom) && !empty($data->dateNaissCom) && !empty($data->telCom) && !empty($data->adresseCom) && !empty($data->emailCom) && !empty($data->usernameCom)) {
        // On hydrate l'objet commercial
        $commercial->matriculeCom = htmlspecialchars($data->matriculeCom);
        $commercial->nomCom = htmlspecialchars($data->nomCom);
        $commercial->prenomCom = htmlspecialchars($data->prenomCom);
        $commercial->dateNaissCom = htmlspecialchars($data->dateNaissCom);
        $commercial->telCom = htmlspecialchars($data->telCom);
        $commercial->adresseCom = htmlspecialchars($data->adresseCom);
        $commercial->emailCom = htmlspecialchars($data->emailCom);
        $commercial->usernameCom = htmlspecialchars($data->usernameCom);

        $result = $commercial->updateCom();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "Le Commercial a été modifié avec succès"]);
        } else {
            http_response_code(300);
            echo json_encode(['message' => "La modification du Commercial a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
}
