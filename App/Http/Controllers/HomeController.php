<?php

namespace App\Http\Controllers;

use Models\Product;
use Views\View;
use App\Http\Requests\ProductCreateRequest;
use App\Helpers\UploadImage;
use App\Http\Controllers\Notice;

const FLASH = 'FLASH_MESSAGES';

const FLASH_ERROR = 'error';
const FLASH_WARNING = 'warning';
const FLASH_INFO = 'info';
const FLASH_SUCCESS = 'success';
class HomeController
{
    public function index()
    {
        $productModel = new Product();
        $products = $productModel->select('*')->get();
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        View::render('home/index.php', [
            'productsInfo' => $products,
            'message' => $message ?? null,
        ]);
    }

    public function create()
    {
//        $productCreateRequest = new ProductCreateRequest();
//        $productCreateRequest->validate($_POST);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $productModel = new Product();
        if ($requestMethod === "POST") {
            if (isset($_FILES['image'])) {
                $uploadImage = new UploadImage();
                $imageUrl = $uploadImage->upload($_FILES['image']);
            }
            $productModel->insert([
                "name" => $_POST['name'],
                "decription" => $_POST['decription'],
                "price" => $_POST['price'],
                "image" => $imageUrl ?? null,
            ]);
            $_SESSION['message'] = "them moi san pham  thanh cong";
            header("Location: /");
        } else {
            View::render('home/create.php');
        }
    }

    public function update($id)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $productModel = new Product();
        $product = $productModel->where('id', $id)->first();
        if ($requestMethod === "POST") {
            if (isset($_FILES['image'])) {
                $uploadImage = new UploadImage();
                $imageUrl = $uploadImage->upload($_FILES['image']);
            }
            $productModel->where('id', $id)->update([
                "name" => $_POST['name'],
                "decription" => $_POST['decription'],
                "price" => $_POST['price'],
                "image" => $imageUrl ?? null,
            ]);
            $_SESSION['message'] = "cap nhat san pham    thanh cong";
            header("Location: /");
        } else {
            View::render('home/update.php', [
                'product' => $product,
            ]);
        }
    }

    public function delete($id)
    {
        $productModel = new Product();
        $productModel->where('id', $id)->delete();
        header("Location: /");
    }
}