<?php

namespace Controller;

use Service\CarManager;
use Service\ValidationManager;

class CarController
{
    public function __construct(private CarManager $carManager)
    {
    }

    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
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

            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $validationManager = new ValidationManager();
                $errors = $validationManager->validateData($data);

                if ( ! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $rows = $this->carManager->update($car, $data);

                echo json_encode([
                    "message" => "Car $id is updated",
                    "rows" => $rows
                ]);
                break;

            case "DELETE":
                $rows = $this->carManager->delete($id);

                echo json_encode([
                    "message" => "Car $id is deleted",
                    "rows" => $rows
                ]);
                break;

            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->carManager->getAll());
                break;

            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $validationManager = new ValidationManager();
                $errors = $validationManager->validateData($data);

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

    /*private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        if ($is_new && empty($data["name"])) {
            $errors[] = "name is required";
        }

        if (array_key_exists("size", $data)) {
            if (filter_var($data["size"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "size must be an integer";
            }
        }

        return $errors;
    }*/
}