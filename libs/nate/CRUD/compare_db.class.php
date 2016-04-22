<?php

namespace libs\nate\CRUD;

/*
 * receives an array (partial if necessary) of data to udpate
 * receives a table to update
 * first it gets table headers
 * then it rejects any non-matching table headers with an error
 * then it checks to see if any data will change 
 * if NO changes, it will respond with "no change"
 * if there IS a change it will respond with which columns have updated.
 */

class compare_db extends read_db {

    // sets up in-class parameters and routes to correct methods
    function __construct($dbh, $sql = false, $param = false, $debug = false) {
        parent::__construct($dbh,$sql,$param,$debug);
        echo "<br>compare_db worked.";
    }
}
