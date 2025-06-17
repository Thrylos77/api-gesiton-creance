<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/PersonnePhysique.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet PersonnePhysique
$personnePhysique = new PersonnePhysique($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    // Ajout d'une personne physique
    if (
        !empty($data->idCli) &&
        !empty($data->prenomPhy) &&
        !empty($data->dateNaissPhy)
    ) {
        // On hydrate l'objet PersonnePhysique
        $personnePhysique->idCli = htmlspecialchars($data->idCli);
        $personnePhysique->prenomPhy = htmlspecialchars($data->prenomPhy);
        $personnePhysique->dateNaissPhy = htmlspecialchars($data->dateNaissPhy);

        // Appel de la méthode pour créer une personne physique
        $result = $personnePhysique->createPhy();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "La personne physique a été ajoutée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout de la personne physique a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $personnePhysique->readPhy();

    if ($statement->rowCount() > 0) {
        // On récupère toutes les personnes physiques sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune personne physique trouvée"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idPhy) &&
        !empty($data->idCli) &&
        !empty($data->prenomPhy) &&
        !empty($data->dateNaissPhy)
    ) {
        // On hydrate l'objet PersonnePhysique
        $personnePhysique->idPhy = htmlspecialchars($data->idPhy);
        $personnePhysique->idCli = htmlspecialchars($data->idCli);
        $personnePhysique->prenomPhy = htmlspecialchars($data->prenomPhy);
        $personnePhysique->dateNaissPhy = htmlspecialchars($data->dateNaissPhy);

        // Appel de la méthode pour mettre à jour la personne physique
        $result = $personnePhysique->updatePhy();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La personne physique a été mise à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour de la personne physique a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idPhy)) {
        // On hydrate l'objet PersonnePhysique
        $personnePhysique->idPhy = htmlspecialchars($data->idPhy);

        // Appel de la méthode pour supprimer la personne physique
        $result = $personnePhysique->deletePhy();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La personne physique a été supprimée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression de la personne physique a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant de la personne physique n'est pas spécifié"]);
    }
}
?>
