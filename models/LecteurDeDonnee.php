<?php 
class LecteurDeDonnee
{
	private $table = "lecteurdedonnees";
    private $connexion = null;

    public $idLec;
    public $usernameLec;
    public $passwordLec;

    public function __construct($db)
    {
        $this->connexion = $db;
    }

    public function readCom2()
    {
        $sql = "SELECT idLec, passwordLec FROM $this->table WHERE BINARY usernameLec=:usernameLec";

        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":usernameLec" => $this->usernameLec
        ]);

        $re = $req->fetch(PDO::FETCH_ASSOC);

        if ($re && password_verify($this->passwordLec, $re['passwordLec'])) {
            return $re['idLec'];
        } else {
            return null;
        }
    }

    public function updateLecPwd($idLec)
    {
        $hashedPassword = password_hash($this->passwordLec, PASSWORD_DEFAULT);

        $sql = "UPDATE $this->table SET passwordLec =:passwordLec  
            WHERE
            idLec =:idLec ";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":passwordLec" => $hashedPassword,
            ":idLec" => $idLec
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyLecCredentials($idLec, $oldPassword) {

        $sql = "SELECT passwordLec FROM lecteurdedonnees WHERE idLec = :idLec";
        $req = $this->connexion->prepare($sql);
        $req->execute([":idLec" => $idLec]);

        $row = $req->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($oldPassword, $row['passwordLec'])) {
            return true; // Les informations d'identification sont correctes
        } else {
            return false; // Les informations d'identification sont incorrectes
        }
    }

}