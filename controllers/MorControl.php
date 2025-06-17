<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/PersonneMorale.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet PersonneMorale
$personneMorale = new PersonneMorale($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées

    // Ajout d'une personne morale
    $data = json_decode(file_get_contents("php://input"));
    if (
        !empty($data->idCli) &&
        !empty($data->sigleMor) &&
        !empty($data->siteWeb)
    ) {
        // On hydrate l'objet PersonneMorale
        $personneMorale->idCli = htmlspecialchars($data->idCli);
        $personneMorale->sigleMor = htmlspecialchars($data->sigleMor);
        $personneMorale->siteWeb = htmlspecialchars($data->siteWeb);

        // Appel de la méthode pour créer une personne morale
        $result = $personneMorale->createMor();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "La personne morale a été ajoutée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout de la personne morale a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $personneMorale->readMor();

    if ($statement->rowCount() > 0) {
        // On récupère toutes les personnes morales sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune personne morale trouvée"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idMor) &&
        !empty($data->idCli) &&
        !empty($data->sigleMor) &&
        !empty($data->siteWeb)
    ) {
        // On hydrate l'objet PersonneMorale
        $personneMorale->idMor = htmlspecialchars($data->idMor);
        $personneMorale->idCli = htmlspecialchars($data->idCli);
        $personneMorale->sigleMor = htmlspecialchars($data->sigleMor);
        $personneMorale->siteWeb = htmlspecialchars($data->siteWeb);

        // Appel de la méthode pour mettre à jour la personne morale
        $result = $personneMorale->updateMor();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La personne morale a été mise à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour de la personne morale a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idMor)) {
        // On hydrate l'objet PersonneMorale
        $personneMorale->idMor = htmlspecialchars($data->idMor);

        // Appel de la méthode pour supprimer la personne morale
        $result = $personneMorale->deleteMor();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La personne morale a été supprimée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression de la personne morale a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant de la personne morale n'est pas spécifié"]);
    }
}
?>
