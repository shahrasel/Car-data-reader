<?php

use Dotenv\Dotenv;
use Service\CsvManager;
use Service\DbConnectionManager;
use Service\DbManager;
use Service\JsonManager;

class Bootstrap
{
    const CAR_PROPERTIES = ['year', 'brand','model', 'location', 'door_no', 'seat_no', 'transmission', 'fuel_type', 'license', 'car_type_group', 'car_type', 'car_km', 'width', 'height', 'length'];

    public function loadDotEnv()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
    }

    public function loadConfig() {
        if($_ENV['APP_ENV'] == 'dev') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
    }

    public function loadDBConnection(): PDO
    {
        return (new DbConnectionManager($_ENV['MYSQL_HOST'], $_ENV['MYSQL_PORT'], $_ENV['MYSQL_DATABASE'], $_ENV['MYSQL_ROOT_USER'], $_ENV['MYSQL_ROOT_PASSWORD']))->DbConnection();
    }

    public function readCsv($filename)
    {
        try {
            $csvArr = (new CsvManager(__DIR__ . '/Resource/' .$filename))->csvToArray();
            (new DbManager($this->loadDBConnection(), SELF::CAR_PROPERTIES, $csvArr))->storeInDb();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function readJson($filename)
    {
        try {
            $jsonArr = (new JsonManager(__DIR__ . '/Resource/' .$filename))->jsonToArray();

            (new DbManager($this->loadDBConnection(), SELF::CAR_PROPERTIES, $jsonArr))->storeInDb();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}