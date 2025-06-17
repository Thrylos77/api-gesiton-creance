<?php
class NoteClient
{
    private $table = "noteclients";
    private $connexion = null;

    public $idNoteCli;
    public $idCli;
    public $matriculeCom;
    public $libelleNoteCli;

    public function __construct($db)
    {
        if ($this->connexion == null) {
            $this->connexion = $db;
        }
    }

    public function createNoteCli()
    {
        $sql = "INSERT INTO $this->table (idCli, matriculeCom, libelleNoteCli) 
                VALUES (:idCli, :matriculeCom, :libelleNoteCli)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idCli" => $this->idCli,
            ":matriculeCom" => $this->matriculeCom,
            ":libelleNoteCli" => $this->libelleNoteCli
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function readNoteCli()
    {
        $sql = "SELECT * FROM $this->table";
        $req = $this->connexion->query($sql);
        return $req;
    }

    public function updateNoteCli()
    {
        $sql = "UPDATE $this->table 
                SET idCli = :idCli, matriculeCom = :matriculeCom, libelleNoteCli = :libelleNoteCli
                WHERE idNoteCli = :idNoteCli";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":idNoteCli" => $this->idNoteCli,
            ":idCli" => $this->idCli,
            ":matriculeCom" => $this->matriculeCom,
            ":libelleNoteCli" => $this->libelleNoteCli
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteNoteCli()
    {
        $sql = "DELETE FROM $this->table WHERE idNoteCli = :idNoteCli";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":idNoteCli" => $this->idNoteCli]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
