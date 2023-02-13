<?php

namespace Service;

use Exception;
use PDO;

class JsonManager implements ResourceDataManagerInterfeace
{
    public string $filePath;
    private PDO $pdoConnection;

    public function __construct(string $filePath, PDO $pdoConnection)
    {
        $this->filePath = $filePath;
        $this->pdoConnection = $pdoConnection;
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

    public function insertDataToDb(array $carLists): array | bool
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
                $errors[] = "$this->filePath has an error at index: $count and couldn't be inserted";
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
        return $validationManager
            ->validateData($carUniKeyList, $this->pdoConnection);
    }
}