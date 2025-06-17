<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

require_once '../config/Database.php';
require_once '../models/LecteurDeDonnee.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet commerciaux
$lec = new LecteurDeDonnee($db);

//Authentification
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->usernameLec) && !empty($data->passwordLec)) {
        $lec->usernameLec = htmlspecialchars($data->usernameLec);
        $lec->passwordLec = htmlspecialchars($data->passwordLec);

        $res = $lec->readCom2();

        if ($res) {
            http_response_code(200);
            echo json_encode([
                $res
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

//Modification du mot de passe
elseif ($_SERVER['REQUEST_METHOD'] === "PUT" && isset($_GET['idLec']) && isset($_GET['passwordLec'])) {
    // On récupère les infos envoyées
    $idLec = htmlspecialchars($_GET['idLec']);
    $oldPassword = htmlspecialchars($_GET['passwordLec']);
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->passwordLec)) {
        $newPassword = htmlspecialchars($data->passwordLec);

        $credentialsVerified = $lec->verifyLecCredentials($idLec, $oldPassword);

        $lec->passwordLec = $data->passwordLec;
        
        if ($credentialsVerified) {
            // Mettre à jour le mot de passe
            $success = $lec->updateLecPwd($idLec);

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