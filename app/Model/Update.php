<?php

class Update
{

    public function __construct()
    {
    }

    public function update($data, $table, $where, $pdo)
    {
        $setValue = [];
        $whereValue = [];
        $setName = [];
        $arrayKey = array_keys($data);
        foreach ($data as $key => $value) {
            $setValue[] = $value;
            $setName[] = "$key=?";
        }
        $placeholdersSql = implode(', ', $setName);
        $sql = "UPDATE $table SET $placeholdersSql";
        if ($where) {
            foreach ($where as $valueWhere) {
                $whereValue[] = $valueWhere["column"] . $valueWhere['operator'] . "?";
                $setValue[] = $valueWhere["value"];
            }
            $placeholdersSql = implode('AND', $whereValue);
            $sql = "UPDATE $table SET $placeholdersSql WHERE $placeholdersSql";
        }
        $stmt = $pdo->prepare($sql)->execute($setValue);
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