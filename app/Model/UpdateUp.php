<?php

class UpdateUp
{

    public function __construct()
    {
    }

    public function updateUp($data, $table, $where, $pdo)
    {
        $whereValue = [];
        $setName = [];
        foreach ($data as $key => $value) {
            $setName[] = "$key" . '=:' . "$key";
        }

        $placeholdersSql = implode(', ', $setName);
        foreach ($where as $valueWhere) {
            $keyWhere = "where_" . $key;
            $whereValue[] = $valueWhere["column"] . $valueWhere['operator'] . ":" . $keyWhere;
            $data[$keyWhere] = $valueWhere['value'];
        }
        $placeholderValuesSql = implode('AND', $whereValue);
        $sql = "UPDATE $table SET $placeholdersSql WHERE $placeholderValuesSql";
        $stmt = $pdo->prepare($sql)->execute($data);

        if ($stmt) {
            $isId = $pdo->lastInsertId();
            echo "thanh cong" . $isId;
        }
        if ($isId) {
            echo "that bai";
        }
        return false;
    }
}