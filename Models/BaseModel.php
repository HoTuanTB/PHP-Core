<?php

namespace Models;
class BaseModel
{
    protected $table;
    private $pdo;

    private $where;

    protected $select = "*";

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

    public function __set($name, $value)
    {
        $this->attribute [$name] = $value;
    }

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
            return $isId;
        }
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
            $placeholdersWhereSql = implode(' AND ', $whereValue);
            $sql = "UPDATE $this->table SET $placeholdersSql WHERE $placeholdersWhereSql";
        }
        $stmt = $this->pdo->prepare($sql)->execute($setValue);
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
    }

    public function get()
    {
        $this->query();
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($this->dataWhere);
        $products = $stmt->fetchAll(\PDO::FETCH_OBJ);
        return $products;
    }

    public function first()
    {
        $this->query();
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($this->dataWhere);
        $product = $stmt->fetch(\PDO::FETCH_ORI_FIRST);
        return $product;
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
        foreach ($conditionArrays as $key => $conditionArray) {
            $this->where[] = [
                'column' => $conditionArray[0],
                'operator' => $conditionArray[1],
                'value' => $conditionArray[2],
            ];
        }
        return $this;
    }

    public function with($modelRelation)
    {
        return $this->$modelRelation();
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
        if ($numArg === 2) {
            $column = $arrArg[0];
            $operator = '=';
            $value = $arrArg[1];
        } elseif ($numArg === 3) {
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
        $db = 'mvc_crud';
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