<?php 
class ConteneurPortefeuille
{
    public $table = "conteneurportefeuilles";
    private $connexion = null;

    public $idCon;
    public $idPor;
    public $idCli;
    public $dateAjoutCli;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createCon()
    {
        $sql = "INSERT INTO $this->table (idPor, idCli, dateAjoutCli) VALUES (:idPor, :idCli, :dateAjoutCli)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idPor" => $this->idPor,
            ":idCli" => $this->idCli,
            ":dateAjoutCli" => $this->dateAjoutCli
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function readCon()
    {
        $sql = "SELECT * FROM $this->table";
    	$req = $this->connexion->query($sql);
    	return $req;
    }

    public function updateCon()
    {
        $sql = "UPDATE $this->table SET idPor = :idPor, idCli = :idCli, dateAjoutCli = :dateAjoutCli WHERE idCon = :idCon";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([             ":idCon" => $this->idCon,
            ":idPor" => $this->idPor,
            ":idCli" => $this->idCli,
            ":dateAjoutCli" => $this->dateAjoutCli
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteCon()
    {
        $sql = "DELETE FROM $this->table WHERE idCon = :idCon";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idCon" => $this->idCon]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}

