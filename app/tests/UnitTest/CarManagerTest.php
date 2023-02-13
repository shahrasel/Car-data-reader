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
        $this->pdoConnect = (new DbConnectionManager($_ENV['MYSQL_HOST'], $_ENV['MYSQL_TEST_PORT'], $_ENV['MYSQL_TEST_DATABASE'], $_ENV['MYSQL_TEST_ROOT_USER'], $_ENV['MYSQL_TEST_ROOT_PASSWORD']))->DbConnection();

        $this->deleteAllCar();
    }

    public function testCarCreatesProperly()
    {
        $carManager = new CarManager($this->pdoConnect);
        $insertedId = $carManager->create(SELF::carData1());

        $this->assertNotEmpty($insertedId);
        $this->assertIsInt($insertedId);
    }

    public function testAllCarFieldsHaveProperValueAfterCreating()
    {

        $carManager = new CarManager($this->pdoConnect);
        $insertedId = $carManager->create(SELF::carData1());

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

    public function testGetAllCarWorksProperly()
    {


        $carManager = new CarManager($this->pdoConnect);
        $carManager->create(SELF::carData1());
        $carManager->create(SELF::carData2());
        $carManager->create(SELF::carData3());

        $car1 = $carManager->getAll()[0];

        $this->assertCount(3, $carManager->getAll());
        $this->assertSame('2022', $car1['year']);
        $this->assertSame('Audi', $car1['brand']);
        $this->assertSame('Q8', $car1['model']);
        $this->assertSame('Essen', $car1['location']);
        $this->assertSame('4', $car1['door_no']);
        $this->assertSame('4', $car1['seat_no']);
        $this->assertSame('Automatic', $car1['transmission']);
        $this->assertSame('Petrol', $car1['fuel_type']);
        $this->assertSame('FWD 1025', $car1['license']);
        $this->assertSame('Car', $car1['car_type_group']);
        $this->assertSame('Luxury car', $car1['car_type']);
        $this->assertSame('2145', $car1['car_km']);
        $this->assertSame('2.15', $car1['width']);
        $this->assertSame('1.25', $car1['height']);
        $this->assertSame('4.10', $car1['length']);
    }

    public function testGetSingleCarDataWorksproperly()
    {
        $carManager = new CarManager($this->pdoConnect);
        $carManager->create(SELF::carData1());
        $carManager->create(SELF::carData2());
        $carManager->create(SELF::carData3());

        $lastCar = $this->getLastCar();

        $lastCarInfo = $carManager->get($lastCar['id']);

        $this->assertSame('2023', $lastCarInfo['year']);
        $this->assertSame('BMW', $lastCarInfo['brand']);
        $this->assertSame('X3', $lastCarInfo['model']);
        $this->assertSame('Koln', $lastCarInfo['location']);
        $this->assertSame('2', $lastCarInfo['door_no']);
        $this->assertSame('2', $lastCarInfo['seat_no']);
        $this->assertSame('Automatic', $lastCarInfo['transmission']);
        $this->assertSame('Petrol', $lastCarInfo['fuel_type']);
        $this->assertSame('FWD 1023', $lastCarInfo['license']);
        $this->assertSame('Car', $lastCarInfo['car_type_group']);
        $this->assertSame('Luxury car', $lastCarInfo['car_type']);
        $this->assertSame('3145', $lastCarInfo['car_km']);
        $this->assertSame('3.15', $lastCarInfo['width']);
        $this->assertSame('3.25', $lastCarInfo['height']);
        $this->assertSame('3.10', $lastCarInfo['length']);
    }

    private function getLastCar() {
        $sql = "SELECT * FROM car order by id desc limit 0,1";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    private function deleteAllCar(): void {
        $sql = "DELETE FROM car";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
    }

    public static function carData1(): array
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

        return $data;
    }

    public static function carData2(): array
    {
        $data = [];
        $data['year'] = '2021';
        $data['brand'] = 'VW';
        $data['model'] = 'Tiguan';
        $data['location'] = 'Bochum';
        $data['door_no'] = '2';
        $data['seat_no'] = '4';
        $data['transmission'] = 'Automatic';
        $data['fuel_type'] = 'Petrol';
        $data['license'] = 'FWD 1022';
        $data['car_type_group'] = 'Car';
        $data['car_type'] = 'Luxury car';
        $data['car_km'] = '2142';
        $data['width'] = '2.12';
        $data['height'] = '1.22';
        $data['length'] = '4.12';

        return $data;
    }

    public static function carData3(): array
    {
        $data = [];
        $data['year'] = '2023';
        $data['brand'] = 'BMW';
        $data['model'] = 'X3';
        $data['location'] = 'Koln';
        $data['door_no'] = '2';
        $data['seat_no'] = '2';
        $data['transmission'] = 'Automatic';
        $data['fuel_type'] = 'Petrol';
        $data['license'] = 'FWD 1023';
        $data['car_type_group'] = 'Car';
        $data['car_type'] = 'Luxury car';
        $data['car_km'] = '3145';
        $data['width'] = '3.15';
        $data['height'] = '3.25';
        $data['length'] = '3.10';

        return $data;
    }
}