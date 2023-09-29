<?php

namespace aspsierra\phpBasicFw\database;

use aspsierra\phpBasicFw\Model;
use aspsierra\phpBasicFw\Application;

abstract class DbModel extends Model
{
    /**
     * Table's name in DB
     * @return  string
     */
    abstract public static function tableName(): string;

    /**
     * Attributes to upload
     * @return  array  
     */
    abstract public function attributes(): array;

    abstract static public function primaryKey(): string;

    /**
     * upload validated data
     * @return  mixed
     */
    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn ($attr) => ":$attr", $attributes);
        $statement = self::prepare(
            "INSERT INTO $tableName 
            (" . implode(',', $attributes) . ") 
            VALUES(" . implode(',', $params) . ")"
        );
        foreach ($attributes as $attribute) {
            $statement->bindParam(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }

    public static function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(" AND ", array_map(fn ($attr) => "$attr = :" . $attr, $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }
    /**
     * Retrieve data from db
     * @param   array  $where   values to select data
     * @param   string  $opt    option, (AND or OR)
     *
     * @return  [type]          [return description]
     */
    public static function select($where, string $opt = ""){
        $data = [];
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(" $opt ", array_map(fn ($attr) => "$attr = :" . $attr, $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        while ($row = $statement->fetchObject(static::class)){
            array_push($data, $row);
        }
        return $data;
    }

    /**
     * Prepare a given sql statement
     * @param   string  $sql  [$sql description]
     * @return  mixed
     */
    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}
