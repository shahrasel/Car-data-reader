<?php

use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Service\CarManager;
use Service\CsvManager;
use Service\DbConnectionManager;
use Service\ValidationManager;

class CarControllerTest extends TestCase
{
    private $pdoConnect;
    private $http;

    protected function setUp(): void
    {
        parent::setUp();
        (new Bootstrap())->loadDotEnv();
        $this->pdoConnect = (
            new DbConnectionManager($_ENV['MYSQL_HOST'],
                $_ENV['MYSQL_PORT'],
                $_ENV['MYSQL_DATABASE'],
                $_ENV['MYSQL_ROOT_USER'],
                $_ENV['MYSQL_ROOT_PASSWORD'])
        )->DbConnection();

        $this->http = new GuzzleHttp\Client(['base_uri'
        => $_ENV['IP_ADDRESS_MAC'].':8081/']);
    }

    public function testGetCars()
    {
        $response = $this->http->request('GET', 'cars');

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json; charset=UTF-8", $contentType);

        $carList = json_decode($response->getBody());

        $this->assertCount(count((new CarManager($this->pdoConnect))->getAll()), $carList);
    }

    public function testCarDataIsProper()
    {
        $response = $this->http->request('GET', 'cars');

        $contentType = $response->getHeaders()["Content-Type"][0];
        $carList = json_decode($response->getBody());

        $randomCarInfo = $carList[array_rand($carList)];
        $selCar = $this->getCarInfoById((int) $randomCarInfo->id);

        $this->assertEquals("application/json; charset=UTF-8", $contentType);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($randomCarInfo->year, $selCar['year']);
        $this->assertSame($randomCarInfo->brand, $selCar['brand']);
        $this->assertSame($randomCarInfo->model, $selCar['model']);
        $this->assertSame($randomCarInfo->location, $selCar['location']);
        $this->assertSame($randomCarInfo->door_no, $selCar['door_no']);
        $this->assertSame($randomCarInfo->seat_no, $selCar['seat_no']);
        $this->assertSame($randomCarInfo->transmission, $selCar['transmission']);
        $this->assertSame($randomCarInfo->fuel_type, $selCar['fuel_type']);
        $this->assertSame($randomCarInfo->license, $selCar['license']);
        $this->assertSame($randomCarInfo->car_type_group, $selCar['car_type_group']);
        $this->assertSame($randomCarInfo->car_type, $selCar['car_type']);
        $this->assertSame($randomCarInfo->car_km, $selCar['car_km']);
        $this->assertSame($randomCarInfo->width, $selCar['width']);
        $this->assertSame($randomCarInfo->height, $selCar['height']);
        $this->assertSame($randomCarInfo->length, $selCar['length']);
    }

    public function testSingleCarFetchProperly()
    {
        $randomCarInfo = $this->getRandomCar();

        $response = $this->http->request('GET', 'cars/'.$randomCarInfo['id']);
        $contentType = $response->getHeaders()["Content-Type"][0];
        $selCar = json_decode($response->getBody());

        $this->assertEquals("application/json; charset=UTF-8", $contentType);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($randomCarInfo['year'], $selCar->year);
        $this->assertSame($randomCarInfo['brand'], $selCar->brand);
        $this->assertSame($randomCarInfo['model'], $selCar->model);
        $this->assertSame($randomCarInfo['location'], $selCar->location);
        $this->assertSame($randomCarInfo['door_no'], $selCar->door_no);
        $this->assertSame($randomCarInfo['seat_no'], $selCar->seat_no);
        $this->assertSame($randomCarInfo['transmission'], $selCar->transmission);
        $this->assertSame($randomCarInfo['fuel_type'], $selCar->fuel_type);
        $this->assertSame($randomCarInfo['license'], $selCar->license);
        $this->assertSame($randomCarInfo['car_type_group'], $selCar->car_type_group);
        $this->assertSame($randomCarInfo['car_type'], $selCar->car_type);
        $this->assertSame($randomCarInfo['car_km'], $selCar->car_km);
        $this->assertSame($randomCarInfo['width'], $selCar->width);
        $this->assertSame($randomCarInfo['height'], $selCar->height);
        $this->assertSame($randomCarInfo['length'], $selCar->length);
    }

    public function testSingleCarWithInvalidIdShowsError()
    {
        $this->expectException(ClientException::class);
        $response = $this->http->request('GET', 'cars/1000000000');
    }

    public function testCreatesCarProperly()
    {
        $randomCarInfo = $this->getRandomCar();

        $response = $this->http->request('POST', 'cars', [
            'body' => json_encode([
                'year' => '2023',
                'brand' => 'VW',
                'model' => 'Tiguan',
                'location' => 'Essen',
                'door_no' => '4',
                'seat_no' => '5',
                'transmission' => 'Automatic',
                'fuel_type' => 'Petrol',
                'license' => 'FWD 1584',
                'car_type_group' => 'Car',
                'car_type' => 'Luxury Car',
                'car_km' => time(),
                'width' => '1.25',
                'height' => '1.58',
                'length' => '2.53',
            ])
        ]);
        $contentType = $response->getHeaders()["Content-Type"][0];
        $response = json_decode($response->getBody());

        $this->assertSame('Car is created', $response->message);
        $this->assertIsInt($response->id);

        $this->deleteCar($response->id);
    }

    public function tearDown(): void {
        $this->http = null;
    }

    private function getCarInfoById(int $id) {
        $sql = "SELECT * FROM car where id=$id limit 0,1";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    private function getRandomCar() {
        $sql = "SELECT * FROM car ORDER BY RAND() LIMIT 1";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    private function deleteCar(int $id): void {
        $sql = "DELETE FROM car where id=$id";
        $stmt = $this->pdoConnect->prepare($sql);

        $stmt->execute();
    }
}