<?php

namespace App\Http\Requests;
class ProductCreateRequest
{

    public function rules()
    {
        return [
            "name" => "required",
            "decription" => "required",
            "price" => "required|number",
        ];
    }

    public function validate($data)
    {
        die();

    }
}