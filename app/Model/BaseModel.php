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

//
//        echo "<pre>";
//        print_r($sql);
//        die();


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataWhere);
        $products = $stmt->fetchAll(\PDO::FETCH_OBJ);

        echo "<pre>";
        print_r($products);
        die();


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
        $db = 'day4';
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