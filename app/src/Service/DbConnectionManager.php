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

    public function __construct (
        string $server,
        string $port,
        string $database,
        string $userName,
        string $password
    ) {

        $this->server = $server;
        $this->port = $port;
        $this->database = $database;
        $this->userName = $userName;
        $this->password = $password;
    }

    public function DbConnection(): PDO | string
    {
        try {
            return new PDO(
                "mysql:host=".$this->server.";port=".$this->port.";dbname=".$this->database.";charset=utf8", "$this->userName", "$this->password", [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);
        } catch(PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}