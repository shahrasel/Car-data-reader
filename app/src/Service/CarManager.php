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

    public function create(array $data): string
    {
        $sql = "INSERT INTO car (name, size, is_available)
                VALUES (:name, :size, :is_available)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":size", $data["size"] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(":is_available", (bool) ($data["is_available"] ?? false), PDO::PARAM_BOOL);

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

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE car
                SET name = :name, size = :size, is_available = :is_available
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
        $stmt->bindValue(":size", $new["size"] ?? $current["size"], PDO::PARAM_INT);
        $stmt->bindValue(":is_available", $new["is_available"] ?? $current["is_available"], PDO::PARAM_BOOL);

        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id): int
    {
        $sql = "DELETE FROM car
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}