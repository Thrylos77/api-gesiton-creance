<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/Creance.php';
require_once '../models/VenteCredit.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet Creance
$creance = new Creance($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées

    // Ajout d'une créance
    $data = json_decode(file_get_contents("php://input"));
    if (
        !empty($data->idVen) &&
        !empty($data->dateDebutCre) &&
        !empty($data->dureePaiement) &&
        !empty($data->dateFinCre) &&
        !empty($data->mensualiteCre) &&
        !empty($data->modeReglement) &&
        !empty($data->soldeCre)
    ) {
        // On hydrate l'objet Creance
        $creance->idVen = htmlspecialchars($data->idVen);
        $creance->dateDebutCre = htmlspecialchars($data->dateDebutCre);
        $creance->dureePaiement = htmlspecialchars($data->dureePaiement);
        $creance->dateFinCre = htmlspecialchars($data->dateFinCre);
        $creance->mensualiteCre = htmlspecialchars($data->mensualiteCre);
        $creance->modeReglement = htmlspecialchars($data->modeReglement);
        $creance->soldeCre = htmlspecialchars($data->soldeCre);

        // Appel de la méthode pour créer une créance
        $result = $creance->createCre();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "La créance a été ajoutée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout de la créance a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} 

// Affichage du solde total des créances en cours pour un commercial
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom']) && isset($_GET['action']) && $_GET['action'] === 'getOwnTotalSoldeCours') {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération du solde total
    $montant = $creance->getOwnTotalSoldeCours($matriculeCom);

    if ($montant) {
        http_response_code(200);
        echo json_encode(
                $montant
            );
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun montant à renvoyer"]);
    }
}


// Affichage du solde total des créances en cours pour un commercial
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action']) && $_GET['action'] === 'getTotalSoldeCours') {

    // Récupération du solde total
    $res = $creance->getTotalSoldeCours();

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

// Contrôleur pour afficher les créances mensuellement
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['mois']) && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $annee = htmlspecialchars($_GET['annee']);
    $mois = htmlspecialchars($_GET['mois']);

    $creancesMois = $creance->getOwnCreancesMensuelles($annee, $mois, $matriculeCom);

    if (!empty($creancesMois)) {
        http_response_code(200);
        echo json_encode($creancesMois);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune creance trouvée pour l'année et le mois spécifiés pour ce matricule"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['idCre']) && isset($_GET['idCli']) && isset($_GET['action']) && $_GET['action'] === 'getSoldeDateFinCre') {
    $idCli = htmlspecialchars($_GET['idCli']);
    $idCre = htmlspecialchars($_GET['idCre']);

    // Récupération du montantCreance associé à l'idVen
    $res = $creance->getSoldeDateFinCre($idCre, $idCli);

    // On renvoie le montantCreance sous forme de valeur numérique
    if ($res>0) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($res);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune donnée à renvoyer"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['idCre']) && isset($_GET['idCli']) && isset($_GET['action']) && $_GET['action'] === 'getSoldeDateFinCre2') {
    $idCli = htmlspecialchars($_GET['idCli']);
    $idCre = htmlspecialchars($_GET['idCre']);

    // Récupération du montantCreance associé à l'idVen
    $res = $creance->getSoldeDateFinCre2($idCre, $idCli);

    // On renvoie le montantCreance sous forme de valeur numérique
    if ($res>0) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($res);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune donnée à renvoyer"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['idCli']) && isset($_GET['action']) && $_GET['action'] === 'getIdCreByClientId') {
    $idCli = htmlspecialchars($_GET['idCli']);

    // Récupération du montantCreance associé à l'idVen
    $res = $creance->getIdCreByClientId($idCli);

    // On renvoie le montantCreance sous forme de valeur numérique
    if ($res>0) {
        // On renvoie les ventes sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($res);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune donnée à renvoyer"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['mois'])) {
    $annee = htmlspecialchars($_GET['annee']);
    $mois = htmlspecialchars($_GET['mois']);

    $creancesMois = $creance->getCreancesMensuelles($annee, $mois);

    if (!empty($creancesMois)) {
        http_response_code(200);
        echo json_encode($creancesMois);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance trouvée pour l'année et le mois spécifiés"]);
    }
}

// Contrôleur pour afficher les créances annuellement
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $annee = htmlspecialchars($_GET['annee']);

    // Récupération des créances pour l'année spécifiée
    $creancesAnnee = $creance->getOwnCreancesAnnuelles($annee, $matriculeCom);

    if (!empty($creancesAnnee)) {
        http_response_code(200);
        echo json_encode($creancesAnnee);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance trouvée pour l'année spécifiée pour ce matriucle"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee'])) {
    $annee = htmlspecialchars($_GET['annee']);

    $creancesAnnee = $creance->getCreancesAnnuelles($annee);

    if (!empty($creancesAnnee)) {
        http_response_code(200);
        echo json_encode($creancesAnnee);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance trouvée pour l'année spécifiée"]);
    }
}


