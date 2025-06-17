<?php
class PersonnePhysique
{
    private $table = "personnephysiques";
    private $connexion = null;

    public $idPhy;
    public $idCli;
    public $prenomPhy;
    public $dateNaissPhy;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createPhy()
    {
        $sql = "INSERT INTO $this->table (idCli, prenomPhy, dateNaissPhy) 
                VALUES (:idCli, :prenomPhy, :dateNaissPhy)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idCli" => $this->idCli,
            ":prenomPhy" => $this->prenomPhy,
            ":dateNaissPhy" => $this->dateNaissPhy
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function readPhy()
    {
        $sql = "SELECT * FROM $this->table";
        $req = $this->connexion->query($sql);
        return $req;
    }

    public function updatePhy()
    {
        $sql = "UPDATE $this->table 
                SET idCli = :idCli, prenomPhy = :prenomPhy, dateNaissPhy = :dateNaissPhy
                WHERE idPhy = :idPhy";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idPhy" => $this->idPhy,
            ":idCli" => $this->idCli,
            ":prenomPhy" => $this->prenomPhy,
            ":dateNaissPhy" => $this->dateNaissPhy
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deletePhy()
    {
        $sql = "DELETE FROM $this->table WHERE idPhy = :idPhy";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idPhy" => $this->idPhy]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
