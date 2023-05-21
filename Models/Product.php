<?php

namespace Models;

class Product extends BaseModel
{
    protected $table = 'products';

    private $filable = [
        'id',
        'name',
        'price',
        'decription',
    ];

    public function getFilable()
    {
        return $this->filable;
    }

}