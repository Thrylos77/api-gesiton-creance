<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

require_once '../config/Database.php';
require_once '../models/Administrateur.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet administrateur
$administrateur = new Administrateur($db);

    // Authentification
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_GET['action']) && $_GET['action'] === 'auth') {

    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->usernameAdmin) && !empty($data->passwordAdmin)){
        $administrateur->usernameAdmin = htmlspecialchars($data->usernameAdmin);
        $administrateur->passwordAdmin = htmlspecialchars($data->passwordAdmin);


        $adminId = $administrateur->getAdminIdByUsernameAndPassword();

        if ($adminId) {
            http_response_code(200);
            echo json_encode([
                $adminId
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => "Identifiant ou mot de passe incorrect"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }  
}
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_GET['action']) && $_GET['action'] === 'add') {
    // On récupère les infos envoyées
    //Ajout d'un administrateur
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->usernameAdmin) && !empty($data->passwordAdmin)) {
        // On hydrate l'objet administrateur
        $administrateur->usernameAdmin = htmlspecialchars($data->usernameAdmin);
        $administrateur->passwordAdmin = htmlspecialchars($data->passwordAdmin);

        $result = $administrateur->createAdmin();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "L'administrateur a été ajouté avec sucès"]);
        } else {
            http_response_code(300);
            echo json_encode(['message' => "L'ajout de l'administrateur a échoué"]);
        }
    }
}
    


//Suppression d'administrateur
elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id)) {
        $administrateur->idAdmin = $data->idAdmin;
        if ($administrateur->deleteAdmin()) {
            http_response_code(200);
            echo json_encode(array("message" => "La suppression a été éffectué avec sucèss"));
        } else {
            http_response_code(300);
            echo json_encode(array("message" => "La suppression n'a été éffectué"));
        }
    } else {
        echo json_encode(['message' => "Vous devez precisé l'identifiant de l'administrateur"]);
    }
}

//Visualisation des administrateurs
elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $administrateur->readAdmin();

    if ($statement->rowCount() > 0) {
        $data = [];

        $data[] = $statement->fetchAll();


        // on renvoie ses données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        echo json_encode(["message" => "Aucune données à renvoyer"]);
    }
}

//Modification du mot de passe administrateur
elseif ($_SERVER['REQUEST_METHOD'] === "PUT" && isset($_GET['idAdmin']) && isset($_GET['passwordAdmin'])) {
    // On récupère les infos envoyées
    $idAdmin = htmlspecialchars($_GET['idAdmin']);
    $oldPassword = htmlspecialchars($_GET['passwordAdmin']);
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->passwordAdmin)) {
        // On hydrate l'objet administrateur
        $newPassword = htmlspecialchars($data->passwordAdmin);

        $credentialsVerified = $administrateur->verifyAdminCredentials($idAdmin, $oldPassword);

        $administrateur->passwordAdmin = $data->passwordAdmin;
        
        if ($credentialsVerified) {
            // Mettre à jour le mot de passe
            $success = $administrateur->updatePwd($idAdmin);

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