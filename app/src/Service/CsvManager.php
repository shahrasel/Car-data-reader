<?php

namespace Service;

use Exception;
use mapper\ArrayMapper;
use PDO;

class CsvManager
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

    /**
     * @throws Exception
     */
    public function readFileToArray(): array
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

    public function fileDataToDb(): bool|array
    {
        $csvData = $this->readFileToArray();
        return $this->dataValidatorDbManager->insertDataToDb($csvData);
    }
}