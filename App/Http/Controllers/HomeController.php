<?php

namespace App\Http\Controllers;

use Models\Product;
use Views\View;

class HomeController
{
    public function index()
    {
        $productModel = new Product();
        $products = $productModel->select('*')->get();
        View::render('home/index.php', [
            'productsInfo' => $products,
        ]);
    }

    public function create()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $productModel = new Product();

        if ($requestMethod === "POST") {
            $productModel->insert([
                "name" => $_POST['name'],
                "decription" => $_POST['decription'],
                "price" => $_POST['price'],
            ]);
            header("Location: /");
        } else {
            View::render('home/create.php');
        }
    }
}