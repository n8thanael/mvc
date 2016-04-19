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

    protected $sqlfields;
    protected $obj; // PDO from $this->dbh

    // sets up in-class parameters and routes to correct methods
    function __construct($dbh, $sql = false, $param = false, $debug = false) {
        parent::__construct($dbh, $sql, $param, $debug);
        if ($this->multi_dimensional_trigger) {

            // check to see if the query is formmatted, correclty prior to proceeding
            // else error & die;
            if ((stripos(strtoupper($this->sql), 'INSERT') === 0) || (
                stripos(strtoupper($this->sql), 'ON DUPLICATE KEY UPDATE') > 0 ) || ( 
                stripos(strtoupper($this->sql), 'VALUES($ARRAY)') > 0 ))
            {
                $this->str_replace_columns_tables();
                $this->prepare_sql_values_for_insert_update();
                $this->sql_query();
            } else {
                if ($this->debug) {
                    $this->echo_in_pre($this->dbh->errorInfo());
                    echo 'Sql Query: <u>"' . $this->sql . '"</u> Query must have a specific syntax.  Please read class documentation.';
                    $this->error = true;
                    die;
                }
            }
        } else {
            if ($this->debug) {
                $this->echo_in_pre($this->dbh->errorInfo());
                echo 'There has been an class inheritance error.';
                $this->error = true;
                die;
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
        // looks for string values($array) to replace it with $sqlvalues
        $regex = '/VALUES\(\$[Aa]{1}rray\)|[Vv]{1}alues\(\$[Aa]{1}rray\)/';
        $this->sql = preg_replace($regex, $sqlvalues, $this->sql);
    }

    protected function sql_query() {
        $this->obj = $this->dbh->prepare($this->sql);
        $this->obj->execute($this->sqlfields);
        if ($this->debug) {
            $this->echo_in_pre($this->obj->errorInfo());
        }
        return $this->obj->fetchall(\PDO::FETCH_ASSOC);
    }
    
    
    function this(){
    $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $dbh->execute();
            $idlist = $dbh->fetchAll();
    }

}

