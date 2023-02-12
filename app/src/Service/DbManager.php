<?php

namespace Service;

use Model\Car;
use PDO;

class DbManager
{
    private PDO $pdoConnection;
    private array $properties;
    private array $data;

    public function __construct(PDO $pdoConnection, array $properties, array $data)
    {
        $this->pdoConnection = $pdoConnection;
        $this->properties = $properties;
        $this->data = $data;
    }

    public function storeInDb()
    {
        if(!empty($this->properties)) {

            $sqlStatement = "INSERT INTO car (".implode(', ', $this->properties).") VALUES (:".implode(', :', $this->properties).")";

            $stmt = $this->pdoConnection->prepare($sqlStatement);

            foreach ($this->properties as $property) {
                $stmt->bindParam(':'.$property, ${$property});
            }

            foreach ($this->data as $data) {

                /* @var Car $data */
                ${$this->properties[0]} = $data->year ?? 0;
                ${$this->properties[1]} = $data->brand ?? '';
                ${$this->properties[2]} = $data->model ?? '';
                ${$this->properties[3]} = $data->location ?? null;
                ${$this->properties[4]} = $data->doorNo ?? 0;
                ${$this->properties[5]} = $data->seatNo ?? 0;
                ${$this->properties[6]} = $data->transmission ?? null;
                ${$this->properties[7]} = $data->fuelType ?? null;
                ${$this->properties[8]} = $data->license ?? null;
                ${$this->properties[9]} = $data->carTypeGroup ?? null;
                ${$this->properties[10]} = $data->carType ?? null;
                ${$this->properties[11]} = $data->carKm ?? null;
                ${$this->properties[12]} = $data->width ?? null;
                ${$this->properties[13]} = $data->height ?? null;
                ${$this->properties[14]} = $data->length ?? null;

                $stmt->execute();
            }
        }
    }
}