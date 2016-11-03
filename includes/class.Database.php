<?php

require_once 'config.php';

class Database
{
    private $db;

    private function __construct()
    {
        global $cfg;
        $this->db = mysqli_connect($cfg['db']['host'], $cfg['db']['user'], $cfg['db']['password']);
        if (!($this->db)) {
            throw new Exception("Can't login to DB", E_USER_ERROR);
        }
        mysqli_select_db($this->db, $cfg['db']['name']);
        mysqli_query($this->db, "set names 'UTF8'");
        mysqli_query($this->db, 'set character set utf8');
        mysqli_set_charset($this->db, 'utf8');
    }

    private function db_qry($query = '')
    {
        $result = mysqli_query($this->db, $query);

        return $result;
    }

    private function escape_string($str)
    {
        return mysqli_real_escape_string($this->db, $str);
    }

    public function log($event, $level)
    {
        $this->insert('log', array(array('event' => $event, 'level' => $level)));
    }

    public function select($sql)
    {
        $hRes = $this->db_qry($sql);
        $arReturn = array();
        while ($row = mysqli_fetch_array($hRes)) {
            $arReturn[] = $row;
        }

        return $arReturn;
    }

    public static function instance()
    {
        static $objDB;

        if (!isset($objDB)) {
            $objDB = new self();
        }

        return $objDB;
    }

    public function insert($table, $arFieldValues)
    {
        mysqli_begin_transaction($this->db);

        foreach ($arFieldValues as $arFieldValue) {
            $fields = array_keys($arFieldValue);
            $values = array_values($arFieldValue);
            $arUpdates = array();
            $escVals = array();
            foreach ($arFieldValue as $field => $val) {
                $val = "'".$this->escape_string($val)."'";
                $escVals[] = $val;
                $arUpdates[] = "$field = $val";
            }
            $sql = " INSERT INTO $table (";
            $sql .= implode(', ', $fields);
            $sql .= ') VALUES (';
            $sql .= implode(', ', $escVals);
            $sql .= ') ON DUPLICATE KEY UPDATE ';
            $sql .= implode(', ', $arUpdates);

            $hRes = $this->db_qry($sql);
        }

        mysqli_commit($this->db);
    }

    public function update($table, $arFieldValues, $arConditions)
    {
        $arUpdates = array();
        foreach ($arFieldValues as $field => $val) {
            $val = "'".$this->escape_string($val)."'";
            $arUpdates[] = "$field = $val";
        }

        $arWhere = array();
        foreach ($arConditions as $field => $val) {
            $val = "'".$this->escape_string($val)."'";
            $arWhere[] = "$field = $val";
        }

        $sql = "UPDATE $table SET ";
        $sql .= implode(', ', $arUpdates);
        $sql .= ' WHERE '.implode(' AND ', $arWhere);

        $hRes = $this->db_qry($sql);
        if (!($hRes)) {
            $err = mysqli_last_error($this->db).NL.$sql;
            throw new Exception($err);
        }
    }

    public function __destruct()
    {
        if (is_resource($this->db)) {
            @mysqli_close($this->db);
        }
    }
}
