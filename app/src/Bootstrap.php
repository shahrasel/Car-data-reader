<?php

use Dotenv\Dotenv;
use Service\CsvManager;
use Service\DbConnectionManager;
use Service\JsonManager;

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

    public function readCsv(string $filename, PDO $pdoConnection): array|string
    {
        try {
            $csvManager = new CsvManager(__DIR__ . '/Resource/' .$filename, $pdoConnection);
            $csvArray = $csvManager->readCsvFileToArray();
            return $csvManager->insertCsvDataToDb($csvArray);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function readJson(string $filename, PDO $pdoConnection): array|string
    {
        try {
            $jsonManager = new JsonManager(__DIR__ . '/Resource/' .$filename, $pdoConnection);
            $jsonArray = $jsonManager->readJsonFileToArray();
            return $jsonManager->insertJsonDataToDb($jsonArray);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function importFilesData(PDO $pdoConnection)
    {
        $errorHandlerArray = [];
        $csvErrors = $this->readCsv('source-1.csv', $pdoConnection);
        $jsonErrors1 = $this->readJson('source-2.json', $pdoConnection);
        $jsonErrors2 = $this->readJson('source-3.json', $pdoConnection);

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