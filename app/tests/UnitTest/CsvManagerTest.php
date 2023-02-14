<?php

use PHPUnit\Framework\TestCase;
use Service\CarManager;
use Service\CsvManager;
use Service\DbConnectionManager;

class CsvManagerTest extends TestCase
{
    private $pdoConnect;

    private const REALFILEPATH =
        __DIR__ . '/../../src/Resource/source-1.csv';
    private const FAKEFILEPATH =
        __DIR__ . '/../../src/Resource/source-111.csv';
    private const CORRUPTEDDATAFILEPATH =
        __DIR__ . '/../../src/Resource/testingFiles/source-11.csv';

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

    public function testCsvDataReadProperly()
    {
        $csvManager = new CsvManager(SELF::REALFILEPATH, $this->pdoConnect);

        $this->assertCount(10, $csvManager->readFileToArray());
    }

    public function testExceptionIfCsvFileNotReadable() {
        $this->expectException(Exception::class);
        $csvManager = new CsvManager(SELF::FAKEFILEPATH, $this->pdoConnect);
        $csvManager->readFileToArray();
    }

    public function testCsvDataToDbInsertProperly()
    {
        $csvManager = new CsvManager(SELF::REALFILEPATH, $this->pdoConnect);
        $csvManager->insertDataToDb($csvManager->readFileToArray());

        $this->assertCount(
            count($csvManager->readFileToArray()),
            (new CarManager($this->pdoConnect))->getAll()
        );
    }

    public function testCsvInvalidDataIsNotInserted()
    {
        $csvManager = new CsvManager(
            SELF::CORRUPTEDDATAFILEPATH,
            $this->pdoConnect
        );
        $carLists = $csvManager->readFileToArray();

        $allErrors = [];
        foreach($carLists as $carList) {
            $carUniKeyList = array_combine(
                CarManager::arrayKeyReplace(array_keys($carList)), $carList
            );

            $errors = $csvManager->dataValidation($carUniKeyList);

            if(!empty($errors)) {
                $allErrors[] = $errors;
            }
        }

        $validDataCount = count($carLists) - count($allErrors);

        $csvManager->insertDataToDb($csvManager->readFileToArray());

        $this->assertCount(
            $validDataCount, (new CarManager($this->pdoConnect))->getAll()
        );
    }

    private function deleteAllCar(): void {
        $sql = "DELETE FROM car";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
    }
}