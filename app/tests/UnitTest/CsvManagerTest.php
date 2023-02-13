<?php

use PHPUnit\Framework\TestCase;
use Service\CarManager;
use Service\CsvManager;
use Service\DbConnectionManager;

class CsvManagerTest extends TestCase
{
    private $pdoConnect;

    protected function setUp(): void
    {
        parent::setUp();
        (new Bootstrap())->loadDotEnv();
        $this->pdoConnect = (new DbConnectionManager($_ENV['MYSQL_HOST'], $_ENV['MYSQL_TEST_PORT'], $_ENV['MYSQL_TEST_DATABASE'], $_ENV['MYSQL_TEST_ROOT_USER'], $_ENV['MYSQL_TEST_ROOT_PASSWORD']))->DbConnection();

        $this->deleteAllCar();
    }

    public function testCsvDataToDbInsertProperly()
    {
        $csvManager = new CsvManager(__DIR__ . '/../../src/Resource/source-1.csv', $this->pdoConnect);
        $csvManager->insertCsvDataToDb();

        $this->assertCount(10, (new CarManager($this->pdoConnect))->getAll());
    }

    private function deleteAllCar(): void {
        $sql = "DELETE FROM car";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
    }
}