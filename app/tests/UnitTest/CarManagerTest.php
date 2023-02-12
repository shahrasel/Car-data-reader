<?php

use PHPUnit\Framework\TestCase;
use Service\CarManager;
use Service\DbConnectionManager;

class CarManagerTest extends TestCase
{
    private $pdoConnect;

    protected function setUp(): void
    {
        parent::setUp();
        (new Bootstrap())->loadDotEnv();
        $this->pdoConnect = (new DbConnectionManager($_ENV['MYSQL_HOST'], $_ENV['MYSQL_PORT'], $_ENV['MYSQL_DATABASE'], $_ENV['MYSQL_ROOT_USER'], $_ENV['MYSQL_ROOT_PASSWORD']))->DbConnection();
    }

    public function testCarCreatesProperly()
    {
        $data = [];
        $data['year'] = '2022';
        $data['brand'] = 'Audi';
        $data['model'] = 'Q8';
        $data['location'] = 'Essen';
        $data['door_no'] = '4';
        $data['seat_no'] = '4';
        $data['transmission'] = 'Automatic';
        $data['fuel_type'] = 'Petrol';
        $data['license'] = 'FWD 1025';
        $data['car_type_group'] = 'Car';
        $data['car_type'] = 'Luxury car';
        $data['car_km'] = '2145';
        $data['width'] = '2.15';
        $data['height'] = '1.25';
        $data['length'] = '4.10';

        $lastDbId = $this->getLastCar()['id'];

        $carManager = new CarManager($this->pdoConnect);
        $insertedId = $carManager->create($data);

        $this->assertNotEmpty($insertedId);
        $this->assertIsInt($insertedId);
        $this->assertSame(($lastDbId+1), $insertedId);
    }

    public function testAllCarFieldsHaveProperValueAfterCreating()
    {
        $data = [];
        $data['year'] = '2022';
        $data['brand'] = 'Audi';
        $data['model'] = 'Q8';
        $data['location'] = 'Essen';
        $data['door_no'] = '4';
        $data['seat_no'] = '4';
        $data['transmission'] = 'Automatic';
        $data['fuel_type'] = 'Petrol';
        $data['license'] = 'FWD 1025';
        $data['car_type_group'] = 'Car';
        $data['car_type'] = 'Luxury car';
        $data['car_km'] = '2145';
        $data['width'] = '2.15';
        $data['height'] = '1.25';
        $data['length'] = '4.10';

        $carManager = new CarManager($this->pdoConnect);
        $insertedId = $carManager->create($data);

        $lastDbCarInfo = $this->getLastCar();

        $this->assertSame('2022', $lastDbCarInfo['year']);
        $this->assertSame('Audi', $lastDbCarInfo['brand']);
        $this->assertSame('Q8', $lastDbCarInfo['model']);
        $this->assertSame('Essen', $lastDbCarInfo['location']);
        $this->assertSame('4', $lastDbCarInfo['door_no']);
        $this->assertSame('4', $lastDbCarInfo['seat_no']);
        $this->assertSame('Automatic', $lastDbCarInfo['transmission']);
        $this->assertSame('Petrol', $lastDbCarInfo['fuel_type']);
        $this->assertSame('FWD 1025', $lastDbCarInfo['license']);
        $this->assertSame('Car', $lastDbCarInfo['car_type_group']);
        $this->assertSame('Luxury car', $lastDbCarInfo['car_type']);
        $this->assertSame('2145', $lastDbCarInfo['car_km']);
        $this->assertSame('2.15', $lastDbCarInfo['width']);
        $this->assertSame('1.25', $lastDbCarInfo['height']);
        $this->assertSame('4.10', $lastDbCarInfo['length']);
    }

    public function testValidationWorksWhileCarCreating()
    {

    }

    private function getLastCar() {
        $sql = "SELECT * FROM car order by id desc limit 0,1";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }
}