
<?php 
require_once '../models/Portefeuille.php';
require_once '../models/ConteneurPortefeuille.php';

    class Client 
    {
        private $table = "clients";
        private $connexion = null;

        public $idCli;
        public $nomCli;
        public $matriucleCom;
        public $telCli;
        public $adresseCli;
        public $emailCli;
        public $typeCli;

        public function __construct($db)
        {
            if ($this->connexion == null) {
                $this->connexion = $db;
            }
        }

        public function createCli()
        {
            $sql = "INSERT INTO $this->table(nomCli,matriculeCom,telCli,adresseCli,emailCli,typeCli) VALUES(:nomCli,:matriculeCom,:telCli,:adresseCli,:emailCli,:typeCli)";

            $req = $this->connexion->prepare($sql);
            // éxecution de la reqête
            $re = $req->execute([
            ":nomCli" => $this->nomCli,
            ":matriculeCom" =>$this->matriculeCom,
            ":telCli" => $this->telCli,
            ":adresseCli" => $this->adresseCli,
            ":emailCli" => $this->emailCli,
            ":typeCli" => $this->typeCli
            ]);

            if ($re) {
                return true;
            } else {
                return false;
            }
        }

        public function getTotalCli()
        {
            $sql = "SELECT COUNT(*) AS totalCli FROM $this->table";

            $req = $this->connexion->query($sql);
            $result = $req->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $totalCli = $result['totalCli'];
                return $totalCli;
            } else {
                return 0; // Ou une autre valeur par défaut en cas d'échec
            }
        }

        public function getOwnTotalCli($matriculeCom)
        {
            $sql = "SELECT COUNT(*) AS totalCli FROM $this->table 
                    WHERE idCli IN (
                        SELECT idCli FROM conteneurportefeuilles 
                        WHERE idPor IN (
                            SELECT idPor FROM portefeuille 
                            WHERE matriculeCom = :matriculeCom
                        )
                    )";

            $req = $this->connexion->prepare($sql);
            $req->execute([
                ":matriculeCom" => $matriculeCom
            ]);

            $result = $req->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $totalCli = $result['totalCli'];
                return $totalCli;
            } else {
                return 0; // Ou une autre valeur par défaut en cas d'échec
            }
        }


        public function readOwnCli($matriculeCom)
        {
            $sql = "SELECT * FROM $this->table 
                    WHERE idCli IN (
                        SELECT idCli FROM conteneurportefeuilles 
                        WHERE idPor IN (
                            SELECT idPor FROM portefeuille 
                            WHERE matriculeCom = :matriculeCom
                        )
                    )";

            $req = $this->connexion->prepare($sql);
            $req->execute([
                ":matriculeCom" => $matriculeCom
            ]);

            $result = $req->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getAllIdcli()
        {
            $sql = "SELECT * FROM $this->table 
                    WHERE idCli NOT IN ( 
                        SELECT idCli FROM conteneurportefeuilles
                    )";

            $req = $this->connexion->query($sql);
            
            return $req;
        }
        
        public function readCli()
        {
            $sql = "SELECT * FROM $this->table";

            $req = $this->connexion->query($sql);

            return $req;
        }

        public function updateCli()
        {
            $sql = "UPDATE $this->table SET matriculeCom=:matriculeCom, nomCli=:nomCli, telCli=:telCli, adresseCli=:adresseCli, emailCli=:emailCli, typeCli=:typeCli WHERE idCli=:idCli";

            // Préparation de la réqête
            $req = $this->connexion->prepare($sql);
                $re = $req->execute([
                ":nomCli" => $this->nomCli,
                ":telCli" => $this->telCli,
                ":adresseCli" => $this->adresseCli,
                ":emailCli" => $this->emailCli,
                ":typeCli" => $this->typeCli,
                ":matriculeCom" => $this->matriculeCom,
                ":idCli" => $this->idCli

            ]);
            if ($re) {
                return true;
            } else {
                return false;
            }
        }
    
        public function deleteCli()
        {
            $sql = "DELETE FROM $this->table WHERE idCli = :idCli";
            $req = $this->connexion->prepare($sql);
    
            $re = $req->execute(array(":idCli" => $this->idCli));
    
            if ($re) {
                return true;
            } else {
                return false;
            }
        }
    }

?>