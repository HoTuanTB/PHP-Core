<?php

class Delete
{

    public function __construct()
    {
    }

    public function delete($table, $where, $pdo)
    {
        foreach ($where as $key => $valueWhere) {
            $keyWhere = "where_" . $key;
            $whereValue[] = $valueWhere["column"] . $valueWhere['operator'] . ":" . $keyWhere;
            $data[$keyWhere] = $valueWhere['value'];
        }
        $where = implode(' AND ', $whereValue);
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $pdo->prepare($sql)->execute($data);
        if ($stmt) {
            echo "xoa thanh cong";
        } else {
            echo "xoa that bai";
        }
    }
}