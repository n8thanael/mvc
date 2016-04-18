<?php

namespace libs\nate\CRUD;

use \PDO;

    /*
     * selects information from DB and returns either a single entry, an array or the actual db query result
     * can be set to 'debug' mode to show errorInfo();
     * 
     * @ param: array[0] = input file name
     * @ param: array[1] = output file name
     */

class read_db extends \database
{
    protected $error = array(); //return error array if parameters do not match.
    protected $debug = false;   // if set to debug - this will perform extensive checks on the data that are not necessary otherwise
    protected $param = array();
    protected $fields = array();
    protected $query = '';
    protected $table = array();
    protected $errormsg = false;
    private $dbh;

    /*
    function __construct($dbh){
        echo '<pre>';
          var_dump($dbh);
        echo '</pre>';
      //          ReflectionObject::export($dbh, __NAMESPACE__ . '\\read_db');
        $this->dbh = $dbh;
        echo '<pre>';
          var_dump($this->dbh);
        echo '</pre>';
               
        $this->dbh->prepare('Select * from users;');
        $this->dbh->setFetchMode(PDO::FETCH_ASSOC, __NAMESPACE__ . '\\read_db');
        $this->dbh->execute();
        $result = $test->fetchAll();
        var_dump($result);
        
    }
     * 
     */
    
    public function __construct($dbh,$param,$debug = false)
    {
            parent::__construct();
            var_dump($this->db);
            $this->dbh = $dbh;
            $this->param_check($param);
            if($debug) {echo '$dbh: '; var_dump($dbh); echo '<br>';
                        echo '<pre>'; print_r($param); echo '</pre>';
                        echo '<p style="color:white;background-color:red;">Error: '.$this->errormsg.'</p>';
            };
            $test = $this->dbh->prepare('Select :a,:b from users;');
            $test->setFetchMode(PDO::FETCH_ASSOC);
            $test->execute($this->fields);
            $result = $test->fetchAll();
            echo '<pre>';
            var_dump($result);
            echo '</pre>';
            /*
            if($debug) {
                var_dump($result);
                echo '<pre>'; var_dump($test->errorInfo); echo '</pre>';
            }
            
            if(isset($param['singlerow'])){
                $result = $result[0];
            }
            
            if($this->errormsg != false){
                return $result;
            }
             * */
    }
    
    private function param_check($param){
    if(!isset($param['query']) || !is_string($param['query']) || !isset($param['fields']) || !is_array($param['fields'])){
        $this->errormsg .= 'no query or fields in parameter array<br>';
        } else {
            $this->query = $param['query'];          
            $this->fields = $param['fields'];
        }
        if(isset($param['table']) && is_array($param['table']) )
        {
            $this->table = $param['table'];
        } else {
        $this->errormsg .= 'no table set in parameter array<br>';
        }
        
        if(!$this->errormsg){
            foreach($this->table as $k => $v){
               $this->query = str_replace($k,$v,$this->query); 
            }
        }
        
    }
}
