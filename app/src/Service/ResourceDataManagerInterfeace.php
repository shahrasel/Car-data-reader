<?php

namespace Service;

interface ResourceDataManagerInterfeace
{
    public function readFileToArray(): array;

    public function fileDataToDb(): bool|array;

}