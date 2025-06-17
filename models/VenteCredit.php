<?php
require_once 'Creance.php';
require_once 'EncaisserCreance.php';

class VenteCredit
{
    private $table = "ventecredits";
    private $connexion = null;

    public $idVen;
    public $matriculeCom;
    public $idCli;
    public $libelleGar;
    public $dateSignContrat;
    public $dateVen;
    public $libelleVehicule;
    public $montantTotal;
    public $acompte;
    public $docJustificatif;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function readOwnVen($matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE matriculeCom=:matriculeCom
                AND idVen NOT IN ( SELECT idVen FROM creances WHERE
                    idCre IN (
                        SELECT idCre FROM encaissercreances))";
        $req = $this->connexion->prepare($sql);
        $re = $req->execute([":matriculeCom" => $matriculeCom]);

        $result = $req->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function readOwnIdVen($matriculeCom)
    {
        $sql = "SELECT idVen, matriculeCom, idCli FROM $this->table 
                WHERE matriculeCom=:matriculeCom AND idVen NOT IN (
                    SELECT idVen FROM creances
                        WHERE matriculeCom = :matriculeCom
                    )";
        $req = $this->connexion->prepare($sql);
        $req->execute([":matriculeCom" => $matriculeCom]);

        $result = $req->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getTotalVen()
    {
        $sql = "SELECT COUNT(*) AS totalVen, SUM(montantTotal) AS MontantTotal, SUM(acompte) AS TotalAcompte FROM $this->table";

        $req = $this->connexion->query($sql);
        $result = $req->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $data = [
                $result['totalVen'],
                $result['MontantTotal'],
                $result['TotalAcompte']
            ];
            return $data;
        } else {
            return 0; // Ou une autre valeur par défaut en cas d'échec
        }
    }

    // Obetnir les propres montant de ventes

    public function getOwnTotalVen($matriculeCom)
    {
        $sql = "SELECT COUNT(*) AS totalVen, SUM(montantTotal) AS MontantTotal, SUM(acompte) AS        TotalAcompte FROM $this->table WHERE matriculeCom=:matriculeCom 
                AND idVen NOT IN ( SELECT idVen FROM creances WHERE
                    idCre IN (
                        SELECT idCre FROM encaissercreances))";

        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":matriculeCom"=> $matriculeCom
        ]);

        $result = $req->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            /*$data = [
                $result['totalVen'],
                $result['MontantTotal'],
                $result['TotalAcompte']
            ];*/
            return $result;
        } else {
            return 0; // Ou une autre valeur par défaut en cas d'échec
        }
    }

    public function getMontantCreance($idVen)
    {
        $sql = "SELECT (montantTotal-acompte) AS montantCre FROM $this->table WHERE idVen = :idVen";
        $req = $this->connexion->prepare($sql);
        $req->execute([":idVen" => $idVen]);

        $row = $req->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row['montantCre'];
        } else { 
            return null;
        }
    }

    public function getVentesCreditAnnuelles($annee)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateVen) = :annee";
        $req = $this->connexion->prepare($sql);
        $req->execute([":annee" => $annee]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnVentesCreditAnnuelles($annee, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateVen) = :annee
                AND matriculeCom=:matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":matriculeCom"=> $matriculeCom
                    ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVentesCreditMensuelles($annee, $mois)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateVen) = :annee AND MONTH(dateVen) = :mois";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":mois" => $mois
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnVentesCreditMensuelles($annee, $mois, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateVen) = :annee AND MONTH(dateVen) = :mois
                AND matriculeCom= :matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":mois" => $mois,
            ":matriculeCom"=> $matriculeCom
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVentesCreditEntreDates($dateDebut, $dateFin)
    {
        $sql = "SELECT * FROM $this->table WHERE dateVen BETWEEN :dateDebut AND :dateFin";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":dateDebut" => $dateDebut,
            ":dateFin" => $dateFin
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnVentesCreditEntreDates($dateDebut, $dateFin, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE dateVen BETWEEN :dateDebut AND :dateFin
                AND matriculeCom= :matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":dateDebut" => $dateDebut,
            ":dateFin" => $dateFin,
            ":matriculeCom"=> $matriculeCom
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createVen()
    {
        $sql = "INSERT INTO $this->table (matriculeCom, idCli, libelleGar, dateSignContrat, dateVen, 
                  libelleVehicule, montantTotal, acompte, docJustificatif) 
                VALUES (:matriculeCom, :idCli, :libelleGar, :dateSignContrat, :dateVen, :libelleVehicule, 
                  :montantTotal, :acompte, :docJustificatif)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":matriculeCom" => $this->matriculeCom,
            ":idCli" => $this->idCli,
            ":libelleGar" => $this->libelleGar,
            ":dateSignContrat" => $this->dateSignContrat,
            ":dateVen" => $this->dateVen,
            ":libelleVehicule" => $this->libelleVehicule,
            ":montantTotal" => $this->montantTotal,
            ":acompte" => $this->acompte,
            ":docJustificatif" => $this->docJustificatif
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function readVen()
    {
        $sql = "SELECT * FROM $this->table";
        $req = $this->connexion->query($sql);
        return $req;
    }



    public function updateVen()
    {
        $sql = "UPDATE $this->table 
                SET matriculeCom = :matriculeCom, idCli = :idCli, libelleGar = :libelleGar, 
                dateSignContrat = :dateSignContrat, dateVen = :dateVen, libelleVehicule = :libelleVehicule, 
                montantTotal = :montantTotal, acompte = :acompte, docJustificatif = :docJustificatif
                WHERE idVen = :idVen";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idVen" => $this->idVen,
            ":matriculeCom" => $this->matriculeCom,
            ":idCli" => $this->idCli,
            ":libelleGar" => $this->libelleGar,
            ":dateSignContrat" => $this->dateSignContrat,
            ":dateVen" => $this->dateVen,
            ":libelleVehicule" => $this->libelleVehicule,
            ":montantTotal" => $this->montantTotal,
            ":acompte" => $this->acompte,
            ":docJustificatif" => $this->docJustificatif
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteVen()
    {
        $sql = "DELETE FROM $this->table WHERE idVen = :idVen";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idVen" => $this->idVen]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
