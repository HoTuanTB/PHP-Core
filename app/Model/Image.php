<?php

namespace Model;

require(__DIR__ . '/BaseModel.php');

class Image extends BaseModel
{

    private $table = 'images';


    private $decription;

    private $price;

    public function __construct()
    {
        parent::__construct($this->table);
    }
}
