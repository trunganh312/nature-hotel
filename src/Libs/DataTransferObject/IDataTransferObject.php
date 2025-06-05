<?php
namespace src\Libs\DataTransferObject;

interface IDataTransferObject
{
    public function toArray(): array;
}