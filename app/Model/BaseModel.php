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

    public function __get($name)
    {
        $this->$name();
    }

    public function __construct()
    {
        $this->connectDatabase();
    }
    public function insertValue($data)
    {
        $insertValue = new \InsertValue();
        return $insertValue->insertValue($data, $this->table, $this->pdo);
    }

    public function update($data)
    {
        $update = new \Update();
        return $update->update($data, $this->table, $this->where, $this->pdo);
    }

    public function updateUp($data)
    {
        $update = new \UpdateUp();
        return $update->updateUp($data, $this->table, $this->where, $this->pdo);
    }

    public function delete()
    {
        $delete = new \Delete();
        return $delete->delete($this->table, $this->where, $this->pdo);
    }

    public function get()
    {
        $this->query();
        $get = new \Get();
        return $get->get($this->pdo, $this->sql, $this->dataWhere, $this->oneToMany);
    }

    public function first()
    {
        $this->query();
        $first = new \First();
        return $first->first($this->pdo, $this->sql, $this->dataWhere);
    }

    public function find($valuePrimaryKeys, $select = '*')
    {
        $find = new \Find();
        return $find->find($this->table, $this->pdo, $valuePrimaryKeys);
    }

    public function save()
    {
        $filable = $this->getFilable();
        $save = new \Save();
        $save->save($filable, $this->table, $this->pdo);
    }

    public function whereArray($conditionArrays)
    {
        foreach ($conditionArrays as $key => $conditionArray) {
            $this->where[] = [
                'column' => $conditionArray[0],
                'operator' => $conditionArray[1],
                'value' => $conditionArray[2],
            ];
        }
        return $this;
//        $whereArray = new \WhereArray();
//        $whereArray->whereArray($conditionArrays);
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
        $belongsTo = new \BeLongsTo();
        return $belongsTo->beLongsTo($this->pdo);
    }


    public function manyToMany()
    {
        $manyToMany = new \ManyToMany();
        return $manyToMany->manyToMany($this->pdo);
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
