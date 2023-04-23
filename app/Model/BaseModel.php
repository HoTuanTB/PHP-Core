<?php

namespace Model;
class BaseModel
{
    protected $table;
    private $pdo;

    private $where;

    protected $select;

    protected $groupBy;

    protected $orderBy;

    protected $having;

    private $limit;

    private $offset;

    private $join = [];

    private $dataWhere;

    private $sql;

    private $attribute;

    protected $getName;

    private $oneToMany = [];

//    public function __set($name, $value)
//    {
//        $this->attribute [$name] = $value;
//    }

    public function __get($name)
    {
        $this->$name();
    }


    public function __construct()
    {
        $this->connectDatabase();
    }

    public function insert($data)
    {
        $columnKey = array_keys($data);
        $columns = implode(', ', $columnKey);
        $placeholdersSql = array_map(function ($item) {
            return ":$item";
        }, $columnKey);
        $placeholdersSql = implode(', ', $placeholdersSql);
        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholdersSql)";
        $stmt = $this->pdo->prepare($sql);
        $dataExecute = $stmt->execute($data);
        if ($dataExecute) {
            $isId = $this->pdo->lastInsertId();
        }
        if ($isId) {
            echo $isId;
//            return $isId;
        }
        return false;
    }

    public function insertValue($data)
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
        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholdersSql)";
        $stmt = $this->pdo->prepare($sql);
        $dataExecute = $stmt->execute($value);


        if ($dataExecute) {
            $isId = $this->pdo->lastInsertId();
        }
        if ($isId) {
            echo $isId;
        }
        return false;
    }

    public function update($data)
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
        $sql = "UPDATE $this->table SET $placeholdersSql";
        if ($this->where) {
            foreach ($this->where as $valueWhere) {
                $whereValue[] = $valueWhere["column"] . $valueWhere['operator'] . "?";
                $setValue[] = $valueWhere["value"];
            }
            $placeholdersSql = implode('AND', $whereValue);
            $sql = "UPDATE $this->table SET $placeholdersSql WHERE $placeholdersSql";
        }
        $stmt = $this->pdo->prepare($sql)->execute($setValue);
        if ($stmt) {
            $isId = $this->pdo->lastInsertId();
            echo "thanh cong" . $isId;
        }
        if ($isId) {
            echo "that bai";
        }
        return false;
    }

    public function updateUp($data)
    {
        $whereValue = [];
        $setName = [];
        foreach ($data as $key => $value) {
            $setName[] = "$key" . '=:' . "$key";
        }

        $placeholdersSql = implode(', ', $setName);
        foreach ($this->where as $valueWhere) {
            $keyWhere = "where_" . $key;
            $whereValue[] = $valueWhere["column"] . $valueWhere['operator'] . ":" . $keyWhere;
            $data[$keyWhere] = $valueWhere['value'];
        }
        $placeholderValuesSql = implode('AND', $whereValue);
        $sql = "UPDATE $this->table SET $placeholdersSql WHERE $placeholderValuesSql";
        $stmt = $this->pdo->prepare($sql)->execute($data);

        if ($stmt) {
            $isId = $this->pdo->lastInsertId();
            echo "thanh cong" . $isId;
        }
        if ($isId) {
            echo "that bai";
        }
        return false;
    }

    public function delete()
    {
        foreach ($this->where as $key => $valueWhere) {
            $keyWhere = "where_" . $key;
            $whereValue[] = $valueWhere["column"] . $valueWhere['operator'] . ":" . $keyWhere;
            $data[$keyWhere] = $valueWhere['value'];
        }
        $where = implode(' AND ', $whereValue);
        $sql = "DELETE FROM $this->table WHERE $where";
        $stmt = $this->pdo->prepare($sql)->execute($data);
        if ($stmt) {
            echo "xoa thanh cong";
        } else {
            echo "xoa that bai";
        }
    }

    public function get()
    {
        $this->query();
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($this->dataWhere);
        $dataMain = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $rules = $this->oneToMany;
        if ($this->oneToMany) {
            foreach ($rules as $rule) {
                $oneToMany = new \HasOneToMany();
                $dataRelation = $oneToMany->oneToMany($dataMain, $rule, $this->pdo);
                $normalData = new \HasStandardizedData();
                $data = $normalData->standardizedData($dataRelation, $dataMain, $rule);
            }
        }
//        if ($this->oneToMany) {
//            $oneToMany = new \HasOneToMany();
//            $dataRelation = $oneToMany->oneToMany($dataMain, $this->oneToMany, $this->pdo);
//            $normalData = new \HasStandardizedData();
//            $data = $normalData->standardizedData($dataRelation, $dataMain, $this->oneToMany);
//        }

        echo "<pre>";
        print_r($data);
    }

    public function first()
    {
        $this->query();
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($this->dataWhere);
        $products = $stmt->fetch(\PDO::FETCH_ORI_FIRST);
        echo "<pre>";
        print_r($products);
        die();
    }

    public function find($valuePrimaryKeys, $select = '*')
    {
        $tableName = $this->table;
        $query = "SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'";
        $result = $this->pdo->query($query);
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
            $sql = "SELECT * FROM $this->table WHERE $queryPrimaryOr";
            $stmt = $this->pdo->prepare($sql);
            foreach ($keysbind as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
        } else {
            $sql = "SELECT * FROM $this->table WHERE $primaryKey = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $valuePrimaryKeys, \PDO::PARAM_INT);
        }
        $stmt->execute();
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($products);
        die();
    }

    public function save()
    {
        $filable = $this->getFilable();
        foreach ($filable as $key => $value) {
            $functionGet = "get" . ucfirst($value);
            $attribute[$value] = $this->$functionGet();
        }

        $this->insert($attribute);
    }

    public function whereArray($conditionArrays)
    {
//        var_dump(count($conditionArrays));
//        die();
//        if (is_array($conditionArrays) && count($conditionArrays) == 1) {
//            $conditionArrays = [$conditionArrays];
//        }

//        echo "<pre>";
//        print_r($conditionArrays);
//        die();

        foreach ($conditionArrays as $key => $conditionArray) {
            $this->where[] = [
                'column' => $conditionArray[0],
                'operator' => $conditionArray[1],
                'value' => $conditionArray[2],
            ];
        }
        return $this;
    }

    public function oneToMany()
    {
        $categoriesSql = "SELECT * FROM category";
        $stmt = $this->pdo->prepare($categoriesSql);
        $stmt->execute();
        $categories = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $categoryId = [];
        foreach ($categories as $categoryItems) {
            $categoryId[] = $categoryItems->id;
        }

        $formatCategoryId = implode(", ", $categoryId);
        $productsSql = "SELECT * FROM products where category_id in ($formatCategoryId) ";
        $stmt = $this->pdo->prepare($productsSql);
        $stmt->execute();
        $products = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $response = [];
        foreach ($products as $product) {
            $categoryId = $product->category_id;
            $response[$categoryId][] = $product;
        }

        foreach ($categories as $categoryItem) {
            $categoryItem->product = $response[$categoryItem->id];
        }


        echo "<pre>";
        print_r($categories);

    }

    public function belongsTo()
    {
        $productsSql = "SELECT * FROM products";
        $stmt = $this->pdo->prepare($productsSql);
        $stmt->execute();
        $products = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $arrayCategoryId = [];
        foreach ($products as $product) {
            $arrayCategoryId[] = $product->category_id;
        }

        $formatCategoryId = implode(", ", $arrayCategoryId);

        $categoriesSql = "SELECT * FROM category where id in ($formatCategoryId) ";
        $stmt = $this->pdo->prepare($categoriesSql);
        $stmt->execute();
        $categories = $stmt->fetchAll(\PDO::FETCH_OBJ);
        foreach ($products as $product) {
            $categoryId = $product->category_id;
            $product->category = $categories[$categoryId];
        }
        echo "<pre>";
        print_r($products);
    }


    public function manyToMany()
    {
        $productsSql = "SELECT * FROM products";
        $stmt = $this->pdo->prepare($productsSql);
        $stmt->execute();
        $products = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $idProducts = [];
        foreach ($products as $product) {
            $idProducts[] = $product->id;
        }

        $formatProductId = implode(", ", $idProducts);

        $tagsSql = "SELECT * FROM tags INNER JOIN product_tag ON tags.id = product_tag.tag_id WHERE product_tag.product_id IN ($formatProductId)";
        $stmt = $this->pdo->prepare($tagsSql);
        $stmt->execute();
        $tags = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $productIdTag = [];

        foreach ($tags as $tag) {
            $productIdTag[$tag->product_id][] = $tag;
        }

        foreach ($products as $product) {
            $product->tags = $productIdTag[$product->id];
        }

        echo "<pre>";
        print_r($products);

    }

    public function manyToManyCP()
    {
        $tagsSql = "SELECT * FROM tags";
        $stmt = $this->pdo->prepare($tagsSql);
        $stmt->execute();
        $tags = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $idTags = [];
        foreach ($tags as $tag) {
            $idTags[] = $tag->id;
        }

    }

    public function hasMany($tableClass, $foreign)
    {
        $classInstance = new $tableClass();
        $this->oneToMany[] = [
            'tableRelation' => $classInstance->table,
            'foreignKey' => $foreign,
            'primaryKey' => $classInstance->primaryKey
        ];
        return $this;
    }


    public function with($modelRelation)
    {
        foreach ($modelRelation as $modelRelationItem) {
            $this->$modelRelationItem();
        }

        return $this;
    }

    public function query()
    {
        $dataWhere = [];
        $sql = "SELECT ";
        if ($this->select) {
            $sql = $sql . $this->select . ' FROM ' . $this->table;
        }
        if ($this->where) {
            list($placeholderValuesSql, $dataWhere) = $this->whereAnd();
            $sql = $sql . " WHERE " . $placeholderValuesSql;
        }
        if ($this->groupBy) {
            $sql = $sql . " GROUP BY " . $this->groupBy;
        }
        if ($this->having) {
            $sql = $sql . " HAVING " . $this->having;
        }

        if ($this->orderBy) {
            $sql = $sql . " ORDER BY " . $this->orderBy;
        }
        if (is_numeric($this->limit)) {
            if (is_numeric($this->offset)) {
                $sql = $sql . ' LIMIT ' . $this->offset . ',' . $this->limit;
            } else {
                $sql = $sql . ' LIMIT ' . $this->limit;
            }
        }

        if ($this->join) {
            foreach ($this->join as $valueJoin) {
                $sql = $sql . $valueJoin['type'] . " JOIN " . $valueJoin['tableJoin'] . " ON " . $valueJoin["condition"];
            }
        }

        $this->dataWhere = $dataWhere;
        $this->sql = $sql;
    }

    public function groupBy($groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function having($having)
    {
        $this->having = $having;
        return $this;
    }

    private function whereAnd()
    {
        $dataWhere = [];
        foreach ($this->where as $key => $valueWhere) {
            $keyWhere = "where_" . $key;
            $whereValue[] = $valueWhere["column"] . $valueWhere['operator'] . ":" . $keyWhere;
            $dataWhere[$keyWhere] = $valueWhere['value'];
        }

        $placeholderValuesSql = implode('AND', $whereValue);
        return [$placeholderValuesSql, $dataWhere];
    }

    public function select($select)
    {
        if ($select) {
            $this->select = $select;
        } else {
            $this->select = "*";
        }
        return $this;
    }

    public function where()
    {
        $numArg = func_num_args();
        $arrArg = func_get_args();
        if ($numArg == 2) {
            $column = $arrArg[0];
            $operator = '=';
            $value = $arrArg[1];
        } elseif ($numArg == 3) {
            $column = $arrArg[0];
            $operator = $arrArg[1];
            $value = $arrArg[2];
        } else {
            $column = null;
            $operator = null;
            $value = null;
        }

        $this->where[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;

    }

    public function join($tableJoin, $condition)
    {
        $this->join[] = [
            "type" => " INNER",
            "tableJoin" => $tableJoin,
            "condition" => $condition,
        ];

        return $this;
    }

    public function connectDatabase()
    {
        $host = 'mysql';
        $db = 'main';
        $user = 'root';
        $pass = 'root';
        $port = "3306";

        $dsn = "mysql:host=$host;dbname=$db;port=$port";
        try {
            $this->pdo = new \PDO($dsn, $user, $pass);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}
