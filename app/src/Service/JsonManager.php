<?php

namespace Service;

use Exception;
use Model\Car;

class JsonManager
{
    public string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @throws Exception
     */
    public function jsonToArray(): array
    {
        $handle = @fopen($this->filePath, 'r');
        if ($handle) {
            $strJsonFileContents = file_get_contents($this->filePath);
            $carLists = json_decode($strJsonFileContents, true);

            $carArray = [];
            foreach($carLists as $carList) {
                $car = new Car();
                $car->location = $carList['Location'] ?? null;
                $car->brand = $carList['Car Brand'] ?? null;
                $car->model = $carList['Car Model'] ?? null;
                $car->license = $carList['License plate'] ?? null;
                $car->year = $carList['Car year'] ?? null;
                $car->doorNo = $carList['Number of doors'] ?? null;
                $car->seatNo = $carList['Number of seats'] ?? null;
                $car->fuelType = $carList['Fuel type'] ?? null;
                $car->transmission = $carList['Transmission'] ?? null;
                $car->width = $carList['Inside width'] ?? null;
                $car->height = $carList['Inside height'] ?? null;
                $car->length = $carList['Inside length'] ?? null;
                $car->carKm = $carList['Car km'] ?? null;

                $carArray[] = $car;
            }

            return $carArray;
        } else {
            throw new Exception('File not readable');
        }
    }
}