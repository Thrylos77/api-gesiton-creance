<?php

include_once __DIR__ . '/database_credentials.php';

class Database
{
    // Les propriétées de connexion à la base de données
    private $host = DB_HOST;
    private $dbname = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;

    // Connexion à la base de données
    public function getConnexion()
    {
        $conn = null;

        try {
            $conn = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (\PDOException  $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }

        return $conn;
    }
}