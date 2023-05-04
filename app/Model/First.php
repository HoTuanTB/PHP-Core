<?php

class First
{

    public function __construct()
    {

    }

    public function first($pdo, $sql, $dataWhere)
    {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($dataWhere);
        $data = $stmt->fetch(\PDO::FETCH_ORI_FIRST);
        echo "<pre>";
        print_r($data);
        die();
    }
}