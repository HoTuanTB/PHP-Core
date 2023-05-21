<?php

namespace App\Helpers;
class UploadImage
{
    public function upload($image)
    {
        $path = "img/" . $image['name'];
        $file = $image['tmp_name'];
        if (move_uploaded_file($file, $path)) {
            $imageUrl = 'http://localhost/' . $path;
            return $imageUrl;
        } else {
            echo "Log::error Upload fail";
        }
    }
}