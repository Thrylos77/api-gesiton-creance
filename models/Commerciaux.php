<?php 
require_once 'Portefeuille.php';

class Commerciaux
{
    private $table = "commerciaux";
    private $connexion = null;

    public $matriculeCom;
    public $nomCom;
    public $prenomCom;
    public $dateNaissCom;
    public $telCom;
    public $adresseCom;
    public $emailCom;
    public $usernameCom;
    public $passwordCom;
    
    public function __construct($db)
    {
        $this->connexion = $db;
    }
    
    public function readCom()
    {
        $sql = "SELECT matriculeCom, nomCom, prenomCom, 
                        dateNaissCom, telCom, adresseCom, 
                        emailCom, usernameCom
                FROM $this->table ";

        $req = $this->connexion->query($sql);

        return $req;
    }

    public function getMatriculeComByEmailAndPassword()
    {
        $sql = "SELECT matriculeCom, passwordCom FROM $this->table WHERE BINARY usernameCom=:usernameCom";

        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":usernameCom" => $this->usernameCom
        ]);

        $result = $req->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($this->passwordCom, $result['passwordCom'])) {
            return $result['matriculeCom'];
        } else {
            return null;
        }
    }

    public function getAllMatriculeCom()
    {
        $sql = "SELECT matriculeCom, nomCom FROM $this->table 
                WHERE matriculeCom NOT IN (
                    SELECT matriculeCom FROM portefeuille
                )";

        $req = $this->connexion->query($sql);

        return $req;
    }

    public function getTotalCom()
    {
        $sql = "SELECT COUNT(*) AS totalCommerciaux FROM $this->table";

        $req = $this->connexion->query($sql);
        $result = $req->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $totalCommerciaux = $result['totalCommerciaux'];
            return $totalCommerciaux;
        } else {
            return 0; // Ou une autre valeur par défaut en cas d'échec
        }
    }

    public function getComByMatricule($matriculeCom)
    {
        $sql = "SELECT matriculeCom, nomCom, prenomCom, 
                        dateNaissCom, telCom, adresseCom, 
                        emailCom, usernameCom
                FROM $this->table
                WHERE matriculeCom = :matriculeCom";

        $req = $this->connexion->prepare($sql);
        $req->execute([":matriculeCom" => $matriculeCom]);

        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result;
    }


    public function readComByMatricule()
    {
        $sql = "SELECT matriculeCom, nomCom, prenomCom, 
                        dateNaissCom, telCom, adresseCom, 
                        emailcCom, usernameCom
                FROM $this->table WHERE matriculeCom=:matriculeCom";

        $req = $this->connexion->prepare($sql);
            $re = $req->execute([
            ":matriculeCom" => $this->matriculeCom,
        ]);

        return $req;
    }


    public function createCom()
    {
        $hashedPassword = password_hash($this->passwordCom, PASSWORD_DEFAULT);

        $sql = "INSERT INTO $this->table (matriculeCom, nomCom,prenomCom, dateNaissCom, telCom, adresseCom, emailCom, usernameCom, passwordCom) VALUES (:matriculeCom, :nomCom, :prenomCom, :dateNaissCom, :telCom, :adresseCom, :emailCom, :usernameCom, :passwordCom)";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":matriculeCom" => $this->matriculeCom,
            ":nomCom" => $this->nomCom,
            ":prenomCom" => $this->prenomCom,
            ":dateNaissCom" => $this->dateNaissCom,
            ":telCom" => $this->telCom,
            ":adresseCom" => $this->adresseCom,
            ":emailCom" => $this->emailCom,
            ":usernameCom" => $this->usernameCom,
            ":passwordCom" => $hashedPassword

        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function updateCom()
    {
        $sql = "UPDATE $this->table SET nomCom = :nomCom, 
            prenomCom = :prenomCom,
            dateNaissCom = :dateNaissCom,
            telCom = :telCom,
            adresseCom = :adresseCom,
            emailCom = :emailCom,
            usernameCom = :usernameCom
            WHERE
            matriculeCom = :matriculeCom";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":nomCom" => $this->nomCom,
            ":prenomCom" => $this->prenomCom,
            ":dateNaissCom" => $this->dateNaissCom,
            ":telCom" => $this->telCom,
            ":matriculeCom" => $this->matriculeCom,
            ":adresseCom" => $this->adresseCom,
            ":usernameCom" => $this->usernameCom,
            ":emailCom" => $this->emailCom
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function updateComPwd($matriculeCom)
    {
        $hashedPassword = password_hash($this->passwordCom, PASSWORD_DEFAULT);

        $sql = "UPDATE $this->table SET passwordCom = :passwordCom  
                WHERE matriculeCom = :matriculeCom ";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":passwordCom" => $hashedPassword,
            ":matriculeCom" => $matriculeCom
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        } 
    }


    public function verifyComCredentials($matriculeCom, $oldPassword) {

        $sql = "SELECT passwordCom FROM commerciaux WHERE matriculeCom = :matriculeCom";
        $req = $this->connexion->prepare($sql);
        $req->execute([":matriculeCom" => $matriculeCom]);

        $row = $req->fetch(PDO::FETCH_ASSOC);

        if (password_verify($oldPassword, $row['passwordCom'])) {
            return true; // Les informations d'identification sont correctes
        } else {
            return false; // Les informations d'identification sont incorrectes
        }
    }

    public function deleteCom()
    {
        $sql = "DELETE FROM $this->table WHERE matriculeCom = :matriculeCom";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([":matriculeCom" => $this->matriculeCom]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }
}
?>