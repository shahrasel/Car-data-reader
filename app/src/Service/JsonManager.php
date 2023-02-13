<?php

namespace Service;

use Exception;
use PDO;

class JsonManager
{
    public string $filePath;
    private PDO $pdoConnection;

    public function __construct(string $filePath, PDO $pdoConnection)
    {
        $this->filePath = $filePath;
        $this->pdoConnection = $pdoConnection;
    }

    public function readJsonFileToArray(): array
    {
        $handle = @fopen($this->filePath, 'r');
        if ($handle) {
            $strJsonFileContents = file_get_contents($this->filePath);
            $carLists = json_decode($strJsonFileContents, true);
            return $carLists;
        } else {
            throw new Exception('File is not readable');
        }
    }

    public function insertJsonDataToDb(array $carLists): array | bool
    {
        $count = 0;
        $allErrors = [];
        foreach($carLists as $carList) {
            $count++;
            $carUniKeyList =
                array_combine(
                    CarManager::arrayKeyReplace(array_keys($carList)),
                    $carList);
            $errors = $this->dataValidation($carUniKeyList);

            if ( ! empty($errors)) {
                $errors[] = "$this->filePath has an error at index: $count";
                $allErrors[] = $errors;
            } else {
                $carManager = new CarManager($this->pdoConnection);
                $carManager->create($carUniKeyList);
            }
        }

        if (!empty($allErrors)) {
            return $allErrors;
        } else {
            return false;
        }
    }

    public function dataValidation(array $carUniKeyList): array
    {
        $validationManager = new ValidationManager();
        $errors = $validationManager
            ->validateData($carUniKeyList, $this->pdoConnection);
        return $errors;
    }
}