<?php

use Dotenv\Dotenv;
use Service\CsvManager;
use Service\DbConnectionManager;
use Service\JsonManager;

/**
 * @codeCoverageIgnore
 */
class Bootstrap
{
    public function loadDotEnv()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
    }

    public function loadConfig() {
        if ($_ENV['APP_ENV'] == 'dev') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
    }

    public function loadDBConnection(): PDO
    {
        return (new DbConnectionManager($_ENV['MYSQL_HOST'], $_ENV['MYSQL_PORT'], $_ENV['MYSQL_DATABASE'], $_ENV['MYSQL_ROOT_USER'], $_ENV['MYSQL_ROOT_PASSWORD']))->DbConnection();
    }

    public function loadTestDBConnection(): PDO
    {
        return (new DbConnectionManager($_ENV['MYSQL_HOST'], $_ENV['MYSQL_TEST_PORT'], $_ENV['MYSQL_TEST_DATABASE'], $_ENV['MYSQL_TEST_ROOT_USER'], $_ENV['MYSQL_TEST_ROOT_PASSWORD']))->DbConnection();
    }

    public function readCsvToArray(string $filename, PDO $pdoConnection): array|string
    {
        try {
            $csvManager = new CsvManager(__DIR__ . '/Resource/' .$filename, $pdoConnection);
            return $csvManager->fileDataToDb();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function readJsonToArray(string $filename, PDO $pdoConnection): array|string
    {
        try {
            $jsonManager = new JsonManager(__DIR__ . '/Resource/' .$filename, $pdoConnection);
            return $jsonManager->fileDataToDb();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function importCarTableInDb(PDO $pdoConnection)
    {
        $pdoConnection->query("DROP TABLE IF EXISTS `car`");
        $pdoConnection->query("CREATE TABLE `car` (
  `id` int NOT NULL,
  `year` varchar(4) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `door_no` varchar(2) DEFAULT NULL,
  `seat_no` varchar(2) DEFAULT NULL,
  `transmission` varchar(255) DEFAULT NULL,
  `fuel_type` varchar(255) DEFAULT NULL,
  `license` varchar(255) DEFAULT NULL,
  `car_type_group` varchar(255) DEFAULT NULL,
  `car_type` varchar(255) DEFAULT NULL,
  `car_km` varchar(255) DEFAULT NULL,
  `width` varchar(255) DEFAULT NULL,
  `height` varchar(255) DEFAULT NULL,
  `length` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

        $pdoConnection->query("ALTER TABLE `car` ADD PRIMARY KEY (`id`)");
        $pdoConnection->query("ALTER TABLE `car`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1");
    }

    public function importCarTableInTestDb(PDO $pdoConnection)
    {
        $pdoConnection->query("DROP TABLE IF EXISTS `car`");
        $pdoConnection->query("CREATE TABLE `car` (
  `id` int NOT NULL,
  `year` varchar(4) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `door_no` varchar(2) DEFAULT NULL,
  `seat_no` varchar(2) DEFAULT NULL,
  `transmission` varchar(255) DEFAULT NULL,
  `fuel_type` varchar(255) DEFAULT NULL,
  `license` varchar(255) DEFAULT NULL,
  `car_type_group` varchar(255) DEFAULT NULL,
  `car_type` varchar(255) DEFAULT NULL,
  `car_km` varchar(255) DEFAULT NULL,
  `width` varchar(255) DEFAULT NULL,
  `height` varchar(255) DEFAULT NULL,
  `length` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

        $pdoConnection->query("ALTER TABLE `car` ADD PRIMARY KEY (`id`)");
        $pdoConnection->query("ALTER TABLE `car`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1");
    }

    public function importFilesData(PDO $pdoConnection)
    {
        $errorHandlerArray = [];
        $csvErrors = $this->readCsvToArray('source-1.csv', $pdoConnection);
        $jsonErrors1 = $this->readJsonToArray('source-2.json', $pdoConnection);
        $jsonErrors2 = $this->readJsonToArray('source-3.json', $pdoConnection);

        if (!empty($csvErrors) || !empty($jsonErrors1) || !empty($jsonErrors2)) {
            if (!empty($csvErrors))
                $errorHandlerArray['csv_errors'] = $csvErrors;

            if (!empty($jsonErrors1))
                $errorHandlerArray['json_errors'][] = $jsonErrors1;

            if (!empty($jsonErrors2))
                $errorHandlerArray['json_errors'][] = $jsonErrors2;

            http_response_code(422);
            echo json_encode($errorHandlerArray);
        }
        else {
            http_response_code(201);
            echo json_encode([
                "message" => "Files imported successfully."
            ]);
        }
    }
}