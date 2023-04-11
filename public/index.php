<link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
<?php
require(__DIR__ . '/../app/Model/Product.php');
$productModel = new \Model\Product();
//$productModel->insertValue([
//    "name" => "sanpham1",
//    "price" => 100,
//    "description" => "tuan",
//]);

//$productModel
//    ->where('id', 11)
//    ->update([
//        "name" => "basdwad",
//    ]);

//$productModel
//    ->where('id', 11)
//    ->updateUp([
//        "name" => "nnn",
//    ]);

//$productModel
//    ->where('id', 12)
//    ->delete();

//$data = $productModel
//    ->select('name')
//    ->join("product_tag", "products.id=product_tag.product_id")
//    ->where('id', 16)
//    ->orderBy("name")
//    ->having("tuan")
//    ->groupBy("name")
//    ->limit(2, 2)
//    ->get();
//echo "<pre>";
//print_r($data);

// first()


//$data = $productModel
//    ->select('*')
//    ->where('name','sanpham1')
//    ->first();
//echo "<pre>";
//print_r($data);


// find()


$data = $productModel
    ->find([2,4]);
echo "<pre>";
print_r($data);


//https://www.ibm.com/docs/en/db2/9.7?topic=rqrs-fetching-rows-columns-from-result-sets