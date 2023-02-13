<?php

namespace Service;

use Exception;
use PDO;

class CsvManager
{
    public string $filePath;
    private PDO $pdoConnection;

    public function __construct(string $filePath, PDO $pdoConnection)
    {
        $this->filePath = $filePath;
        $this->pdoConnection = $pdoConnection;
    }

    /**
     * @throws Exception
     */
    public function readCsvFileToArray(): array
    {
        $handle = @fopen($this->filePath, 'r');
        if ($handle) {
            $carLists = array_map('str_getcsv', file($this->filePath));
            $header = array_shift($carLists);

            $totalCarList = [];
            foreach ($carLists as $carList) {
                $totalCarList[] = array_combine($header, $carList);
            }
            return $totalCarList;
        } else {
            throw new Exception('File is not readable');
        }
    }

    /**
     * @throws Exception
     */
    public function insertCsvDataToDb(array $carLists): array | bool
    {
        $count = 0;
        $allErrors = [];
        foreach($carLists as $carList) {
            $count++;
            $carUniKeyList = array_combine(
                CarManager::arrayKeyReplace(array_keys($carList)), $carList
            );

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
        $errors = $validationManager->validateData(
            $carUniKeyList,
            $this->pdoConnection
        );
        return $errors;
    }
}