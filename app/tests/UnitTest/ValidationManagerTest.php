<?php

use PHPUnit\Framework\TestCase;
use Service\DbConnectionManager;
use Service\ValidationManager;

class ValidationManagerTest extends TestCase
{
    private string|PDO $pdoConnect;

    protected function setUp(): void
    {
        parent::setUp();
        (new Bootstrap())->loadDotEnv();
        $this->pdoConnect = (
            new DbConnectionManager(
                $_ENV['MYSQL_HOST'],
                $_ENV['MYSQL_TEST_PORT'],
                $_ENV['MYSQL_TEST_DATABASE'],
                $_ENV['MYSQL_TEST_ROOT_USER'],
                $_ENV['MYSQL_TEST_ROOT_PASSWORD']
            )
        )->DbConnection();

        $this->deleteAllCar();
    }

    public function testEmptyDataValidatedProperly()
    {
        $carInvalidData = SELF::carEmptyData();

        $validationManager = new ValidationManager();
        $errors =$validationManager->validateData($carInvalidData, $this->pdoConnect);

        $this->assertContains('Year is required', $errors);
        $this->assertContains('Brand is required', $errors);
        $this->assertContains('Model is required', $errors);
        $this->assertContains('Door number is required', $errors);
        $this->assertContains('Seat number is required', $errors);
        $this->assertContains('Transmission is required', $errors);
        $this->assertContains('Fuel type is required', $errors);

    }

    public function testInvalidDataValidatedProperly()
    {
        $carInvalidData = SELF::carInvalidData();

        $validationManager = new ValidationManager();
        $errors =$validationManager->validateData($carInvalidData, $this->pdoConnect);

        $this->assertContains('Year is too long, maximum is'.
        ' 4 characters', $errors);
        $this->assertContains('Brand is too long, maximum is'.
        ' 50 characters', $errors);
        $this->assertContains('Model is too long, maximum is'.
        ' 50 characters', $errors);
        $this->assertContains('Door number can\'t be negative', $errors);
        $this->assertContains('Seat number is too long, maximum'.
        ' is 2 characters', $errors);
        $this->assertContains('Transmission is too long, maximum is'.
        ' 50 characters', $errors);
        $this->assertContains('Fuel type is too long, maximum is'.
        ' 50 characters', $errors);

    }

    private function deleteAllCar(): void {
        $sql = "DELETE FROM car";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
    }

    public static function carEmptyData(): array
    {
        $data = [];
        $data['year'] = '';
        $data['brand'] = '';
        $data['model'] = '';
        $data['location'] = '';
        $data['door_no'] = '';
        $data['seat_no'] = '';
        $data['transmission'] = '';
        $data['fuel_type'] = '';
        $data['license'] = '';
        $data['car_type_group'] = '';
        $data['car_type'] = '';
        $data['car_km'] = '';
        $data['width'] = '';
        $data['height'] = '';
        $data['length'] = '';

        return $data;
    }

    public static function carInvalidData(): array
    {
        $data = [];
        $data['year'] = '202234';
        $data['brand'] = 'Audi safasd fasd fasdf asdf asdf adsf asdf fd sadf
        sa dfasdf asdf asdfasfd asdf';
        $data['model'] = 'Q8 sa fadsf asdfasdfesfasd fasdfadsfasdfsdfadsfsf
        sdfasdfadsfdsf';
        $data['location'] = 'sd afsadf asdf asdf adsf sadf asdf asdfsdaf
        sa dfsadf asdf adsfEssen';
        $data['door_no'] = '-5';
        $data['seat_no'] = 'sdfsdf';
        $data['transmission'] = 'sa dfasdf asdf asdf asdf dsaf as
        s dafsadf asf safdAutomatic';
        $data['fuel_type'] = 'sa dfasdf dsaf sadf asdf asdf adsf asdf
        sa fasdf sadf adsf sfd Petrol';
        $data['license'] = 'FWDs afsadf asdf asdf asdfasd fasdfsdf
         s dfasdf safasdfdsaf1025';
        $data['car_type_group'] = 'Car';
        $data['car_type'] = 'Luxury car';
        $data['car_km'] = '2145';
        $data['width'] = '2.15';
        $data['height'] = '1.25';
        $data['length'] = '4.10';

        return $data;
    }
}