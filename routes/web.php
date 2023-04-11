<?php
require(__DIR__ . '/../app/controllers/UploadController.php');
$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($request_uri == '') {
    var_dump("khong co view nay");
    die();
} elseif ($request_uri == 'images') {
//    new UploadController("upload");
    require(__DIR__ . '/../views/image.php');
} elseif ($request_uri == 'contact') {
    require 'views/contact.php';
} elseif ($request_uri == 'app/controllers/UploadController/upload') {
    new UploadController("upload");
} else {
    var_dump("duong dan khong chinh xac");
    die();
}