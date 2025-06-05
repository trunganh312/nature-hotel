<?php

namespace src\Libs\DataTransferObject;

class AbstractDataTransferObject implements IDataTransferObject
{
    function camel_to_snake($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    function snakeToCamel($input)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }

    public function toArray(bool $toSnake = true): array
    {
        $data = get_object_vars($this);
        if ($toSnake) {
            $result = [];

            foreach ($data as $key => $value) {
                $result[$this->camel_to_snake($key)] = $value;
            }
            return $result;
        }
        return $data;
    }
}