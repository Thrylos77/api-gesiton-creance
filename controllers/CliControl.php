<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/Client.php';
require_once '../models/Portefeuille.php';
require_once '../models/ConteneurPortefeuille.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet Client
$client = new Client($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées

    // Ajout d'un client
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->nomCli) && !empty($data->telCli) && !empty($data->matriculeCom) && !empty($data->adresseCli) && !empty($data->emailCli) && !empty($data->typeCli)) {
        // On hydrate l'objet client
        $client->nomCli = htmlspecialchars($data->nomCli);
        $client->matriculeCom = htmlspecialchars($data->matriculeCom);
        $client->telCli = htmlspecialchars($data->telCli);
        $client->adresseCli = htmlspecialchars($data->adresseCli);
        $client->emailCli = htmlspecialchars($data->emailCli);
        $client->typeCli = htmlspecialchars($data->typeCli);

        // Appel de la méthode pour créer un client
        $result = $client->createCli();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "Le client a été ajouté avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout du client a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action']) && $_GET['action'] === 'readAllIdcli') {

    $statement = $client->getAllIdcli();

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

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action']) && $_GET['action'] === 'getTotalCli') {
    // Récupération du total de clients
    $totalcli = $client->getTotalCli();

    if ($totalcli > 0) {
        // Renvoi du total sous format JSON
        http_response_code(200);
        echo json_encode([$totalcli]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun client trouvé"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom']) &&  isset($_GET['action']) && $_GET['action'] === 'getOwnTotalCli') {
    // Récupération du total de clients
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $totalcli = $client->getOwnTotalCli($matriculeCom);

    if ($totalcli > 0) {
        // Renvoi du total sous format JSON
        http_response_code(200);
        echo json_encode([$totalcli]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun client trouvé"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération des clients associés au portefeuille
    $clients = $client->readOwnCli($matriculeCom);

    if (!empty($clients)) {
        // On renvoie les clients sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($clients);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun client trouvé pour le matriculeCom spécifié"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" &&  isset($_GET['action']) && $_GET['action'] === 'readAll') {
    // Récupération des données
    $statement = $client->readCli();

    if ($statement->rowCount() > 0) {
        // On récupère tous les clients sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun client trouvé"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idCli) && !empty($data->nomCli) && !empty($data->telCli) && !empty($data->adresseCli) && !empty($data->emailCli) && !empty($data->typeCli) && !empty($data->matriculeCom)) {
        // On hydrate l'objet client
        $client->idCli = htmlspecialchars($data->idCli);
        $client->nomCli = htmlspecialchars($data->nomCli);
        $client->telCli = htmlspecialchars($data->telCli);
        $client->adresseCli = htmlspecialchars($data->adresseCli);
        $client->emailCli = htmlspecialchars($data->emailCli);
        $client->typeCli = htmlspecialchars($data->typeCli);
        $client->matriculeCom = htmlspecialchars($data->matriculeCom);

        // Appel de la méthode pour mettre à jour le client
        $result = $client->updateCli();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Le client a été mis à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour du client a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idCli)) {
        // On hydrate l'objet client
        $client->idCli = htmlspecialchars($data->idCli);

        // Appel de la méthode pour supprimer le client
        $result = $client->deleteCli();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Le client a été supprimé avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression du client a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant du client n'est pas spécifié"]);
    }
}
