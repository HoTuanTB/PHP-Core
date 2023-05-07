<?php

class Save
{

    public function __construct()
    {
    }

    public function save($filable, $table, $pdo)
    {
        foreach ($filable as $key => $value) {
            $functionGet = "get" . ucfirst($value);
            $attribute[$value] = $this->$functionGet();
        }
        $insert = new \Insert();
        $insert->insert($attribute, $table, $pdo);
    }
}