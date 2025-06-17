<?php 
require_once 'VenteCredit.php';
require_once 'EncaisserCreance.php';

 class Creance
{
    private $table = "creances";
    private $connexion = null;

    public $idCre;
    public $idVen;
    public $dateDebutCre;
    public $dureePaiement;
    public $dateFinCre;
    public $mensualiteCre;
    public $modeReglement;
    public $soldeCre;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createCre()
    {
        $sql = "INSERT INTO $this->table (idVen, dateDebutCre, dureePaiement, dateFinCre, mensualiteCre, modeReglement, soldeCre) 
                VALUES (:idVen, :dateDebutCre, :dureePaiement, :dateFinCre, :mensualiteCre, :modeReglement, :soldeCre)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idVen" => $this->idVen,
            ":dateDebutCre" => $this->dateDebutCre,
            ":dureePaiement" => $this->dureePaiement,
            ":dateFinCre" => $this->dateFinCre,
            ":mensualiteCre" => $this->mensualiteCre,
            ":modeReglement" => $this->modeReglement,
            ":soldeCre" => $this->soldeCre
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function readOwnCre($matriculeCom)
    {
        $sql = "SELECT * FROM $this->table 
                WHERE idCre NOT IN
                    (SELECT idCre FROM encaissercreances) 
                    AND idVen IN (
                    SELECT idVen FROM ventecredits
                        WHERE matriculeCom = :matriculeCom
                    )";

        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":matriculeCom" => $matriculeCom
        ]);

        $result = $req->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    // Solde total des créances en cours pour un commercial
    public function getOwnTotalSoldeCours($matriculeCom)
    {
        $sql = "SELECT COUNT(*) AS nbreCre, SUM(soldeCre) AS SoldeTotal FROM $this->table WHERE 
                    idCre NOT IN
                    (SELECT idCre FROM encaissercreances )
                    AND idVen IN (
                    SELECT idVen FROM ventecredits
                        WHERE matriculeCom = :matriculeCom
                    )";

        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":matriculeCom" => $matriculeCom
        ]);

        $row = $req->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $data = [
                $row['nbreCre'],
                $row['SoldeTotal'],
            ];
            return $data;
        } else {
            return null;
        }
    }


    // Solde total des créances en cours
    public function getTotalSoldeCours()
    {
        $sql = "SELECT COUNT(*) AS nbreCre, SUM(soldeCre) AS SoldeTotal FROM $this->table WHERE 
                    idCre NOT IN
                    (SELECT idCre FROM encaissercreances 
                        )";

        $req = $this->connexion->query($sql);

        $row = $req->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $data = [
                $row['nbreCre'],
                $row['SoldeTotal'],
            ];
            return $data;
        } else {
            return null;
        }
    }

    // Lire les créances
    public function readCre()
    {
        $sql = "SELECT * FROM $this->table";

            $req = $this->connexion->query($sql);

            return $req;
    }



    // Obtenir le solde et la date de fin de créance
    public function getSoldeDateFinCre($idCre, $idCli)
    {
        $sql = "SELECT soldeCre, dateFinCre FROM $this->table WHERE idCre = :idCre
                AND idVen IN (
                    SELECT idVen FROM ventecredits WHERE idCli = :idCli)";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":idCli" => $idCli,
            ":idCre" => $idCre
        ]);

        $row = $req->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $data = [
                $row['soldeCre'],
            ];
            return $data;
        } else {
            return null;
        }
    }
    public function getSoldeDateFinCre2($idCre, $idCli)
    {
        $sql = "SELECT dateFinCre FROM $this->table WHERE idCre = :idCre
                AND idVen IN (
                    SELECT idVen FROM ventecredits WHERE idCli = :idCli)";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":idCli" => $idCli,
            ":idCre" => $idCre
        ]);

        $row = $req->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $data = [
                $row['dateFinCre'],
            ];
            return $data;
        } else {
            return null;
        }
    }

    //
    public function getIdCreByClientId($idCli)
    {
        $sql = "SELECT idCre FROM $this->table WHERE 
                idCre NOT IN (
                    SELECT idCre FROM encaissercreances )
                AND idVen IN (SELECT idVen FROM ventecredits WHERE idCli = :idCli
                )";

        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":idCli" => $idCli,
        ]);

        $rows = $req->fetchAll(PDO::FETCH_ASSOC);
        
        if ($rows) {
            $result = array_map(function($row) {
                return $row['idCre'];
            }, $rows);
            return $result; 
        }else {
            return null;
        }
    }


    // Pour voir l'Etat anuelles des créances
    public function getCreancesAnnuelles($annee)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateDebutCre) = :annee
                AND idVen IN (
                    SELECT idVen FROM ventecredits
                )";
        $req = $this->connexion->prepare($sql);
        $req->execute([":annee" => $annee]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnCreancesAnnuelles($annee, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateDebutCre) = :annee
                AND idVen IN (
                    SELECT idVen FROM ventecredits
                        WHERE matriculeCom = :matriculeCom
                    )";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":matriculeCom"=> $matriculeCom
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    // Pour voir l'Etat mensuelles des créances
    public function getCreancesMensuelles($annee, $mois)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateDebutCre) = :annee AND MONTH(dateDebutCre) = :mois
                AND idVen IN (
                    SELECT idVen FROM ventecredits
                )";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":mois" => $mois
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnCreancesMensuelles($annee, $mois, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(dateDebutCre) = :annee AND MONTH(dateDebutCre) = :mois
                AND idVen IN (
                    SELECT idVen FROM ventecredits
                        WHERE matriculeCom = :matriculeCom
                    )";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":mois" => $mois,
            ":matriculeCom"=> $matriculeCom
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    //Pour lire l'Etat périodique des créances

    public function getCreancesEntreDates($dateDebut, $dateFin)
    {
        $sql = "SELECT * FROM $this->table WHERE dateDebutCre BETWEEN :dateDebut AND :dateFin
                AND idVen IN (
                    SELECT idVen FROM ventecredits
                )";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":dateDebut" => $dateDebut,
            ":dateFin" => $dateFin
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnCreancesEntreDates($dateDebut, $dateFin, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE dateDebutCre BETWEEN :dateDebut AND :dateFin
                AND idVen IN (
                    SELECT idVen FROM ventecredits
                        WHERE matriculeCom = :matriculeCom
                    )";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":dateDebut" => $dateDebut,
            ":dateFin" => $dateFin,
            ":matriculeCom"=> $matriculeCom
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }



    public function updateCre()
    {
        $sql = "UPDATE $this->table 
                SET idVen = :idVen, dateDebutCre = :dateDebutCre, dureePaiement = :dureePaiement, 
                dateFinCre = :dateFinCre, mensualiteCre = :mensualiteCre, modeReglement = :modeReglement, 
                soldeCre = :soldeCre 
                WHERE idCre = :idCre";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idCre" => $this->idCre,
            ":idVen" => $this->idVen,
            ":dateDebutCre" => $this->dateDebutCre,
            ":dureePaiement" => $this->dureePaiement,
            ":dateFinCre" => $this->dateFinCre,
            ":mensualiteCre" => $this->mensualiteCre,
            ":modeReglement" => $this->modeReglement,
            ":soldeCre" => $this->soldeCre
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteCre()
    {
        $sql = "DELETE FROM $this->table WHERE idCre = :idCre";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idCre" => $this->idCre]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
