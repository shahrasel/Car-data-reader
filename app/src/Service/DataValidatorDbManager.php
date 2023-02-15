<?php

namespace Service;

use mapper\ArrayMapper;
use PDO;

class DataValidatorDbManager
{
    public string $filePath;
    private PDO $pdoConnection;

    public function __construct(string $filePath, PDO $pdoConnection)
    {
        $this->filePath = $filePath;
        $this->pdoConnection = $pdoConnection;
    }

    public function insertDataToDb(array $carLists): array | bool
    {
        $count = 0;
        $allErrors = [];
        foreach($carLists as $carList) {
            $count++;
            $carUniKeyList = array_combine(
                ArrayMapper::arrayKeyMapper(array_keys($carList)),
                $carList
            );

            $errors = $this->dataValidation($carUniKeyList);

            if ( ! empty($errors)) {
                $errors[] = "$this->filePath has an error at index: $count".
                    " and couldn't be inserted";
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
        return $validationManager->validateData(
            $carUniKeyList,
            $this->pdoConnection
        );
    }
}