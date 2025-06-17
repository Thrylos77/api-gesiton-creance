<?php
class Portefeuille
{
    protected $table = "portefeuille";
    private $connexion = null;

    public $idPor;
    public $matriculeCom;
    public $nomPor;
    public $dateCreation;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createPor()
    {
        $sql = "INSERT INTO $this->table (matriculeCom, nomPor, dateCreation) 
                VALUES (:matriculeCom, :nomPor, :dateCreation)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":matriculeCom" => $this->matriculeCom,
            ":nomPor" => $this->nomPor,
            ":dateCreation" => $this->dateCreation
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function getTotalPor()
    {
        $sql = "SELECT COUNT(*) AS totalPor FROM $this->table";

        $req = $this->connexion->query($sql);
        $result = $req->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $totalPor = $result['totalPor'];
            return $totalPor;
        } else {
            return 0; // Ou une autre valeur par défaut en cas d'échec
        }
    }

    public function readPor()
    {
        $sql = "SELECT * FROM $this->table";
        $req = $this->connexion->query($sql);
        return $req;
    }

    public function updatePor()
    {
        $sql = "UPDATE $this->table 
                SET matriculeCom = :matriculeCom, nomPor = :nomPor, dateCreation = :dateCreation
                WHERE idPor = :idPor";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idPor" => $this->idPor,
            ":matriculeCom" => $this->matriculeCom,
            ":nomPor" => $this->nomPor,
            ":dateCreation" => $this->dateCreation
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deletePor()
    {
        $sql = "DELETE FROM $this->table WHERE idPor = :idPor";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idPor" => $this->idPor]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
