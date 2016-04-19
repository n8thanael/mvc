<?php

namespace libs\nate\CRUD;

/*
 * selects information from DB and returns results based on $param.
 * $param['table']: table(s) - :key => 'key'
 * $param['columns']: column(s) - :key => 'key'
 * $param['fields']: fields(s) - PDO array of named parameters
 * $param['whitelist'] - if designated provides a safe-guard against table/column user entry error
 *      - all table & column values are checked in_array() against this whitelist.
 * 
 * for the most basic result...simply use: 
 *      read_db($PDO [object], $sql [string]);
 * 
 * Get error information (echo'd to screen within: <pre> tags), by including "true" at the end of any request:
 *      read_db($PDO [object], $sql [string], true);
 *      read_db($PDO [object], $sql [string], $param [array], true);
 * 
 * $this->sql_routing()
 *      this function is heavily manipulated in child occurances of this class
 *      if the $fields array are multi-dimensional child function: insert_update_db class will handle
 */

class read_db {

    protected $get = '';
    protected $dbh;
    protected $sql;
    protected $param;
    protected $debug;
    protected $table;
    protected $columns;
    protected $fields;
    protected $whitelist;
    protected $whitelist_check;
    protected $multi_dimensional_trigger = false;
    protected $error;
    public $result;

    function __construct($dbh, $sql = false, $param = false, $debug = false) {
        // sets up in-class parameters
        $error = false;
        $this->whitelist_check = array();

        if (is_string($sql)) {
            $this->sql = $sql;
        } else {
            $this->error = true;
        }
        if (is_object($dbh)) {
            $this->dbh = $dbh;
        } else {
            $this->error = true;
        }
        $this->param = $param;
        $this->debug = $debug;

        if (isset($this->param['fields']) && is_array($this->param['fields'])) {
            $this->fields = $this->param['fields'];
        }

        // sets up in-class parameters and builds whitelist_check array
        if (isset($this->param['table']) && is_array($this->param['table'])) {
            $this->table = $this->param['table'];
            $this->whitelist_check = array_merge($this->whitelist_check, $this->table);
        }

        if (isset($this->param['columns']) && is_array($this->param['columns'])) {
            $this->columns = $this->param['columns'];
            $this->whitelist_check = array_merge($this->whitelist_check, $this->columns);
        }

        if (isset($this->param['whitelist']) && is_array($this->param['whitelist'])) {
            $this->whitelist = $this->param['whitelist'];
            foreach ($this->whitelist_check as $v) {
                if (!in_array($v, $this->whitelist)) {
                    echo 'Value: <u>"' . $v . '"</u> in query did not match whilelist provided.';
                    $this->error = true;
                    die;
                }
            }
        }

        if ($error) {
            echo 'Minimum class requirement: read_db($PDO [object], $sql [string]);';
            $this->error = true;
            die;
        }
        
        // the base procedure for simple SQL management - inserts & updates with multi-dimensional arrays will override this method.
        
        if (count($this->fields) == count($this->fields, COUNT_RECURSIVE)) {
            $this->sql_routing();
        } else {
            $this->multi_dimensional_trigger = true;
        }
        
    }

    protected function sql_routing(){
        //routes to correct internal methods            
        if (!is_array($this->param)) {
            // Return basic & simple sql query requested.
            if ($this->param == true) {
                $this->debug = $this->param;
            }
            $this->result = $this->simple_sql_query();
        } else {
            $this->str_replace_columns_tables();
            $this->dbh = $this->dbh->prepare($this->sql);
            $this->dbh->execute($this->fields);
            $this->result = $this->dbh->fetchall(\PDO::FETCH_ASSOC);
            if ($this->debug) {
                $this->echo_in_pre($this->dbh->errorInfo());
            }
        }
    }
    
    // since there are no $this->params, simply return the SQL result
    private function simple_sql_query() {
        $this->dbh = $this->dbh->prepare($this->sql);
        $this->dbh->execute();
        if ($this->debug) {
            $this->echo_in_pre($this->dbh->errorInfo());
        }
        return $this->dbh->fetchall(\PDO::FETCH_ASSOC);
    }
    
    protected function str_replace_columns_tables(){
            if ((count($this->param['fields']) > 0) &&
                    (count($this->param['table'] > 0))) {
                $this->sql = str_replace(array_keys($this->columns), array_values($this->columns), $this->sql);
                $this->sql = str_replace(array_keys($this->table), array_values($this->table), $this->sql);
            } else {
                echo 'not enough suffient values in fields or table';
                $this->error = true;
                die;
            }
    }

    // error info if it exists is wrapped in <pre> for easy viewing as well as sql display
    protected function echo_in_pre($display) {
        echo '<pre>';
        echo 'sql query:  ' . $this->sql . '<br>';
        var_dump($display);
        echo '</pre>';
    }

}
