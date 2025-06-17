<?php
class NoteCommercial
{
    private $table = "notecommerciaux";
    private $connexion = null;

    public $idNoteCom;
    public $matriculeCom;
    public $idAdmin;
    public $nbreEtoile;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createNoteCom()
    {
        $sql = "INSERT INTO $this->table (matriculeCom, idAdmin, nbreEtoile) 
                VALUES (:matriculeCom, :idAdmin, :nbreEtoile)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":matriculeCom" => $this->matriculeCom,
            ":idAdmin" => $this->idAdmin,
            ":nbreEtoile" => $this->nbreEtoile
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function readNoteCom()
    {
        $sql = "SELECT * FROM $this->table";
        $req = $this->connexion->query($sql);
        return $req;
    }

    public function updateNoteCom()
    {
        $sql = "UPDATE $this->table 
                SET matriculeCom = :matriculeCom, idAdmin = :idAdmin, nbreEtoile = :nbreEtoile
                WHERE idNoteCom = :idNoteCom";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idNoteCom" => $this->idNoteCom,
            ":matriculeCom" => $this->matriculeCom,
            ":idAdmin" => $this->idAdmin,
            ":nbreEtoile" => $this->nbreEtoile
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteNoteCom()
    {
        $sql = "DELETE FROM $this->table WHERE idNoteCom = :idNoteCom";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idNoteCom" => $this->idNoteCom]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
