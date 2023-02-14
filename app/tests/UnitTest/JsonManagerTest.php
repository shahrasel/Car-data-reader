<?php

use PHPUnit\Framework\TestCase;
use Service\CarManager;
use Service\DbConnectionManager;
use Service\JsonManager;

class JsonManagerTest extends TestCase
{
    private string|PDO $pdoConnect;

    private const REALFILEPATH =
        __DIR__ . '/../../src/Resource/source-2.json';
    private const FAKEFILEPATH =
        __DIR__ . '/../../src/Resource/source-222.json';
    private const CORRUPTEDDATAFILEPATH =
        __DIR__ . '/../../src/Resource/testingFiles/source-22.json';

    protected function setUp(): void
    {
        parent::setUp();
        (new Bootstrap())->loadDotEnv();
        $this->pdoConnect = (
            new DbConnectionManager($_ENV['MYSQL_HOST'],
                $_ENV['MYSQL_TEST_PORT'],
                $_ENV['MYSQL_TEST_DATABASE'],
                $_ENV['MYSQL_TEST_ROOT_USER'],
                $_ENV['MYSQL_TEST_ROOT_PASSWORD'])
        )->DbConnection();

        $this->deleteAllCar();
    }

    public function testJsonDataReadProperly()
    {
        $jsonManager = new JsonManager(SELF::REALFILEPATH, $this->pdoConnect);

        $this->assertCount(10, $jsonManager->readFileToArray());
    }

    public function testExceptionIfJsonFileNotReadable() {
        $this->expectException(Exception::class);
        $jsonManager = new JsonManager(SELF::FAKEFILEPATH, $this->pdoConnect);
        $jsonManager->readFileToArray();
    }

    public function testJsonDataToDbInsertProperly()
    {
        $jsonManager = new JsonManager(SELF::REALFILEPATH, $this->pdoConnect);

        $carLists = $jsonManager->readFileToArray();

        $allErrors = [];
        foreach($carLists as $carList) {
            $carUniKeyList = array_combine(
                CarManager::arrayKeyReplace(array_keys($carList)), $carList
            );

            $errors = $jsonManager->dataValidation($carUniKeyList);

            if(!empty($errors)) {
                $allErrors[] = $errors;
            }
        }

        $validDataCount = count($carLists) - count($allErrors);

        $jsonManager->insertDataToDb($jsonManager->readFileToArray());

        $this->assertCount(
            $validDataCount,
            (new CarManager($this->pdoConnect))->getAll()
        );
    }

    public function testJsonInvalidDataIsNotInserted()
    {
        $jsonManager = new JsonManager(
            SELF::CORRUPTEDDATAFILEPATH,
            $this->pdoConnect
        );
        $jsonManager->insertDataToDb($jsonManager->readFileToArray());

        $this->assertCount(6, (new CarManager($this->pdoConnect))->getAll());
    }

    private function deleteAllCar(): void {
        $sql = "DELETE FROM car";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
    }
}