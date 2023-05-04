<?php

class Insert
{

    public function __construct()
    {
    }

    public function insert($data, $table, $pdo)
    {
        $columnKey = array_keys($data);
        $columns = implode(', ', $columnKey);
        $placeholdersSql = array_map(function ($item) {
            return ":$item";
        }, $columnKey);
        $placeholdersSql = implode(', ', $placeholdersSql);
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholdersSql)";


        $stmt = $pdo->prepare($sql);
        $dataExecute = $stmt->execute($data);
        if ($dataExecute) {
            $isId = $pdo->lastInsertId();
        }
        if ($isId) {
            echo $isId;
//            return $isId;
        }
        return false;
    }
}