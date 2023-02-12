<?php

namespace Controller;

use PDO;
use Service\CarManager;
use Service\ValidationManager;

class CarController
{
    private CarManager $carManager;
    private PDO $pdoConnection;

    public function __construct(
        CarManager $carManager,
        PDO $pdoConnection
    ) {
        $this->carManager = $carManager;
        $this->pdoConnection = $pdoConnection;
    }

    public function processRequest(string $method, $endpoint, ?string $id): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } elseif ($endpoint == 'cars') {
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $car = $this->carManager->get($id);

        if ( ! $car) {
            http_response_code(404);
            echo json_encode(["message" => "Car not found"]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode($car);
                break;

            default:
                http_response_code(405);
                header("Allow: GET");
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->carManager->getAll());
                break;

            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"),
                    true
                );

                $validationManager = new ValidationManager();
                $errors = $validationManager->validateData($data,
                    $this->pdoConnection
                );

                if ( ! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $id = $this->carManager->create($data);

                http_response_code(201);
                echo json_encode([
                    "message" => "Car is created",
                    "id" => $id
                ]);
                break;

            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }
}