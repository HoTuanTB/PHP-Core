<?php

class HasOneToMany
{
    public function oneToMany($dataMain, $oneToMany, $pdo)
    {
        $primaryKey = $oneToMany['primaryKey'];
        foreach ($dataMain as $category) {
            $idCategory[] = $category->$primaryKey;
        }
        $formatCategoryId = implode(", ", $idCategory);

//        foreach ($oneToMany as $itemOneToMany) {
//            $tableRelation = $itemOneToMany['tableRelation'];
//            $foreignKey = $itemOneToMany['foreignKey'];
//        }

        $tableRelation = $oneToMany['tableRelation'];
        $foreignKey = $oneToMany['foreignKey'];

        $productsSql = "SELECT * FROM $tableRelation WHERE $foreignKey  IN ($formatCategoryId)";
        $stmt = $pdo->prepare($productsSql);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_OBJ);


//        echo "<pre>";
//        print_r($data);
//        die();

        return $data;
    }

}
