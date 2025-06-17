<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/Garantie.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet Garantie
$garantie = new Garantie($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées

    // Ajout d'une garantie
    $data = json_decode(file_get_contents("php://input"));
    if (
        !empty($data->libelleGar) &&
        !empty($data->dureeGar)
    ) {
        // On hydrate l'objet Garantie
        $garantie->libelleGar = htmlspecialchars($data->libelleGar);
        $garantie->dureeGar = htmlspecialchars($data->dureeGar);

        // Appel de la méthode pour créer une garantie
        $result = $garantie->createGar();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "La garantie a été ajoutée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout de la garantie a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $garantie->readGar();

    if ($statement->rowCount() > 0) {
        // On récupère toutes les garanties sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune garantie trouvée"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idGar) &&
        !empty($data->libelleGar) &&
        !empty($data->dureeGar)
    ) {
        // On hydrate l'objet Garantie
        $garantie->idGar = htmlspecialchars($data->idGar);
        $garantie->libelleGar = htmlspecialchars($data->libelleGar);
        $garantie->dureeGar = htmlspecialchars($data->dureeGar);

        // Appel de la méthode pour mettre à jour la garantie
        $result = $garantie->updateGar();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La garantie a été mise à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour de la garantie a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idGar)) {
        // On hydrate l'objet Garantie
        $garantie->idGar = htmlspecialchars($data->idGar);

        // Appel de la méthode pour supprimer la garantie
        $result = $garantie->deleteGar();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La garantie a été supprimée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression de la garantie a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant de la garantie n'est pas spécifié"]);
    }
}
?>