// Contrôleur pour afficher les créances périodiquement entre deux dates
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['dateDebut']) && isset($_GET['dateFin']) && isset($_GET['matriculeCom'])) 
{
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $dateDebut = htmlspecialchars($_GET['dateDebut']);
    $dateFin = htmlspecialchars($_GET['dateFin']);

    $creancesPeriode = $creance->getCreancesEntreDates($dateDebut, $dateFin, $matriculeCom);

    if (!empty($creancesPeriode)) {
        http_response_code(200);
        echo json_encode($creancesPeriode);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créances trouvée pour la période spécifiée pour ce matriucle"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['dateDebut']) && isset($_GET['dateFin'])) {
    $dateDebut = htmlspecialchars($_GET['dateDebut']);
    $dateFin = htmlspecialchars($_GET['dateFin']);

    $creancesPeriode = $creance->getCreancesEntreDates($dateDebut, $dateFin);

    if (!empty($creancesPeriode)) {
        http_response_code(200);
        echo json_encode($creancesPeriode);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créances trouvée pour la période spécifiée"]);
    }
}

// Affichage des créances effectuées par un commercial spécifique
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération des creances associées au matriculeCom
    $creances = $creance->readOwnCre($matriculeCom);

    if (!empty($creances)) {
        // On renvoie les creances sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($creances);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune créance trouvée pour le matriculeCom spécifié"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET") {

    // Récupération des données
    $statement = $creance->readCre();

    if ($statement->rowCount() > 0) {
        // On récupère toutes les créances sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucune creance trouvé"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idCre) &&
        !empty($data->idVen) &&
        !empty($data->dateDebutCre) &&
        !empty($data->dureePaiement) &&
        !empty($data->dateFinCre) &&
        !empty($data->mensualiteCre) &&
        !empty($data->modeReglement) &&
        !empty($data->soldeCre)
    ) {
        // On hydrate l'objet Creance
        $creance->idCre = htmlspecialchars($data->idCre);
        $creance->idVen = htmlspecialchars($data->idVen);
        $creance->dateDebutCre = htmlspecialchars($data->dateDebutCre);
        $creance->dureePaiement = htmlspecialchars($data->dureePaiement);
        $creance->dateFinCre = htmlspecialchars($data->dateFinCre);
        $creance->mensualiteCre = htmlspecialchars($data->mensualiteCre);
        $creance->modeReglement = htmlspecialchars($data->modeReglement);
        $creance->soldeCre = htmlspecialchars($data->soldeCre);

        // Appel de la méthode pour mettre à jour la créance
        $result = $creance->updateCre();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La créance a été mise à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour de la créance a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idCre)) {
        // On hydrate l'objet Creance
        $creance->idCre = htmlspecialchars($data->idCre);

        // Appel de la méthode pour supprimer la créance
        $result = $creance->deleteCre();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "La créance a été supprimée avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression de la créance a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant de la créance n'est pas spécifié"]);
    }
}
?>
