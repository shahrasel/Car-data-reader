<?php

namespace Service;

interface ResourceDataManagerInterfeace
{
    public function readFileToArray(): array;

    public function insertDataToDb(array $carLists): array|bool;

    public function dataValidation(array $carUniKeyList): array;
}