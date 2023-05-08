<?php

namespace Views;
class View
{
    public static function render($path, $data = [])
    {
        if (!empty($data) && is_array($data)) {
            extract($data);
        }

        require __DIR__ . '/' . $path;
    }
}