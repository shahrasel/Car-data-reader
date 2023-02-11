<?php

namespace Model;

class Car
{
    public int $year = 0;
    public string $brand = '';
    public string $model = '';
    public ?string $location = null;
    public ?string $doorNo = null;
    public ?string $seatNo = null;
    public ?string $transmission = null;
    public ?string $fuelType = null;

    public ?string $license = null;
    public ?string $carTypeGroup = null;
    public ?string $carType = null;
    public ?string $carKm = null;
    public ?string $width = null;
    public ?string $height = null;
    public ?string $length = null;
}