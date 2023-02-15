<?php

namespace Service;

use PDO;

class ValidationManager
{
    public function validateData(array $data, PDO $pdoConnection): array
    {
        $error = [];

        if (empty($data['year'])) {
            $error[] = "Year is required";
        }
        if (array_key_exists("year", $data) && !empty($data['year'])) {
            if ( ((int) $data['year']) <=0 ) {
                $error[] = "Year is invalid";
            } else {
                $isError = $this->validateStrLen('Year', $data['year'], 4, 4);
                if ($isError) {
                    $error[] = $isError;
                }
            }
        }

        if (empty($data['brand'])) {
            $error[] = "Brand is required";
        }
        if (array_key_exists("brand", $data) && !empty($data['brand'])) {
            $isError = $this->validateStrLen('Brand', $data['brand'], 1, 50);
            if ($isError) {
                $error[] = $isError;
            }
        }

        if (empty($data['model'])) {
            $error[] = "Model is required";
        }
        if (array_key_exists("model", $data) && !empty($data['model'])) {
            $isError = $this->validateStrLen('Model', $data['model'], 2, 50);
            if ($isError) {
                $error[] = $isError;
            }
        }

        if (empty($data['door_no'])) {
            $error[] = "Door number is required";
        }
        if (array_key_exists("door_no", $data) && !empty($data['door_no'])) {
            if ( ((int) $data['door_no']) <=0 ) {
                $error[] = "Door number is invalid";
            } else {
                $isError = $this->validateStrLen(
                    'Door number',
                    $data['door_no'],
                    1,
                    2
                );
                if ($isError) {
                    $error[] = $isError;
                }
            }
        }

        if (empty($data['seat_no'])) {
            $error[] = "Seat number is required";
        }
        if (array_key_exists("seat_no", $data) && !empty($data['seat_no'])) {
            if ( ((int) $data['seat_no']) <=0 ) {
                $error[] = "Seat number is invalid";
            } else {
                $isError = $this->validateStrLen(
                    'Seat number',
                    $data['seat_no'],
                    1,
                    2
                );
                if ($isError) {
                    $error[] = $isError;
                }
            }
        }

        if (empty($data['transmission'])) {
            $error[] = "Transmission is required";
        }
        if (array_key_exists("transmission", $data)
            && !empty($data['transmission'])) {
            $isError = $this->validateStrLen(
                'Transmission', $data['transmission'], 2, 50
            );
            if ($isError) {
                $error[] = $isError;
            }
        }

        if (empty($data['fuel_type'])) {
            $error[] = "Fuel type is required";
        }
        if (array_key_exists("fuel_type", $data)
            && !empty($data['fuel_type'])) {
            $isError = $this->validateStrLen(
                'Fuel type', $data['fuel_type'], 2, 50
            );
            if ($isError) {
                $error[] = $isError;
            }
        }

        if (array_key_exists("location", $data)
            && !empty($data['location'])) {
            $isError = $this->validateStrLen(
                'Location', $data['location'], 2, 100
            );
            if ($isError) {
                $error[] = $isError;
            }
        }

        if (array_key_exists("license", $data)
            && !empty($data['license'])) {
            $isError = $this->validateStrLen(
                'License', $data['license'], 2, 50
            );
            if ($isError) {
                $error[] = $isError;
            }
        }

        if (array_key_exists("car_type_group", $data)
            && !empty($data['car_type_group'])) {
            $isError = $this->validateStrLen(
                'Car Type Group', $data['car_type_group'], 2, 100
            );
            if ($isError) {
                $error[] = $isError;
            }
        }

        if (array_key_exists("car_type", $data)
            && !empty($data['car_type'])) {
            $isError = $this->validateStrLen(
                'Car type', $data['car_type'], 2, 50
            );
            if ($isError) {
                $error[] = $isError;
            }
        }

        if (array_key_exists("car_km", $data)
            && !empty($data['car_km'])) {
            if ( ((float) $data['car_km']) <=0 ) {
                $error[] = "Car km is invalid";
            } else {
                $isError = $this->validateStrLen(
                    'Car Km',
                    $data['car_km'],
                    2,
                    10
                );
                if ($isError) {
                    $error[] = $isError;
                }
            }
        }

        if (array_key_exists("width", $data)
            && !empty($data['width'])) {
            if ( ((float) $data['width']) <=0 ) {
                $error[] = "Width is invalid";
            } else {
                $isError = $this->validateStrLen(
                    'Width',
                    $data['width'],
                    1,
                    10
                );
                if ($isError) {
                    $error[] = $isError;
                }
            }
        }

        if (array_key_exists("height", $data)
            && !empty($data['height'])) {
            if ( ((float) $data['height']) <=0 ) {
                $error[] = "Height is invalid";
            } else {
                $isError = $this->validateStrLen(
                    'Height',
                    $data['height'],
                    1,
                    10
                );
                if ($isError) {
                    $error[] = $isError;
                }
            }
        }

        if (array_key_exists("length", $data)
            && !empty($data['length'])) {
            if ( ((float) $data['length']) <=0 ) {
                $error[] = "Length can't be negative";
            } else {
                $isError = $this->validateStrLen(
                    'Length',
                    $data['length'],
                    1,
                    10
                );
                if ($isError) {
                    $error[] = $isError;
                }
            }
        }

        if(empty($error)) {
            $isError = $this->isDuplicate($data, $pdoConnection);
            if ($isError) {
                $error[] = $isError;
            }
        }


        return $error;
    }

    private function isDuplicate(array $data, PDO $pdoConnection): bool|string {
        $sql = "SELECT * FROM car
                WHERE year = :year
                and brand = :brand
                and model = :model
                and location <=> :location
                and door_no <=> :door_no
                and seat_no <=> :seat_no
                and transmission <=> :transmission
                and fuel_type <=> :fuel_type
                and license <=> :license
                and car_type_group <=> :car_type_group
                and car_type <=> :car_type
                and car_km <=> :car_km
                and width <=> :width
                and height <=> :height
                and length <=> :length";

        $stmt = $pdoConnection->prepare($sql);

        $stmt->bindValue(":year", $data["year"],
            PDO::PARAM_STR);
        $stmt->bindValue(":brand", $data["brand"],
            PDO::PARAM_STR);
        $stmt->bindValue(":model", $data["model"],
            PDO::PARAM_STR);
        $stmt->bindValue(":location", $data["location"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":door_no", $data["door_no"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":seat_no", $data["seat_no"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":transmission", $data["transmission"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":fuel_type", $data["fuel_type"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":license", $data["license"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":car_type_group", $data["car_type_group"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":car_type", $data["car_type"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":car_km", $data["car_km"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":width", $data["width"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":height", $data["height"] ?? null,
            PDO::PARAM_STR);
        $stmt->bindValue(":length", $data["length"] ?? null,
            PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($row)) {
            return "data duplication found!";
        }
        return false;
    }

    private function validateStrLen(
        string $property,
        string $data,
        int $min,
        int $max): bool|string
    {
        $length = strlen($data);
        if ($length < $min) {
            return "$property is too short, minimum is $min characters";
        } elseif ($length > $max) {
            return "$property is too long, maximum is $max characters";
        }
        return false;
    }
}