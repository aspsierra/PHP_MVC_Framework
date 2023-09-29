<?php

namespace aspsierra\phpBasicFw\database;

use aspsierra\phpBasicFw\Model;
use aspsierra\phpBasicFw\Application;

abstract class DbModel extends Model
{
    public static string $sql = '';
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
    /*public static function select($where, string $opt = ""){
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
    }*/
    /**
     * Build a Select statement
     * @param   mixed  $attr  attributes to search
     */
    public static function select($attr)
    {
        $sql = "SELECT ";
        if (empty($attr)) {
            $sql .= " * ";
        } else {
            $attr = func_get_args();
            $sql .= '(' . implode(', ', $attr)  . ') ';
        }
        self::$sql = $sql;
    }

    /**
     * Choosing table
     * @param   string  $table  table's name
     */
    public static function from($table = "")
    {
        self::$sql .= "FROM " . $table . " ";
    }
    /**
     * Searching conditions
     * @param   string  $key    attribute name
     * @param   string  $value  value to compare
     * @param   string  $op     operation to do
     */
    public static function where($key, $value, $op = '=')
    {
        self::$sql .= "WHERE $key $op $value";
    }

    /**
     * Obtain the data with the specified conditions
     * @param   string  $className  modle to apply
     * @return  array               retrieved data
     */
    public static function get($className)
    {
        try {
            $data = [];
            $statement = self::prepare(self::$sql);
            $statement->execute();
            while ($row = $statement->fetchObject($className)) {
                $data[] = $row;
            }
            return ($data);
        } catch (\Exception $ex) {
            throw new \Exception("Error en la operación", 55);
        }
    }

    /**
     * Prepare a given sql statement
     * @param   string  $sql  [$sql description]
     * @return  pdo
     */
    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}
