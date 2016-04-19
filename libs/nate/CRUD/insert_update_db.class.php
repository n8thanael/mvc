<?php

namespace libs\nate\CRUD;

/*
 * inherits echo_in_pre($display) - which is useful as well as several privage/public parameters
 * inherits variable table, columns and a whitelist to keep things safe from user input
 * triggers when the parent construct is able to find a multi_dim array in $this->param['fields'];
 * works on simple INSERT and UPDATE queries...NOT MAGICAL
 * 
 *      INSERT must start with INSERT at position w/ VALUES($array)
 *      EX:
 *          insert into :table(:cola,:colb) values($array);
 * 
 *      INSERT-UPDATE must use structure: INSERT - VALUES() 'ON DUPLICATE KEY UPDATE'
 *      EX: 
 *          $sql = 'insert into :table(:cola,:colb) values($array) ON DUPLICATE KEY UPDATE id=VALUES(:cola),:colb = VALUES(:colb);';""
 *          $param['table'] = array(':table' => 'users');
            $param['columns']= array(':cola' => 'id', ':colb' => 'login');
            $param['fields']= array(
                array(':id' => 3, ':login' => 'jimmy'),
                array(':id' => 4, ':login' => 'dan'));
            $param['batch'] = 10;
            $param['display'] = true;
 */

class insert_update_db extends read_db {

    private $batch;
    private $display;
    private $sqlfields;

    // sets up in-class parameters and routes to correct methods
    function __construct($dbh, $sql = false, $param = false, $debug = false) {
        parent::__construct($dbh, $sql, $param, $debug);
        if ($this->multi_dimensional_trigger) {

            isset($param['batch']) && is_int($param['batch']) ? $this->batch = $param['batch'] : $this->batch = false;
            isset($param['display']) && $param['display'] == true ? $this->display = true : $this->display = false;

            if ((stripos(strtoupper($this->sql), 'INSERT') === 0) || (
                stripos(strtoupper($this->sql), 'ON DUPLICATE KEY UPDATE') > 0 ))
            {
                $this->str_replace_columns_tables();
                $this->prepare_sql_values_for_insert_update();
                $this->sql_query();
            } 

            if ($this->batch) {
                // create batch tomorrow.
            }
        }
    }
    
    private function prepare_sql_values_for_insert_update() {
        $j = count($this->fields);
        $sqlvalues = "VALUES";
        $this->sqlfields = array();
        $keys = array_keys($this->fields[0]);
        for ($i = 0; $i < $j; $i++) {
            $sqlvalues .= '(';
            foreach ($this->fields[$i] as $v) {
                array_push($this->sqlfields, $v);
            };
            $sqlvalues .= substr(str_repeat('?,', count($keys)), 0, -1);
            $sqlvalues .= '),';
        }
        $sqlvalues = substr($sqlvalues, 0, -1);
        $this->sql = preg_replace('/VALUES[ ]{0,2}\(\$[Aa]{1}rray\)|[Vv]{1}alues[ ]{0,2}\(\$[Aa]{1}rray\)/', $sqlvalues, $this->sql);
    }

    private function sql_query() {
        $this->dbh = $this->dbh->prepare($this->sql);
        echo '<pre>';
        var_dump($this->sqlfields);
        echo '</pre>';
        $this->dbh->execute($this->sqlfields);
        if ($this->debug) {
            $this->echo_in_pre($this->dbh->errorInfo());
        }
        return $this->dbh->fetchall(\PDO::FETCH_ASSOC);
    }

}
