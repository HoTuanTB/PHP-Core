<?php

namespace Model;

//require(__DIR__ . '/BaseModel.php');
//require(__DIR__ . '/Comment.php');

class Branch extends BaseModel
{
    private $name;
    private $filable = [
        'name',
    ];

    protected $primaryKey = 'id';
    protected $table = 'branchs';

    public function getFilable()
    {
        return $this->filable;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getname()
    {
        return $this->name;
    }

}
