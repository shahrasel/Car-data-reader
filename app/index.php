<?php

use Controller\CarController;
use Service\CarManager;

require './vendor/autoload.php';

$bootstrap = new Bootstrap();

$bootstrap->loadDotEnv();
$bootstrap->loadConfig();
$pdoConnection = $bootstrap->loadDBConnection();


set_error_handler("\\Service\\ErrorManager::handleError");
set_exception_handler("\\Service\\ErrorManager::handleException");

header("Content-type: application/json; charset=UTF-8");


$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ($parts[1] != "cars" && $parts[1] != "import_data") {
    http_response_code(404);
    exit;
}

if ($parts[1] == 'import_data') {
    $bootstrap->importFilesData($pdoConnection);
}

$id = $parts[2] ?? null;

$gateway = new CarManager($pdoConnection);

$controller = new CarController($gateway, $pdoConnection);

$controller->processRequest($_SERVER["REQUEST_METHOD"], $parts[1], $id);