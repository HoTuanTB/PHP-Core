<?php

class Find
{
    public function __construct()
    {

    }

    public function find($tableName,$pdo,$valuePrimaryKeys)
    {
        $query = "SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'";
        $result = $pdo->query($query);
        $primaryKeyArray = $result->fetch(\PDO::FETCH_ASSOC);
        $primaryKey = $primaryKeyArray['Column_name'];
        $queryPrimary = [];
        $keysbind = [];
        if (is_array($valuePrimaryKeys)) {
            foreach ($valuePrimaryKeys as $key => $valuePrimaryKey) {
                $queryPrimary[] = "$primaryKey = :$key";
                $keysbind[] = $valuePrimaryKey;
            }

            $queryPrimaryOr = implode(' OR ', $queryPrimary);
            $sql = "SELECT * FROM $tableName WHERE $queryPrimaryOr";
            $stmt = $pdo->prepare($sql);
            foreach ($keysbind as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
        } else {
            $sql = "SELECT * FROM $tableName WHERE $primaryKey = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $valuePrimaryKeys, \PDO::PARAM_INT);
        }
        $stmt->execute();
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($products);
        die();
    }
}