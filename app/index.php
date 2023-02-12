<?php

use Controller\CarController;
use Service\CarManager;

require './vendor/autoload.php';

$bootstrap = new Bootstrap();

$bootstrap->loadDotEnv();
$bootstrap->loadConfig();
$pdoConnection = $bootstrap->loadDBConnection();

/*
set_error_handler("\\Service\\ErrorManager::handleError");
set_exception_handler("\\Service\\ErrorManager::handleException");

header("Content-type: application/json; charset=UTF-8");


$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ($parts[1] != "cars") {
    http_response_code(404);
    exit;
}

$id = $parts[2] ?? null;

$gateway = new CarManager($pdoConnection);

$controller = new CarController($gateway);

$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);*/

if(!empty($_REQUEST['first_load'])) {
    if ($_REQUEST['first_load'] == true) {
        $bootstrap->readCsv('source-1.csv');
        $bootstrap->readJson('source-2.json');
        $bootstrap->readJson('source-3.json');
    }
}