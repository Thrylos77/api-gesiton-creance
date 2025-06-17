<?php

class RecouvrementEcheance
{
    private $table = "recouvrementecheances";
    private $connexion = null;

    public $idRec;
    public $idCli;
    public $matriculeCom;
    public $idCre;
    public $datePaiement;
    public $soldeDebutMois;
    public $mensualiteRec;
    public $montantRendu;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createRec()
    {
        $sql = "INSERT INTO $this->table (idCli, matriculeCom, idCre, datePaiement, soldeDebutMois, mensualiteRec, montantRendu) 
                VALUES (:idCli, :matriculeCom, :idCre, :datePaiement, :soldeDebutMois, :mensualiteRec, :montantRendu)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idCli" => $this->idCli,
            ":matriculeCom" => $this->matriculeCom,
            ":idCre" => $this->idCre,
            ":datePaiement" => $this->datePaiement,
            ":soldeDebutMois" => $this->soldeDebutMois,
            ":mensualiteRec" => $this->mensualiteRec,
            ":montantRendu" => $this->montantRendu
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
    public function readOwnRec($matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE matriculeCom=:matriculeCom";
        
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":matriculeCom" => $matriculeCom
        ]);

        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getRecouvrementsEcheancesAnnuelles($annee)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(datePaiement) = :annee";
        $req = $this->connexion->prepare($sql);
        $req->execute([":annee" => $annee]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnRecouvrementsEcheancesAnnuelles($annee, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(datePaiement) = :annee
                AND matriculeCom=:matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":matriculeCom"=> $matriculeCom
                    ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour afficher les recouvrements d'échéances mensuellement
    public function getRecouvrementsEcheancesMensuelles($annee, $mois)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(datePaiement) = :annee AND MONTH(datePaiement) = :mois";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":mois" => $mois
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnRecouvrementsEcheancesMensuelles($annee, $mois, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE YEAR(datePaiement) = :annee AND MONTH(datePaiement) = :mois
                AND matriculeCom= :matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":annee" => $annee,
            ":mois" => $mois,
            ":matriculeCom"=> $matriculeCom
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour afficher les recouvrements d'échéances périodiquement entre deux dates
    public function getRecouvrementsEcheancesEntreDates($dateDebut, $dateFin)
    {
        $sql = "SELECT * FROM $this->table WHERE datePaiement BETWEEN :dateDebut AND :dateFin";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":dateDebut" => $dateDebut,
            ":dateFin" => $dateFin
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnRecouvrementsEcheancesEntreDates($dateDebut, $dateFin, $matriculeCom)
    {
        $sql = "SELECT * FROM $this->table WHERE datePaiement BETWEEN :dateDebut AND :dateFin
                AND matriculeCom= :matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":dateDebut" => $dateDebut,
            ":dateFin" => $dateFin,
            ":matriculeCom"=> $matriculeCom
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readRec()
    {
        $sql = "SELECT * FROM $this->table";
        $req = $this->connexion->query($sql);
        return $req;
    }

    public function updateRec()
    {
        $sql = "UPDATE $this->table 
                SET idCli = :idCli, matriculeCom = :matriculeCom, idCre = :idCre,
                datePaiement = :datePaiement, soldeDebutMois = :soldeDebutMois, mensualiteRec = :mensualiteRec, montantRendu = :montantRendu
                WHERE idRec = :idRec";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idRec" => $this->idRec,
            ":idCli" => $this->idCli,
            ":matriculeCom" => $this->matriculeCom,
            ":idCre" => $this->idCre,
            ":montantRendu" => $this->montantRendu,
            ":datePaiement" => $this->datePaiement,
            ":soldeDebutMois" => $this->soldeDebutMois,
            ":mensualiteRec" => $this->mensualiteRec
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteRec()
    {
        $sql = "DELETE FROM $this->table WHERE idRec = :idRec";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idRec" => $this->idRec]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
