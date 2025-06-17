<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/VenteCredit.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet VenteCredit
$venteCredit = new VenteCredit($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    // Ajout d'une vente à crédit
    if (
        !empty($data->matriculeCom) &&
        !empty($data->idCli) &&
        !empty($data->libelleGar) &&
        !empty($data->dateSignContrat) &&
        !empty($data->dateVen) &&
        !empty($data->libelleVehicule) &&
        !empty($data->montantTotal) &&
        !empty($data->acompte) &&
        !empty($data->docJustificatif)
    ) {
        // On hydrate l'objet VenteCredit
        $venteCredit->matriculeCom = htmlspecialchars($data->matriculeCom);
        $venteCredit->idCli = htmlspecialchars($data->idCli);
        $venteCredit->libelleGar = htmlspecialchars($data->libelleGar);
        $venteCredit->dateSignContrat = htmlspecialchars($data->dateSignContrat);
        $venteCredit->dateVen = htmlspecialchars($data->dateVen);
        $venteCredit->libelleVehicule = htmlspecialchars($data->libelleVehicule);
        $venteCredit->montantTotal = htmlspecialchars($data->montantTotal);
        $venteCredit->acompte = htmlspecialchars($data->acompte);
        $venteCredit->docJustificatif = htmlspecialchars($data->docJustificatif);

        // Appel de la méthode pour créer une vente à crédit
        $result = $venteCredit->createVen();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "La vente à crédit a été ajoutée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout de la vente à crédit a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom']) && isset($_GET['action']) && $_GET['action'] === 'readIdVen') {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération des ventes associées au matriculeCom
    $ventes = $venteCredit->readOwnIdVen($matriculeCom);

    if (!empty($ventes)) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($ventes);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente trouvée pour le matriculeCom spécifié"]);
    }
}

// pour récupérer le montant de créance qui est le montantTotal-acompte
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['idVen']) && isset($_GET['action']) && $_GET['action'] === 'getMontantCreance') {
    $idVen = htmlspecialchars($_GET['idVen']);

    // Récupération du montantCreance associé à l'idVen
    $montantCreance = $venteCredit->getMontantCreance($idVen);

    // On renvoie le montantCreance sous forme de valeur numérique
    if ($montantCreance) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode([
                $montantCreance
            ]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun montant à renvoyer"]);
    }
}

// Contrôleur pour afficher les ventes à crédit mensuelles
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['mois']) &&
        isset($_GET['matriculeCom'])) 
{
    $annee = htmlspecialchars($_GET['annee']);
    $mois = htmlspecialchars($_GET['mois']);
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération des ventes à crédit pour l'année et le mois spécifiés
    $ventesMois = $venteCredit->getOwnVentesCreditMensuelles($annee, $mois, $matriculeCom);

    if (!empty($ventesMois)) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($ventesMois);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente trouvée pour l'année et le mois spécifiés pour ce matricule"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action']) && $_GET['action'] === 'getTotalVen') {

    $totalVen = $venteCredit->getTotalVen();

    if ($totalVen > 0) {
        // Renvoi du total sous format JSON
        http_response_code(200);
        echo json_encode([$totalVen]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente à credit trouvé"]);
    }
}

// Obetnir ses propes montant total de ventes et d'acomptes
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action']) && isset($_GET['matriculeCom']) && $_GET['action'] === 'getOwnTotalVen') {

    $matricule = htmlspecialchars($_GET['matriculeCom']);

    $totalVen = $venteCredit->getOwnTotalVen($matricule);

    if ($totalVen > 0) {
        // Renvoi du total sous format JSON
        http_response_code(200);
        echo json_encode([$totalVen]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente à credit trouvé"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['mois'])) {
    $annee = htmlspecialchars($_GET['annee']);
    $mois = htmlspecialchars($_GET['mois']);

    // Récupération des ventes à crédit pour l'année et le mois spécifiés
    $ventesMois = $venteCredit->getVentesCreditMensuelles($annee, $mois);

    if (!empty($ventesMois)) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($ventesMois);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente trouvée pour l'année et le mois spécifiés"]);
    }
}

// Contrôleur pour afficher les ventes à crédit annuelles
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['matriculeCom'])) {
    $annee = htmlspecialchars($_GET['annee']);
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    $ventesAnnee = $venteCredit->getOwnVentesCreditAnnuelles($annee, $matriculeCom);

    if (!empty($ventesAnnee)) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($ventesAnnee);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente trouvée pour l'année spécifiée pour ce matricule"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee'])) {
    $annee = htmlspecialchars($_GET['annee']);

    // Récupération des ventes à crédit pour l'année spécifiée
    $ventesAnnee = $venteCredit->getVentesCreditAnnuelles($annee);

    if (!empty($ventesAnnee)) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($ventesAnnee);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente trouvée pour l'année spécifiée "]);
    }
}

