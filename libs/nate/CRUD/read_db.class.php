<?php

namespace libs\nate\CRUD;

/*
 * selects information from DB and returns results based on $param.
 * 
 * for the most basic result...simply use: 
 *      read_db($PDO [object], $sql [string]);
 * 
 * Get error information (echo'd to screen within: <pre> tags), by including "true" at the end of any request:
 *      read_db($PDO [object], $sql [string], true);
 *      read_db($PDO [object], $sql [string], $param [array], true);
 */

class read_db {

    public $get = '';
    private $dbh;
    public $result;
    private $sql;
    private $param;
    private $debug;
    
    // sets up in-class parameters and routes to correct methods
    function __construct($dbh, $sql = true, $param = "", $debug = false) {
        $this->dbh = $dbh;
        $this->sql = $sql;
        $this->param = $param;
        $this->debug = $debug;
        //
        if ((is_string($sql)) && ($this->param === true || $param === '')) {
            // Return straight sql query requested.
            if($this->param === true){$this->debug = $this->param;}
            $this->result = $this->simple_sql_query();
        }
    }

    // since there are no $this->params, simply return the SQL result
    private function simple_sql_query() {
        $this->dbh = $this->dbh->prepare($this->sql);
        $this->dbh->execute();
        if($this->debug){$this->echo_in_pre($this->dbh->errorInfo());}
        return $this->dbh->fetchall(\PDO::FETCH_ASSOC);
    }
    
    // error info if it exists is wrapped in <pre> for easy viewing as well as sql display
    private function echo_in_pre($display){
        echo '<pre>';
        echo 'sql query:  ' . $this->sql . '<br>';
        var_dump($display);
        echo '</pre>';
    }
        

}
