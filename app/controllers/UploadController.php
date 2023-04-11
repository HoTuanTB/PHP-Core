<?php
require(__DIR__ . '/../Model/Image.php');
//require(__DIR__ . '/../../views/image.php');

class UploadController
{
    protected $action;

    public function __construct($action)
    {
        $this->$action();
    }

    public function upload()
    {
        if (!file_exists('img')) {
            mkdir('img', 0777, true);
        }
        if ($_FILES["file"]) {
            $path = "img/" . $_FILES['file']['name'];
            $file = $_FILES['file']['tmp_name'];
            if (move_uploaded_file($file, $path)) {
                $imageUrl = 'http://localhost/' . $path;
                $image = new \Model\Image();
                $image->uploadImage($imageUrl);
                echo "Tải tập tin thành công";
            } else {
                echo "Tải tập tin thất bại";
            }
        }

        $getImages = new \Model\Image();
        $data = $getImages->getData();
    }
}
