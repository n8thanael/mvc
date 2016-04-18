<?php

namespace libs\nate\CRUD;

    /*
     * updates or inserts data into a database
     * and if desired can call table information to check if something has changed prior to write
     * 
     * @ param: array[0] = input file name
     * @ param: array[1] = output file name
     */
        /* Outside of class:
         * A.)  all values are set as basearray1
         * B.)  inputarray is pre-built to specs
         * c.)
         * 
         * new class = $update_db($basearray,$inputarray,$table,$returnerror,$echoerrors) 
         * So this class:
         *  1.)  recieves a BASE ARRAY of all fields to lookup/update
         *  2.)  looks up those fields in DB
         *  3.)  compares all fields with each other...which are different?
         *          -> None are different, return an error
         *          -> something changed...only update changed
         *  4.)  
         *  2.)  SETS A DATE
         *  3.)  Checks did the input form values change (needs to be "compare from DB" -> return TRUE/FALSE
         *          - instead, we can set a separate parameter to FIRST check if values are different - if they are, then TRUE, not FALSE
         *  4.)  
         */

class update_insert_db
{
    private $error = array(); //return error array if parameters do not match.
    private $debug = false;  // if set to debug - this will perform extensive checks on the data that are not necessary otherwise
    private $errorarray = array();

    function __construct($dbh,$table,$ainput,$upORin = '',$idfield = '',$atemp='',$debug = false)
    {
            echo 'update_insert_db has instatiated.<br>';
            $this->paramcheck($dbh,$table,$ainput,$upORin,$idfield,$atemp,$debug);
    }
    
    private function paramcheck($dbh,$table,$ainput,$upORin,$idfield,$atemp,$debug){
            if(!is_bool($debug)){
                $this->error = [__LINE__ => 'debug option (parameter 7) is improperly set.'];
            } else {
                $this->debug = $debug;
            }
            
            if(!is_object($dbh)){
                $this->error = [__LINE__ => '1st parameter needs to be a PDO instatiated table object.'];
            } else {
                       
            if(!isset($table) || !is_string($table)){
                $this->error = '2nd parameter string is not a valid string.';
                if ($this->debug){
                    $rs = $db->prepare('SELECT * FROM '.$table.' LIMIT 0');
                    for ($i = 0; $i < $rs->columnCount(); $i++) {
                        $col = $rs->getColumnMeta($i);
                        $columns[] = $col['name'];
                    }
                    print_r($columns);
                }
            }
            }
        
            if($upORin !== 'update' || $upORin !== 'input' && !isset($atemp)){
                $this->error = '4th parameter should be "update" or '
                        . '"input". If neither then 5th param should be an array that contains all keys within the table to insert into.  - class must have separate array of ';
                
            }
        
    }
    
    public function geterror(){
        echo '';
        print_r($this->error);
        echo '';
    }
            

}
