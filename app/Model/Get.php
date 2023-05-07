<?php

class Get
{

    public function __construct()
    {

    }

    public function get($pdo, $sql, $dataWhere, $oneMany)
    {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($dataWhere);
        $dataMain = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $rules = $oneMany;
        if ($oneMany) {
            foreach ($rules as $rule) {
                $oneToMany = new \HasOneToMany();
                $dataRelation = $oneToMany->oneToMany($dataMain, $rule, $pdo);
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
}