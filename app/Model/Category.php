<?php

namespace Model;

require(__DIR__ . '/BaseModel.php');

require(__DIR__ . '/Product.php');

require(__DIR__ . '/Branch.php');

class Category extends BaseModel
{
    private $name;
    private $filable = [
        'name',
    ];
    protected $table = 'category';

    protected $primaryKey = 'id';

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function branchs()
    {
        return $this->hasMany(Branch::class, 'category_id');
    }

}
