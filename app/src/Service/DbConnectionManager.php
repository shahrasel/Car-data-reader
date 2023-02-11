<?php

namespace Service;

use PDO;
use PDOException;

class DbConnectionManager
{
    private string $server;
    private string $port;
    private string $database;
    private string $userName;
    private string $password;

    public function __construct(string $server, string $port, string $database, string $userName, string $password)
    {

        $this->server = $server;
        $this->port = $port;
        $this->database = $database;
        $this->userName = $userName;
        $this->password = $password;
    }

    public function DbConnection(): PDO
    {
        try {
            $conn = new PDO("mysql:host=".$this->server.";port=".$this->port.";dbname=".$this->database."", "$this->userName", "$this->password");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}