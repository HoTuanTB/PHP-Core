<?php

class InsertValue
{

    public function __construct()
    {
    }

    public function insertValue($data, $table, $pdo)
    {
        $columnKey = array_keys($data);
        $columns = implode(', ', $columnKey);
        $placeholdersSql = [];
        $value = [];
        foreach ($data as $values) {
            $placeholdersSql[] = "?";
            $value[] = $values;
        }
        $placeholdersSql = implode(', ', $placeholdersSql);
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholdersSql)";
        $stmt = $pdo->prepare($sql);
        $dataExecute = $stmt->execute($value);


        if ($dataExecute) {
            $isId = $pdo->lastInsertId();
        }
        if ($isId) {
            echo $isId;
        }
        return false;
    }
}