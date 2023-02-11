<?php
use Dotenv\Dotenv;
use Service\CsvManager;
use Service\DbConnectionManager;
use Service\DbManager;

require './vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if($_ENV['APP_ENV'] == 'dev') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$pdoConnection = (new DbConnectionManager($_ENV['MYSQL_HOST'], $_ENV['MYSQL_PORT'], $_ENV['MYSQL_DATABASE'], $_ENV['MYSQL_ROOT_USER'], $_ENV['MYSQL_ROOT_PASSWORD']))->DbConnection();


const CAR_PROPERTIES = ['year', 'brand','model', 'location', 'door_no', 'seat_no', 'transmission', 'fuel_type', 'license', 'car_type_group', 'car_type', 'car_km', 'width', 'height', 'length'];

try {
    $csvArr = (new CsvManager(__DIR__ . '/src/Resource/source-1.csv'))->csvToArray();
    (new DbManager($pdoConnection, CAR_PROPERTIES, $csvArr))->storeInDb();
} catch (Exception $e) {
    return $e->getMessage();
}

try {
    $jsonArr = (new \Service\JsonManager(__DIR__ . '/src/Resource/source-2.json'))->jsonToArray();

    (new DbManager($pdoConnection, CAR_PROPERTIES, $jsonArr))->storeInDb();
} catch (Exception $e) {
    return $e->getMessage();
}

try {
    $jsonArr = (new \Service\JsonManager(__DIR__ . '/src/Resource/source-3.json'))->jsonToArray();

    (new DbManager($pdoConnection, CAR_PROPERTIES, $jsonArr))->storeInDb();
} catch (Exception $e) {
    return $e->getMessage();
}
