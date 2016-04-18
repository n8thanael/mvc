<?php

namespace libs\nate\CRUD;

/*
 * read_db contains echo_in_pre($display) - which is useful as well as several privage/public parameters
 * 
 * receives an array (partial if necessary) of data to udpate
 * receives a table to update
 * first it gets table headers
 * then it rejects any non-matching table headers with an error
 * then it checks to see if any data will change 
 * if NO changes, it will respond with "no change"
 * if there IS a change it will respond with which columns have updated.
 */

class update_db extends read_db {

    //public $get = '';
    //private $dbh;
    //public $result;
    //private $sql;
    //private $param;
    //private $debug;
        
    // sets up in-class parameters and routes to correct methods
    function __construct($dbh, $sql = false, $param = false, $debug = false) {
        // $this->sql = "Sql Query from child;";
        // $this->echo_in_pre('a fake display from child');
        parent::__construct($dbh,$sql,$param,$debug);
    }
}
