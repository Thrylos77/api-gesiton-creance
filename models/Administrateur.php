<?php 
    class Administrateur 
    {
        private $table = "administrateurs";
        private $connexion = null;

        public $idAdmin;
        public $usernameAdmin;
        public $passwordAdmin;


        public function __construct($db)
        {
            if ($this->connexion == null) {
                $this->connexion = $db;
            }
        }
        public function readAdmin()
        {
            $sql = "SELECT idAdmin, usernameAdmin FROM $this->table";

            $req = $this->connexion->query($sql);

            return $req;
        }

    public function getAdminIdByUsernameAndPassword()
    {
        $sql = "SELECT idAdmin, passwordAdmin FROM $this->table WHERE BINARY usernameAdmin=:usernameAdmin";

        $req = $this->connexion->prepare($sql);
        $req->execute([
            ":usernameAdmin" => $this->usernameAdmin,
        ]);

        $result = $req->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($this->passwordAdmin, $result['passwordAdmin'])) {
            return $result['idAdmin'];
        } else {
            return null;
        }
    }

    public function updatePwd($idAdmin)
    {
        $hashedPassword = password_hash($this->passwordAdmin, PASSWORD_DEFAULT);

        $sql = "UPDATE $this->table SET passwordAdmin =:passwordAdmin  
            WHERE
            idAdmin =:idAdmin ";
        $req = $this->connexion->prepare($sql);

        $re = $req->execute([
            ":passwordAdmin" => $hashedPassword,
            ":idAdmin" => $idAdmin
        ]);

        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyAdminCredentials($idAdmin, $oldPassword) {

        $sql = "SELECT passwordAdmin FROM administrateurs WHERE idAdmin = :idAdmin";
        $req = $this->connexion->prepare($sql);
        $req->execute([":idAdmin" => $idAdmin]);

        $row = $req->fetch(PDO::FETCH_ASSOC);

        if (password_verify($oldPassword, $row['passwordAdmin'])) {
            return true; // Les informations d'identification sont correctes
        } else {
            return false; // Les informations d'identification sont incorrectes
        }
    }

    public function getAdminById($idAdmin)
    {
        $sql = "SELECT idAdmin, usernameAdmin FROM $this->table WHERE idAdmin=:idAdmin";

        $req = $this->connexion->prepare($sql);
        $req->execute([":idAdmin" => $idAdmin]);

        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

        public function createAdmin()
        {
            $hashedPassword = password_hash($this->passwordAdmin, PASSWORD_DEFAULT);

            $sql = "INSERT INTO $this->table(usernameAdmin,passwordAdmin) VALUES(:usernameAdmin,:passwordAdmin)";

            $req = $this->connexion->prepare($sql);
            // éxecution de la reqête
            $re = $req->execute([
            ":usernameAdmin" => $this->usernameAdmin,
            ":passwordAdmin" => $this->$hashedPassword
            ]);
            if ($re) {
                return true;
            } else {
                return false;
            }
        }

        
    
        public function deleteAdmin()
        {
            $sql = "DELETE FROM $this->table WHERE idAdmin = :idAdmin";
            $req = $this->connexion->prepare($sql);
    
            $re = $req->execute(array(":idAdmin" => $this->idAdmin));
    
            if ($re) {
                return true;
            } else {
                return false;
            }
        }

        //Authentification
        public function readAdmin2()
        {
            $sql = "SELECT idAdmin, usernameAdmin, passwordAdmin FROM $this->table WHERE usernameAdmin=:usernameAdmin AND passwordAdmin=:passwordAdmin";

            $req = $this->connexion->prepare($sql);
                $re = $req->execute([
                ":usernameAdmin" => $this->usernameAdmin,
                ":passwordAdmin" => $this->passwordAdmin,
            ]);

            if ($req->rowCount() > 0) {
                return true;
            }else{
                return false;
            }
        }
        
    }
?>