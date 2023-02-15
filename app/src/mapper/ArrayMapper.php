<?php

namespace mapper;

class ArrayMapper
{
    public static function arrayKeyMapper(array $oldArray): array
    {
        $newArray = [];
        foreach ($oldArray as $item) {
            if ($item == 'Location')
                $newArray[] = 'location';

            if ($item == 'Car Brand')
                $newArray[] = 'brand';

            if ($item == 'Car Model')
                $newArray[] = 'model';

            if ($item == 'License plate')
                $newArray[] = 'license';

            if ($item == 'Car year')
                $newArray[] = 'year';

            if ($item == 'Number of doors')
                $newArray[] = 'door_no';

            if ($item == 'Number of seats')
                $newArray[] = 'seat_no';

            if ($item == 'Fuel type')
                $newArray[] = 'fuel_type';

            if ($item == 'Transmission')
                $newArray[] = 'transmission';

            if ($item == 'Car Type Group')
                $newArray[] = 'car_type_group';

            if ($item == 'Car Type')
                $newArray[] = 'car_type';

            if ($item == 'Car km')
                $newArray[] = 'car_km';

            if ($item == 'Inside width')
                $newArray[] = 'width';

            if ($item == 'Inside height')
                $newArray[] = 'height';

            if ($item == 'Inside length')
                $newArray[] = 'length';
        }
        return $newArray;
    }
}