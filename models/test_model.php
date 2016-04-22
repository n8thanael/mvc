<?php

class test_Model extends model {

    function __construct() {
        parent::__construct();
        $dbh = new database();
        echo 'test model is working <br>';
        //$a = new \libs\nate\CRUD\update_insert_db('howdy','','','','','',true);
        
        /*
        $param['table'] = array(':table' => 'users');
        $param['columns']= array(':cola' => 'password', ':colb' => 'login');
        $param['fields']= array(
            array(':password' => 'ee11cbb19052e40b07aac0ca060c23ee', ':login' => rand())
            );
        $param['batch'] = 5;
        $param['display'] = 'jokeface';
        $param['display_multiple_of'] = 'B';
        $sql = 'insert into :table(:cola,:colb) values($array);';
         * 
         */
        $sql = 'SELECT * from :t1 where id > 0;';
        $param['whitelist'] = array('users');
        $param['table']= array(
            ':t1' => 'users'
        );
        /*
         * $param['table']= array(
            ':table1' => 'users'
            );
         * 
         */
        

         
        // example A simple query works...adding TRUE after $sql will return
        /*
        $a = new \libs\nate\CRUD\read_db($dbh,$sql,$param,TRUE);

        echo '<pre>';
        print_r($a->result[0]);
        echo '</pre>';
        
         * 
         */

        $b = new \libs\nate\CRUD\compare_db($dbh,$sql,$param,true);
        echo '<pre>';
        // print_r($b->result);
        echo '</pre>';
        
        $number = 50;
        echo $number . '<br>';
        $divide = 3;
        echo $divide . '<br>';
        echo ($number/$divide) . '<br>';
        $intval = intval(($number/$divide));
        echo 'intval: '. $intval . '<br>';
        $remainder = $number - ($divide * $intval);
        echo 'remainder: ' .$remainder. '<br>';
        echo 'total intval: ' .$divide * $intval. '<br>';
        echo 'return: '. (($divide * $intval) + $remainder) . '<br>';
        
        echo '<br>';
        echo 50 % 3;
        echo '<br>';
        echo (50/99999);
        echo '<br>';
        echo intval(50/100);
       
        

    }
}
