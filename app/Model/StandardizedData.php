<?php

class HasStandardizedData
{


    public function standardizedData($dataRelation, $dataMain, $oneToMany)
    {
//        list($table, $foreign) = array_values(reset($oneToMany));
        $table = $oneToMany['tableRelation'];
        $foreign = $oneToMany['foreignKey'];
        foreach ($dataRelation as $dataRelationItem) {
            $dataResponse[$dataRelationItem->$foreign][] = $dataRelationItem;
        }

        foreach ($dataMain as $dataMainItem) {
            $dataMainItem->$table = $dataResponse[$dataMainItem->id];
        }

        return $dataMain;
    }
}