<?php
// Les entêtes requises
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset= UTF-8");

// Inclusion des fichiers requis
require_once '../config/Database.php';
require_once '../models/RecouvrementEcheance.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnexion();

// On instancie l'objet RecouvrementEcheance
$recouvrementEcheance = new RecouvrementEcheance($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    // Ajout d'un recouvrement d'échéance
    if (
        !empty($data->idCli) &&
        !empty($data->matriculeCom) &&
        !empty($data->idCre) &&
        !empty($data->datePrevuRec) &&
        !empty($data->montantRendu) &&
        !empty($data->soldeDebutMois) &&
        !empty($data->mensualiteRec)
    ) {
        // On hydrate l'objet RecouvrementEcheance
        $recouvrementEcheance->idCli = htmlspecialchars($data->idCli);
        $recouvrementEcheance->matriculeCom = htmlspecialchars($data->matriculeCom);
        $recouvrementEcheance->idCre = htmlspecialchars($data->idCre);
        $recouvrementEcheance->datePrevuRec = htmlspecialchars($data->datePrevuRec);
        $recouvrementEcheance->montantRendu = htmlspecialchars($data->montantRendu);
        $recouvrementEcheance->soldeDebutMois = htmlspecialchars($data->soldeDebutMois);
        $recouvrementEcheance->mensualiteRec = htmlspecialchars($data->mensualiteRec);

        // Appel de la méthode pour créer un recouvrement d'échéance
        $result = $recouvrementEcheance->createRec();
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => "Le recouvrement d'échéance a été ajouté avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "L'ajout du recouvrement d'échéance a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} 



// Contrôleur pour afficher les recouvrements d'échéances mensuellement
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['mois']) && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $annee = htmlspecialchars($_GET['annee']);
    $mois = htmlspecialchars($_GET['mois']);

    $recouvrementsMois = $recouvrementEcheance->getOwnRecouvrementsEcheancesMensuelles($annee, $mois, $matriculeCom);

    if (!empty($recouvrementsMois)) {
        // On renvoie les recouvrements sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($recouvrementsMois);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun recouvrement d'échéance trouvé pour l'année et le mois spécifiés pour ce matricule"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['mois'])) {
    $annee = htmlspecialchars($_GET['annee']);
    $mois = htmlspecialchars($_GET['mois']);

    // Récupération des recouvrements d'échéances pour l'année et le mois spécifiés
    $recouvrementsMois = $recouvrementEcheance->getRecouvrementsEcheancesMensuelles($annee, $mois);

    if (!empty($recouvrementsMois)) {
        // On renvoie les recouvrements sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($recouvrementsMois);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun recouvrement d'échéance trouvé pour l'année et le mois spécifiés"]);
    }
}

// Contrôleur pour afficher les recouvrements d'échéances annuellement
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee']) && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $annee = htmlspecialchars($_GET['annee']);

    // Récupération des recouvrements d'échéances pour l'année spécifiée
    $recouvrementsAnnee = $recouvrementEcheance->getOwnRecouvrementsEcheancesAnnuelles($annee, $matriculeCom);

    if (!empty($recouvrementsAnnee)) {
        // On renvoie les recouvrements sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($recouvrementsAnnee);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun recouvrement d'échéance trouvé pour l'année spécifiée pour ce matriucle"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['annee'])) {
    $annee = htmlspecialchars($_GET['annee']);

    // Récupération des recouvrements d'échéances pour l'année spécifiée
    $recouvrementsAnnee = $recouvrementEcheance->getRecouvrementsEcheancesAnnuelles($annee);

    if (!empty($recouvrementsAnnee)) {
        // On renvoie les recouvrements sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($recouvrementsAnnee);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun recouvrement d'échéance trouvé pour l'année spécifiée"]);
    }
}

