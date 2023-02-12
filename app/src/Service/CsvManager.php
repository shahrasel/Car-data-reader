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
            $count = 0;
            foreach($carLists as $carList) {
                $count++;
                $carList = array_combine($header, $carList);
                $carUniKeyList = array_combine($this->arrayKeyReplace(array_keys($carList)), array_keys($carList));

                $validationManager = new ValidationManager();
                $errors = $validationManager->validateData($carUniKeyList);

                if ( ! empty($errors)) {
                    $errors[] = "$this->filePath has an error in the index of: $count";
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

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

    private function arrayKeyReplace(array $oldArray): array
    {
        $newArray = [];
        foreach ($oldArray as $item) {
            if($item == 'Location')
                $newArray[] = 'location';

            if($item == 'Car Brand')
                $newArray[] = 'brand';

            if($item == 'Car Model')
                $newArray[] = 'model';

            if($item == 'License plate')
                $newArray[] = 'license';

            if($item == 'Car year')
                $newArray[] = 'year';

            if($item == 'Number of doors')
                $newArray[] = 'door_no';

            if($item == 'Number of seats')
                $newArray[] = 'seat_no';

            if($item == 'Fuel type')
                $newArray[] = 'fuel_type';

            if($item == 'Transmission')
                $newArray[] = 'transmission';

            if($item == 'Car Type Group')
                $newArray[] = 'car_type_group';

            if($item == 'Car Type')
                $newArray[] = 'car_type';
        }
        return $newArray;
    }
}