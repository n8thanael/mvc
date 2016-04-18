<?php

class test_Model extends model {

    function __construct() {
        parent::__construct();
        $dbh = new database();
        echo 'test model is working <br>';
        //$a = new \libs\nate\CRUD\update_insert_db('howdy','','','','','',true);
        
        $param['table'] = array(':table' => 'users');
        $param['columns']= array(':cola' => 'login', ':colb' => 'password');
        $param['fields']= array(':a' => 'another_test', ':b' => 'ee11cbb19052e40b07aac0ca060c23ee');
        $sql = 'insert into :table(:cola,:colb) values(:a,:b);';
        //$sql = 'SELECT * from users limit 1;';
        //$param['whitelist'] = array('users','login','id');

         
        // example A simple query works...adding TRUE after $sql will return
        /*
        $a = new \libs\nate\CRUD\read_db($dbh,$sql,$param,TRUE);

        echo '<pre>';
        print_r($a->result[0]);
        echo '</pre>';
        
         * 
         */

        $b = new \libs\nate\CRUD\compare_update_db($dbh,$sql,$param,true);
        echo '<pre>';
        print_r($b->result);
        echo '</pre>';

    }
}
