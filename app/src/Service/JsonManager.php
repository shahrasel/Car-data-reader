<?php

namespace Service;

use Exception;
use mapper\ArrayMapper;
use PDO;

class JsonManager
{
    public string $filePath;
    private PDO $pdoConnection;
    private DataValidatorDbManager $dataValidatorDbManager;

    public function __construct(string $filePath, PDO $pdoConnection)
    {
        $this->filePath = $filePath;
        $this->pdoConnection = $pdoConnection;
        $this->dataValidatorDbManager = new DataValidatorDbManager($filePath,$pdoConnection);
    }

    public function readFileToArray(): array
    {
        $handle = @fopen($this->filePath, 'r');
        if ($handle) {
            $strJsonFileContents = file_get_contents($this->filePath);
            return json_decode($strJsonFileContents, true);
        } else {
            throw new Exception('File is not readable');
        }
    }

    public function fileDataToDb(): bool|array
    {
        $jsonData = $this->readFileToArray();
        return $this->dataValidatorDbManager->insertDataToDb($jsonData);
    }
}