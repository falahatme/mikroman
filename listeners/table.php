<?php
/**
 * Created by PhpStorm.
 * User: mohammad falahat
 * Website: www.falahat.me
 * Date: 10/10/2014
 * Time: 12:13 PM
 */

class table {


    private static $table = "";
    private static $fields = array("*");
    private static $values = array("");
    private static $where = "";
    private static $group = "";
    private static $having = "";
    private static $order = "";
    private static $limitStart = 0;
    private static $limitCount = 0;


    public function __construct($table){
        self::$table = '`'.$table.'`';
        return $this;
    }



    public function fields(array $fields){

        if(array_keys($fields) !== range(0, count($fields) - 1)){

            self::$fields = array();
            self::$values = array();
            foreach($fields as $field=>$value){
                self::$fields[] = $field;
                self::$values[] = Database::$db->real_escape_string($value);
            }

        }else{

            self::$fields = $fields;

        }
        return $this;

    }

    public function where(array $conditions){
        self::$where = implode(' AND ', $conditions);
        return $this;
    }

    public function group(array $fields){
        self::$group = '`'.implode('`, `', $fields).'`';
        return $this;
    }

    public function having(array $conditions){
        self::$having = implode(' AND ', $conditions);
        return $this;
    }

    public function order(array $orders){
        $ordersArray = array();
        foreach($orders as $key => $value){
            if(!$key){
                $key = $value;
                $value = "ASC";
            }else{
                $value = "DESC";
            }
            $ordersArray[] = " `$key` $value ";
        }
        self::$order = implode(', ', $ordersArray);
        return $this;
    }

    public function limit($start=0, $count=0){
        self::$limitStart = intval($start);
        self::$limitCount = intval($count);
        return $this;
    }

    public function run($showQuery = false){

        $query = "SELECT ";

        if(self::$fields[0] == "*"){
            $query .= "*";
        }else{
            $query .= implode('`, `', self::$fields);
        }

        $query .= " \r\nFROM " . self::$table;

        if(strlen(self::$where) > 0){
            $query .= " \r\nWHERE " . self::$where;
        }

        if(strlen(self::$group) > 0){
            $query .= " \r\nGROUP BY " . self::$group;
        }

        if(strlen(self::$having) > 0){
            $query .= " \r\nHAVING " . self::$having;
        }

        if(strlen(self::$order) > 0){
            $query .= " \r\nORDER BY " . self::$order;
        }

        if(!(self::$limitStart == 0 and self::$limitCount == 0)){
            $query .= " \r\nLIMIT " . self::$limitStart . ', ' . self::$limitCount;
        }

        if($showQuery)
            echo $query;

        $this->reset();
        return Database::$db->query($query);

    }

    public function insert($showQuery = false){

        $query = "INSERT INTO " . self::$table;

        $query .= " \r\n(`" . implode('`, `', self::$fields) . "`)";

        $query .= " \r\nVALUES";

        $query .= " \r\n('" . implode('\', \'', self::$values) . "')";

        $this->reset();

        if($showQuery)
            echo $query;

        if(Database::$db->query($query))
            return Database::$db->insert_id;
        else
            return false;

    }

    public function update($showQuery = false){

        $query = "UPDATE " . self::$table . " SET ";
        $updates = array();

        foreach(self::$fields as $field){

            $updates[] = " \r\n`$field` = '" . array_shift(self::$values) . "'";

        }

        $query .= implode(', ', $updates);

        if(strlen(self::$where) > 0){

            $query .= " \r\nWHERE " . self::$where;

        }

        if($showQuery)
            echo $query;

        $this->reset();
        return Database::$db->query($query);

    }


    public function delete($showQuery = false){

        $query = "DELETE FROM " . self::$table;

        if(strlen(self::$where) > 0){

            $query .= " \r\nWHERE " . self::$where;

        }

        if($showQuery)
            echo $query;

        $this->reset();
        return Database::$db->query($query);

    }


    private function reset(){

        self::$table = "";
        self::$fields = array("*");
        self::$values = array();
        self::$where = "";
        self::$group = "";
        self::$having = "";
        self::$order = "";
        self::$limitStart = 0;
        self::$limitCount = 0;

    }


} 