// Contrôleur pour afficher les ventes à crédit périodiquement entre deux datesVen
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['dateDebut']) && isset($_GET['dateFin']) && isset($_GET['matriculeCom'])) {
    $dateDebut = htmlspecialchars($_GET['dateDebut']);
    $dateFin = htmlspecialchars($_GET['dateFin']);
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    $ventesPeriode = $venteCredit->getOwnVentesCreditEntreDates($dateDebut, $dateFin, $matriculeCom);

    if (!empty($ventesPeriode)) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($ventesPeriode);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente trouvée pour la période spécifiée pour ce matricule"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['dateDebut']) && isset($_GET['dateFin'])) {
    $dateDebut = htmlspecialchars($_GET['dateDebut']);
    $dateFin = htmlspecialchars($_GET['dateFin']);

    // Récupération des ventes à crédit entre les datesVen spécifiées
    $ventesPeriode = $venteCredit->getVentesCreditEntreDates($dateDebut, $dateFin);

    if (!empty($ventesPeriode)) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($ventesPeriode);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente trouvée pour la période spécifiée"]);
    }
}

// Affichage des ventes effectuées par un commercial spécifique
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom']) && isset($_GET['action'])  && $_GET['action'] === 'readOwnVen') {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération des ventes associées au matriculeCom
    $ventes = $venteCredit->readOwnVen($matriculeCom);

    if (!empty($ventes)) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($ventes);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente trouvée pour le matriculeCom spécifié"]);
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action'])  && $_GET['action'] === 'readAll') {
    // Récupération des données
    $statement = $venteCredit->readVen();

    if ($statement->rowCount() > 0) {
        // On récupère toutes les ventes à crédit sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune vente à crédit trouvée"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idVen) &&
        !empty($data->matriculeCom) &&
        !empty($data->idCli) &&
        !empty($data->libelleGar) &&
        !empty($data->dateSignContrat) &&
        !empty($data->dateVen) &&
        !empty($data->libelleVehicule) &&
        !empty($data->montantTotal) &&
        !empty($data->acompte) &&
        !empty($data->docJustificatif)
    ) {
        // On hydrate l'objet VenteCredit
        $venteCredit->idVen = htmlspecialchars($data->idVen);
        $venteCredit->matriculeCom = htmlspecialchars($data->matriculeCom);
        $venteCredit->idCli = htmlspecialchars($data->idCli);
        $venteCredit->libelleGar = htmlspecialchars($data->libelleGar);
        $venteCredit->dateSignContrat = htmlspecialchars($data->dateSignContrat);
        $venteCredit->dateVen = htmlspecialchars($data->dateVen);
        $venteCredit->libelleVehicule = htmlspecialchars($data->libelleVehicule);
        $venteCredit->montantTotal = htmlspecialchars($data->montantTotal);
        $venteCredit->acompte = htmlspecialchars($data->acompte);
        $venteCredit->docJustificatif = htmlspecialchars($data->docJustificatif);

        // Appel de la méthode pour mettre à jour la vente à crédit
        $result = $venteCredit->updateVen();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La vente à crédit a été mise à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour de la vente à crédit a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idVen)) {
        // On hydrate l'objet VenteCredit
        $venteCredit->idVen = htmlspecialchars($data->idVen);

        // Appel de la méthode pour supprimer la vente à crédit
        $result = $venteCredit->deleteVen();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La vente à crédit a été supprimée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression de la vente à crédit a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant de la vente à crédit n'est pas spécifié"]);
    }
}
?>
