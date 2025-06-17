<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/NoteCommercial.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet NoteCommercial
$noteCommercial = new NoteCommercial($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    // Ajout d'une note commerciale
    if (
        !empty($data->matriculeCom) &&
        !empty($data->idAdmin) &&
        isset($data->nbreEtoile)
    ) {
        // On hydrate l'objet NoteCommercial
        $noteCommercial->matriculeCom = htmlspecialchars($data->matriculeCom);
        $noteCommercial->idAdmin = htmlspecialchars($data->idAdmin);
        $noteCommercial->nbreEtoile = intval($data->nbreEtoile);

        // Appel de la méthode pour créer une note commerciale
        $result = $noteCommercial->createNoteCom();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "La note commerciale a été ajoutée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout de la note commerciale a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $noteCommercial->readNoteCom();

    if ($statement->rowCount() > 0) {
        // On récupère toutes les notes commerciales sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune note commerciale trouvée"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idNoteCom) &&
        !empty($data->matriculeCom) &&
        !empty($data->idAdmin) &&
        isset($data->nbreEtoile)
    ) {
        // On hydrate l'objet NoteCommercial
        $noteCommercial->idNoteCom = htmlspecialchars($data->idNoteCom);
        $noteCommercial->matriculeCom = htmlspecialchars($data->matriculeCom);
        $noteCommercial->idAdmin = htmlspecialchars($data->idAdmin);
        $noteCommercial->nbreEtoile = intval($data->nbreEtoile);

        // Appel de la méthode pour mettre à jour la note commerciale
        $result = $noteCommercial->updateNoteCom();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La note commerciale a été mise à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour de la note commerciale a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idNoteCom)) {
        // On hydrate l'objet NoteCommercial
        $noteCommercial->idNoteCom = htmlspecialchars($data->idNoteCom);

        // Appel de la méthode pour supprimer la note commerciale
        $result = $noteCommercial->deleteNoteCom();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La note commerciale a été supprimée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression de la note commerciale a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant de la note commerciale n'est pas spécifié"]);
    }
}
?>
