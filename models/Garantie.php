<?php
class Garantie
{
    private $table = "garanties";
    private $connexion = null;

    public $idGar;
    public $libelleGar;
    public $dureeGar;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createGar()
    {
        $sql = "INSERT INTO $this->table (libelleGar, dureeGar) 
                VALUES (:libelleGar, :dureeGar)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":libelleGar" => $this->libelleGar,
            ":dureeGar" => $this->dureeGar
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function readGar()
    {
        $sql = "SELECT * FROM $this->table";
        $req = $this->connexion->query($sql);
        return $req;
    }

    public function updateGar()
    {
        $sql = "UPDATE $this->table 
                SET libelleGar = :libelleGar, dureeGar = :dureeGar
                WHERE idGar = :idGar";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idGar" => $this->idGar,
            ":libelleGar" => $this->libelleGar,
            ":dureeGar" => $this->dureeGar
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteGar()
    {
        $sql = "DELETE FROM $this->table WHERE idGar = :idGar";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idGar" => $this->idGar]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
