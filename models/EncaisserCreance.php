<?php
class EncaisserCreance
{
    private $table = "encaissercreances";
    private $connexion = null;

    public $idEnc;
    public $idCre;
    public $idCli;
    public $matriculeCom;
    public $dateEnc;
    public $montantEnc;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createEnc()
    {
        $sql = "INSERT INTO $this->table (idCre, idCli, matriculeCom, dateEnc, montantEnc) 
                VALUES (:idCre, :idCli, :matriculeCom, :dateEnc, :montantEnc)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idCre" => $this->idCre,
            ":idCli" => $this->idCli,
            ":matriculeCom" => $this->matriculeCom,
            ":dateEnc" => $this->dateEnc,
            ":montantEnc" => $this->montantEnc
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function readOwnEnc($matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE matriculeCom=:matriculeCom
                AND (MONTH(dateEnc) - MONTH(NOW())) <= 3
                AND YEAR(dateEnc) = YEAR(NOW())";
        
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":matriculeCom" => $matriculeCom
        ]);

        $result = $req->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getOwnTotalCours($matriculeCom)
    {
        $sql = "SELECT COUNT(*) AS nbreEnc, SUM(montantEnc) AS montantTotal FROM $this->table 
            WHERE matriculeCom = :matriculeCom 
            AND (MONTH(dateEnc) - MONTH(NOW())) <= 3
            AND YEAR(dateEnc) = YEAR(NOW())";

        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":matriculeCom" => $matriculeCom
        ]);

        $row = $req->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $data = [
                $row['nbreEnc'],
                $row['montantTotal'],
            ];
            return $data;
        } else {
            return null;
        }
    }

    // Méthode pour afficher les créances encaissées annuellement
    public function getCreancesEncaisseesAnnuelles($annee)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateEnc) = :annee";
        $req = $this->connexion->prepare($sql);
        $req->execute([":annee" => $annee]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnCreancesEncaisseesAnnuelles($annee, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateEnc) = :annee
                AND matriculeCom=:matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":matriculeCom"=> $matriculeCom
            ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour afficher les créances encaissées mensuellement
    public function getCreancesEncaisseesMensuelles($annee, $mois)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateEnc) = :annee AND MONTH(dateEnc) = :mois";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":mois" => $mois
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnCreancesEncaisseesMensuelles($annee, $mois, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateEnc) = :annee AND MONTH(dateEnc) = :mois
                AND matriculeCom= :matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":mois" => $mois,
            ":matriculeCom"=> $matriculeCom
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour afficher les créances encaissées périodiquement entre deux datesEnc
    public function getCreancesEncaisseesEntreDates($dateDebut, $dateFin)
    {
        $sql = "SELECT * FROM $this->table WHERE dateEnc BETWEEN :dateDebut AND :dateFin";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":dateDebut" => $dateDebut,
            ":dateFin" => $dateFin
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnCreancesEncaisseesEntreDates($dateDebut, $dateFin, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE dateEnc BETWEEN :dateDebut AND :dateFin
                AND matriculeCom= :matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":dateDebut" => $dateDebut,
            ":dateFin" => $dateFin,
            ":matriculeCom"=> $matriculeCom
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    public function readEnc()
    {
        $sql = "SELECT * FROM $this->table";
        $req = $this->connexion->query($sql);
        return $req;
    }

    public function updateEnc()
    {
        $sql = "UPDATE $this->table 
                SET idCre = :idCre, idCli = :idCli, matriculeCom = :matriculeCom, dateEnc = :dateEnc, 
                montantEnc = :montantEnc
                WHERE idEnc = :idEnc";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idEnc" => $this->idEnc,
            ":idCre" => $this->idCre,
            ":idCli" => $this->idCli,
            ":matriculeCom" => $this->matriculeCom,
            ":dateEnc" => $this->dateEnc,
            ":montantEnc" => $this->montantEnc
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteEnc()
    {
        $sql = "DELETE FROM $this->table WHERE idEnc = :idEnc";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idEnc" => $this->idEnc]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
