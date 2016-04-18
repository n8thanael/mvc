<?php

class test_Model extends model {

    function __construct() {
        parent::__construct();
        $dbh = new database();
        echo 'test model is working <br>';
        //$a = new \libs\nate\CRUD\update_insert_db('howdy','','','','','',true);
        
        $param['table'] = array(':table' => 'users');
        $param['query'] = 'Select :a,:b from :table limit 1;';
        $param['fields']= array(':a' => 'id', ':b' => 'login');


        // example A simple query works...adding TRUE after $sql will return 
        $sql = 'SELECT id,login FROM users limit 1;';
        $a = new \libs\nate\CRUD\read_db($dbh,$sql);
        echo '<pre>';
        print_r($a->result[0]);
        echo '</pre>';
    }

}
