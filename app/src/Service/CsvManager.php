<?php

namespace Service;

use Exception;
use Model\Car;

class CsvManager
{
    public string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @throws Exception
     */
    public function csvToArray(): array
    {
        $handle = @fopen($this->filePath, 'r');
        if ($handle) {
            $carLists   = array_map('str_getcsv', file($this->filePath));
            $header = array_shift($carLists);
            $csv    = array();
            foreach($carLists as $carList) {
                $car = new Car();
                $car->location = $carList[0] ?? null;
                $car->brand = $carList[1] ?? null;
                $car->model = $carList[2] ?? null;
                $car->license = $carList[3] ?? null;
                $car->year = $carList[4] ?? null;
                $car->doorNo = $carList[5] ?? null;
                $car->seatNo = $carList[6] ?? null;
                $car->fuelType = $carList[7] ?? null;
                $car->transmission = $carList[8] ?? null;
                $car->carTypeGroup = $carList[9] ?? null;
                $car->carType = $carList[10] ?? null;

                $csv[] = $car;
            }
            return $csv;
        }
        else {
            throw new Exception('File not readable');
        }
    }
}