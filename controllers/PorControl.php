<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/Portefeuille.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet Portefeuille
$portefeuille = new Portefeuille($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    // Ajout d'un portefeuille
    if (
        !empty($data->matriculeCom) &&
        !empty($data->nomPor) &&
        !empty($data->dateCreation)
    ) {
        // On hydrate l'objet Portefeuille
        $portefeuille->matriculeCom = htmlspecialchars($data->matriculeCom);
        $portefeuille->nomPor = htmlspecialchars($data->nomPor);
        $portefeuille->dateCreation = htmlspecialchars($data->dateCreation);

        // Appel de la méthode pour créer un portefeuille
        $result = $portefeuille->createPor();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "Le portefeuille a été ajouté avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout du portefeuille a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action']) && $_GET['action'] === 'getTotalPor') {
    // Récupération du total de portefeuille
    $total = $portefeuille->getTotalPor();

    if ($total > 0) {
        // Renvoi du total sous format JSON
        http_response_code(200);
        echo json_encode([$total]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun portefeuille trouvé"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $portefeuille->readPor();

    if ($statement->rowCount() > 0) {
        // On récupère tous les portefeuilles sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun portefeuille trouvé"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idPor) &&
        !empty($data->matriculeCom) &&
        !empty($data->nomPor) &&
        !empty($data->dateCreation)
    ) {
        // On hydrate l'objet Portefeuille
        $portefeuille->idPor = htmlspecialchars($data->idPor);
        $portefeuille->matriculeCom = htmlspecialchars($data->matriculeCom);
        $portefeuille->nomPor = htmlspecialchars($data->nomPor);
        $portefeuille->dateCreation = htmlspecialchars($data->dateCreation);

        // Appel de la méthode pour mettre à jour le portefeuille
        $result = $portefeuille->updatePor();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Le portefeuille a été mis à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour du portefeuille a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idPor)) {
        // On hydrate l'objet Portefeuille
        $portefeuille->idPor = htmlspecialchars($data->idPor);

        // Appel de la méthode pour supprimer le portefeuille
        $result = $portefeuille->deletePor();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Le portefeuille a été supprimé avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression du portefeuille a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant du portefeuille n'est pas spécifié"]);
    }
}
?>
