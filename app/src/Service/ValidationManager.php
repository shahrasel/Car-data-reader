<?php

namespace Service;

class ValidationManager
{
    public function validateData(array $data): array
    {
        $error = [];

        if(empty($data['year'])) {
            $error[] = "Year is required";
        }
        if(array_key_exists("year", $data) && !empty($data['year'])) {
            if (filter_var($data['year'], FILTER_VALIDATE_INT,
                    array("options" => array("min_range"=>1900, "max_range"=>date('Y')))) === false) {
                $error[] = "Year is not valid";
            }
        }

        if(empty($data['brand'])) {
            $error[] = "Brand is required";
        }
        if(array_key_exists("brand", $data) && !empty($data['brand'])) {
            $isError = $this->validateStrLen('Brand', $data['brand'], 1, 50);
            if($isError)
                $error[] = $isError;
        }

        if(empty($data['model'])) {
            $error[] = "Model is required";
        }
        if(array_key_exists("model", $data) && !empty($data['model'])) {
            $isError = $this->validateStrLen('Model', $data['model'], 2, 50);
            if($isError)
                $error[] = $isError;
        }

        if(empty($data['door_no'])) {
            $error[] = "Door number is required";
        }
        if(array_key_exists("door_no", $data)) {
            if (filter_var($data['door_no'], FILTER_VALIDATE_INT,
                    array("options" => array("min_range"=>1, "max_range"=>6))) === false) {
                $error[] = "Door number is not valid";
            }
        }

        if(empty($data['seat_no'])) {
            $error[] = "Seat number is required";
        }
        if(array_key_exists("seat_no", $data)) {
            if (filter_var($data['seat_no'], FILTER_VALIDATE_INT,
                    array("options" => array("min_range"=>1, "max_range"=>10))) === false) {
                $error[] = "Seat number is not valid";
            }
        }

        if(empty($data['transmission'])) {
            $error[] = "Transmission is required";
        }
        if(array_key_exists("transmission", $data) && !empty($data['transmission'])) {
            $isError = $this->validateStrLen('Transmission', $data['transmission'], 2, 50);
            if($isError)
                $error[] = $isError;
        }

        if(empty($data['fuel_type'])) {
            $error[] = "Fuel type is required";
        }
        if(array_key_exists("fuel_type", $data) && !empty($data['fuel_type'])) {
            $isError = $this->validateStrLen('Fuel type', $data['fuel_type'], 2, 50);
            if($isError)
                $error[] = $isError;
        }

        return $error;
    }

    private function validateStrLen($property, $data, $min, $max): bool|string
    {
        $length = strlen($data);
        if($length < $min){
            return "$property is too short, minimum is $min characters";
        }
        elseif($length > $max){
            return "$property is too long, maximum is $max characters";
        }
        return false;
    }
}