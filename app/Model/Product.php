<?php

namespace Model;

require(__DIR__ . '/BaseModel.php');
require(__DIR__ . '/Comment.php');

class Product extends BaseModel
{
    private $name;
    private $filable = [
        'name',
//        'price,'
    ];
    protected $table = 'products';

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

    public function comments()
    {
        $this->hasMany(Comment::class, 'comment_id');
    }
}