// Contrôleur pour afficher les recouvrements d'échéances périodiquement entre deux dates
elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['dateDebut']) && isset($_GET['dateFin']) && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);
    $dateDebut = htmlspecialchars($_GET['dateDebut']);
    $dateFin = htmlspecialchars($_GET['dateFin']);

    $recouvrementsPeriode = $recouvrementEcheance->getRecouvrementsEcheancesEntreDates($dateDebut, $dateFin, $matriculeCom);

    if (!empty($recouvrementsPeriode)) {
        // On renvoie les recouvrements sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($recouvrementsPeriode);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun recouvrement d'échéance trouvé pour la période spécifiée pour ce matriucle"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['dateDebut']) && isset($_GET['dateFin'])) {
    $dateDebut = htmlspecialchars($_GET['dateDebut']);
    $dateFin = htmlspecialchars($_GET['dateFin']);

    // Récupération des recouvrements d'échéances entre les dates spécifiées
    $recouvrementsPeriode = $recouvrementEcheance->getRecouvrementsEcheancesEntreDates($dateDebut, $dateFin);

    if (!empty($recouvrementsPeriode)) {
        // On renvoie les recouvrements sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($recouvrementsPeriode);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun recouvrement d'échéance trouvé pour la période spécifiée"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['matriculeCom'])) {
    $matriculeCom = htmlspecialchars($_GET['matriculeCom']);

    // Récupération des recouvrements associés au matriculeCom
    $recouvrements = $recouvrementEcheance->readOwnRec($matriculeCom);

    if (!empty($recouvrements)) {
        // On renvoie les recouvrements sous forme de tableau JSON
        http_response_code(200);
        echo json_encode($recouvrements);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun recouvrement trouvé pour le matriculeCom spécifié"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Récupération des données
    $statement = $recouvrementEcheance->readRec();

    if ($statement->rowCount() > 0) {
        // On récupère tous les recouvrements d'échéance sous forme de tableau associatif
        $data = $statement->fetchAll();

        // On renvoie les données sous format json
        http_response_code(200);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Aucun recouvrement d'échéance trouvé"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->idRec) &&
        !empty($data->idCli) &&
        !empty($data->matriculeCom) &&
        !empty($data->idCre) &&
        !empty($data->datePrevuRec) &&
        !empty($data->montantRendu) &&
        !empty($data->soldeDebutMois) &&
        !empty($data->mensualiteRec)
    ) {
        // On hydrate l'objet RecouvrementEcheance
        $recouvrementEcheance->idRec = htmlspecialchars($data->idRec);
        $recouvrementEcheance->idCli = htmlspecialchars($data->idCli);
        $recouvrementEcheance->matriculeCom = htmlspecialchars($data->matriculeCom);
        $recouvrementEcheance->idCre = htmlspecialchars($data->idCre);
        $recouvrementEcheance->datePrevuRec = htmlspecialchars($data->datePrevuRec);
        $recouvrementEcheance->montantRendu = htmlspecialchars($data->montantRendu);
        $recouvrementEcheance->soldeDebutMois = htmlspecialchars($data->soldeDebutMois);
        $recouvrementEcheance->mensualiteRec = htmlspecialchars($data->mensualiteRec);

        // Appel de la méthode pour mettre à jour le recouvrement d'échéance
        $result = $recouvrementEcheance->updateRec();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Le recouvrement d'échéance a été mis à jour avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La mise à jour du recouvrement d'échéance a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Les données ne sont pas complètes"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // On récupère les infos envoyées
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->idRec)) {
        // On hydrate l'objet RecouvrementEcheance
        $recouvrementEcheance->idRec = htmlspecialchars($data->idRec);

        // Appel de la méthode pour supprimer le recouvrement d'échéance
        $result = $recouvrementEcheance->deleteRec();
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Le recouvrement d'échéance a été supprimé avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => "La suppression du recouvrement d'échéance a échoué"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => "L'identifiant du recouvrement d'échéance n'est pas spécifié"]);
    }
}
?>
