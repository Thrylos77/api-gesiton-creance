<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/EncaisserCreance.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet EncaisserCreance
$encaisserCreance = new EncaisserCreance($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées

    // Ajout d'une encaisse de créance
    $data = json_decode(file_get_contents("php://input"));
    if (
        !empty($data->idCre) &&
        !empty($data->idCli) &&
        !empty($data->matriculeCom) &&
        !empty($data->dateEnc) &&
        !empty($data->montantEnc)
    ) {
        // On hydrate l'objet EncaisserCreance
        $encaisserCreance->idCre = htmlspecialchars($data->idCre);
        $encaisserCreance->idCli = htmlspecialchars($data->idCli);
        $encaisserCreance->matriculeCom = htmlspecialchars($data->matriculeCom);
        $encaisserCreance->dateEnc = htmlspecialchars($data->dateEnc);
        $encaisserCreance->montantEnc = htmlspecialchars($data->montantEnc);

        // Appel de la méthode pour créer une encaisse de créance
        $result = $encaisserCreance->createEnc();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "L'encaisse de créance a été ajoutée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout de l'encaisse de créance a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
}
// Affichage du montant total des créances en cours pour un commercial
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom']) && isset($_GET['action']) && $_GET['action'] === 'getOwnTotalCours') {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération du solde total
    $res = $encaisserCreance->getOwnTotalCours($matriculeCom);

    if ($res) {
        http_response_code(200);
        echo json_encode(
                $res
            );
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun montant à renvoyer"]);
    }
}

// Contrôleur pour afficher les créances encaissées mensuellement
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['mois']) && isset($_GET['matriculeCom'])) {
    $annee = htmlspecialchars($_GET['annee']);
    $mois = htmlspecialchars($_GET['mois']);
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération des créances encaissées pour l'année et le mois spécifiés
    $creancesMensuelles = $encaisserCreance->getOwnCreancesEncaisseesMensuelles($annee, $mois, $matriculeCom);

    if (!empty($creancesMensuelles)) {
        // On renvoie les créances sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($creancesMensuelles);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance encaissée trouvée pour l'année et le mois spécifiés pour ce matricule"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['mois'])) {
    $annee = htmlspecialchars($_GET['annee']);
    $mois = htmlspecialchars($_GET['mois']);

    // Récupération des créances encaissées pour l'année et le mois spécifiés
    $creancesMensuelles = $encaisserCreance->getCreancesEncaisseesMensuelles($annee, $mois);

    if (!empty($creancesMensuelles)) {
        // On renvoie les créances sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($creancesMensuelles);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance encaissée trouvée pour l'année et le mois spécifiés"]);
    }
}

// Contrôleur pour afficher les créances encaissées annuellement

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $annee = htmlspecialchars($_GET['annee']);

    // Récupération des créances encaissées pour l'année spécifiée
    $creancesAnnuelles = $encaisserCreance->getOwnCreancesEncaisseesAnnuelles($annee, $matriculeCom);

    if (!empty($creancesAnnuelles)) {
        // On renvoie les créances sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($creancesAnnuelles);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance encaissée trouvée pour l'année spécifiée pour ce matriucle"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee'])) {
    $annee = htmlspecialchars($_GET['annee']);

    // Récupération des créances encaissées pour l'année spécifiée
    $creancesAnnuelles = $encaisserCreance->getCreancesEncaisseesAnnuelles($annee);

    if (!empty($creancesAnnuelles)) {
        // On renvoie les créances sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($creancesAnnuelles);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance encaissée trouvée pour l'année spécifiée"]);
    }
}



// Contrôleur pour afficher les créances encaissées périodiquement entre deux datesEnc
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['dateDebut']) && isset($_GET['dateFin'])&& isset($_GET['matriculeCom'])) 
{

    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $dateDebut = htmlspecialchars($_GET['dateDebut']);
    $dateFin = htmlspecialchars($_GET['dateFin']);

    // Récupération des créances encaissées entre les datesEnc spécifiées
    $creancesPeriode = $encaisserCreance->getOwnCreancesEncaisseesEntreDates($dateDebut, $dateFin, $matriculeCom);

    if (!empty($creancesPeriode)) {
        // On renvoie les créances sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($creancesPeriode);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance encaissée trouvée pour la période spécifiée pour ce matriucle"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['dateDebut']) && isset($_GET['dateFin'])) {
    $dateDebut = htmlspecialchars($_GET['dateDebut']);
    $dateFin = htmlspecialchars($_GET['dateFin']);

    // Récupération des créances encaissées entre les datesEnc spécifiées
    $creancesPeriode = $encaisserCreance->getCreancesEncaisseesEntreDates($dateDebut, $dateFin);

    if (!empty($creancesPeriode)) {
        // On renvoie les créances sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($creancesPeriode);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance encaissée trouvée pour la période spécifiée"]);
    }
}



elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération des encaissements associés au matriculeCom
    $encaissements = $encaisserCreance->readOwnEnc($matriculeCom);

    if (!empty($encaissements)) {
        // On renvoie les encaissements sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($encaissements);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun encaissement trouvé pour le matriculeCom spécifié"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $encaisserCreance->readEnc();

    if ($statement->rowCount() > 0) {
        // On récupère toutes les encaisses de créance sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune encaisse de créance trouvée"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idEnc) &&
        !empty($data->idCre) &&
        !empty($data->idCli) &&
        !empty($data->matriculeCom) &&
        !empty($data->dateEnc) &&
        !empty($data->montantEnc)
    ) {
        // On hydrate l'objet EncaisserCreance
        $encaisserCreance->idEnc = htmlspecialchars($data->idEnc);
        $encaisserCreance->idCre = htmlspecialchars($data->idCre);
        $encaisserCreance->idCli = htmlspecialchars($data->idCli);
        $encaisserCreance->matriculeCom = htmlspecialchars($data->matriculeCom);
        $encaisserCreance->dateEnc = htmlspecialchars($data->dateEnc);
        $encaisserCreance->montantEnc = htmlspecialchars($data->montantEnc);

        // Appel de la méthode pour mettre à jour l'encaisse de créance
        $result = $encaisserCreance->updateEnc();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "L'encaisse de créance a été mise à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour de l'encaisse de créance a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idEnc)) {
        // On hydrate l'objet EncaisserCreance
        $encaisserCreance->idEnc = htmlspecialchars($data->idEnc);

        // Appel de la méthode pour supprimer l'encaisse de créance
        $result = $encaisserCreance->deleteEnc();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "L'encaisse de créance a été supprimée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression de l'encaisse de créance a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant de l'encaisse de créance n'est pas spécifié"]);
    }
}
?>
