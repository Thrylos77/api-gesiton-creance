<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/ConteneurPortefeuille.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet ConteneurPortefeuille
$conteneurPortefeuille = new ConteneurPortefeuille($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées

    // Ajout d'un conteneur de portefeuille
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->idPor) && !empty($data->idCli) && !empty($data->dateAjoutCli)) {
        // On hydrate l'objet conteneur de portefeuille
        $conteneurPortefeuille->idPor = htmlspecialchars($data->idPor);
        $conteneurPortefeuille->idCli = htmlspecialchars($data->idCli);
        $conteneurPortefeuille->dateAjoutCli = htmlspecialchars($data->dateAjoutCli);

        // Appel de la méthode pour créer un conteneur de portefeuille
        $result = $conteneurPortefeuille->createCon();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "Le conteneur de portefeuille a été ajouté avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout du conteneur de portefeuille a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $conteneurPortefeuille->readCon();

    if ($statement->rowCount() > 0) {
        // On récupère tous les conteneurs de portefeuille sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun conteneur de portefeuille trouvé"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idCon) && !empty($data->idPor) && !empty($data->idCli) && !empty($data->dateAjoutCli)) {
        // On hydrate l'objet conteneur de portefeuille
        $conteneurPortefeuille->idCon = htmlspecialchars($data->idCon);
        $conteneurPortefeuille->idPor = htmlspecialchars($data->idPor);
        $conteneurPortefeuille->idCli = htmlspecialchars($data->idCli);
        $conteneurPortefeuille->dateAjoutCli = htmlspecialchars($data->dateAjoutCli);

        // Appel de la méthode pour mettre à jour le conteneur de portefeuille
        $result = $conteneurPortefeuille->updateCon();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Le conteneur de portefeuille a été mis à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour du conteneur de portefeuille a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idCon)) {
        // On hydrate l'objet conteneur de portefeuille
        $conteneurPortefeuille->idCon = htmlspecialchars($data->idCon);

        // Appel de la méthode pour supprimer le conteneur de portefeuille
        $result = $conteneurPortefeuille->deleteCon();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Le conteneur de portefeuille a été supprimé avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression du conteneur de portefeuille a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant du conteneur de portefeuille n'est pas spécifié"]);
    }
}
?>
