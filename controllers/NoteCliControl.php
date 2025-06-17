<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/NoteClient.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet NoteClient
$noteClient = new NoteClient($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    // Ajout d'une note client
    if (
        !empty($data->idCli) &&
        !empty($data->matriculeCom) &&
        !empty($data->libelleNoteCli)
    ) {
        // On hydrate l'objet NoteClient
        $noteClient->idCli = htmlspecialchars($data->idCli);
        $noteClient->matriculeCom = htmlspecialchars($data->matriculeCom);
        $noteClient->libelleNoteCli = htmlspecialchars($data->libelleNoteCli);

        // Appel de la méthode pour créer une note client
        $result = $noteClient->createNoteCli();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "La note client a été ajoutée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout de la note client a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $noteClient->readNoteCli();

    if ($statement->rowCount() > 0) {
        // On récupère toutes les notes clients sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune note client trouvée"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idNoteCli) &&
        !empty($data->idCli) &&
        !empty($data->matriculeCom) &&
        !empty($data->libelleNoteCli)
    ) {
        // On hydrate l'objet NoteClient
        $noteClient->idNoteCli = htmlspecialchars($data->idNoteCli);
        $noteClient->idCli = htmlspecialchars($data->idCli);
        $noteClient->matriculeCom = htmlspecialchars($data->matriculeCom);
        $noteClient->libelleNoteCli = htmlspecialchars($data->libelleNoteCli);

        // Appel de la méthode pour mettre à jour la note client
        $result = $noteClient->updateNoteCli();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La note client a été mise à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour de la note client a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idNoteCli)) {
        // On hydrate l'objet NoteClient
        $noteClient->idNoteCli = htmlspecialchars($data->idNoteCli);

        // Appel de la méthode pour supprimer la note client
        $result = $noteClient->deleteNoteCli();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La note client a été supprimée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression de la note client a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant de la note client n'est pas spécifié"]);
    }
}
?>
