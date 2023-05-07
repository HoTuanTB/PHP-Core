<?php

class WhereArray
{

    public function __construct()
    {

    }

    public function whereArray($conditionArrays)
    {
        foreach ($conditionArrays as $key => $conditionArray) {
            $this->where[] = [
                'column' => $conditionArray[0],
                'operator' => $conditionArray[1],
                'value' => $conditionArray[2],
            ];
        }
        return $this;
    }
}