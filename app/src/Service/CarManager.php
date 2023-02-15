<?php

namespace Service;

use PDO;

class CarManager
{
    private PDO $conn;

    /**
     * @param PDO $pdoConnection
     */
    public function __construct(PDO $pdoConnection)
    {
        $this->conn = $pdoConnection;
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM car";

        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO `car` (`year`, `brand`, `model`, `location`, 
                   `door_no`, `seat_no`, `transmission`, `fuel_type`, 
                   `license`, `car_type_group`, `car_type`, `car_km`, `width`, 
                   `height`, `length`) 
                   VALUES (:year, :brand, :model, :location, :door_no, 
                           :seat_no, :transmission, :fuel_type, :license, 
                           :car_type_group, :car_type, :car_km, :width, 
                           :height, :length)";

        $stmt = $this->conn->prepare($sql);

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

        return $this->conn->lastInsertId();
    }

    public function get(string $id): array | false
    {
        $sql = "SELECT * FROM car
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }
}