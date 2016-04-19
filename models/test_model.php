<?php

class test_Model extends model {

    function __construct() {
        parent::__construct();
        $dbh = new database();
        echo 'test model is working <br>';
        //$a = new \libs\nate\CRUD\update_insert_db('howdy','','','','','',true);
        
        
        $param['table'] = array(':table' => 'users');
        $param['columns']= array(':cola' => 'id', ':colb' => 'login');
        $param['fields']= array(
            array(':id' => 3, ':login' => 'jimmy'),
            array(':id' => 4, ':login' => 'dan'),
            array(':id' => 5, ':login' => 'sarah'),
            array(':id' => 6, ':login' => 'jeff'),
            array(':id' => 7, ':login' => 'charlie horse'));
        $param['batch'] = 10;
        $param['display'] = true;
        $sql = 'insert into :table(:cola,:colb) values($array) ON DUPLICATE KEY UPDATE id=VALUES(:cola),:colb = VALUES(:colb);';
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

        $b = new \libs\nate\CRUD\insert_update_db($dbh,$sql,$param,true);
        echo '<pre>';
        print_r($b->result);
        echo '</pre>';

    }
}
