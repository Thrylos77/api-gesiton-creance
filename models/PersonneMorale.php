<?php
class PersonneMorale
{
    private $table = "personnemorales";
    private $connexion = null;

    public $idMor;
    public $idCli;
    public $sigleMor;
    public $siteWeb;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createMor()
    {
        $sql = "INSERT INTO $this->table (idCli, sigleMor, siteWeb) 
                VALUES (:idCli, :sigleMor, :siteWeb)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idCli" => $this->idCli,
            ":sigleMor" => $this->sigleMor,
            ":siteWeb" => $this->siteWeb
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function readMor()
    {
        $sql = "SELECT * FROM $this->table";
        $req = $this->connexion->query($sql);
        return $req;
    }

    public function updateMor()
    {
        $sql = "UPDATE $this->table 
                SET idCli = :idCli, sigleMor = :sigleMor, siteWeb = :siteWeb
                WHERE idMor = :idMor";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idMor" => $this->idMor,
            ":idCli" => $this->idCli,
            ":sigleMor" => $this->sigleMor,
            ":siteWeb" => $this->siteWeb
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteMor()
    {
        $sql = "DELETE FROM $this->table WHERE idMor = :idMor";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idMor" => $this->idMor]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
