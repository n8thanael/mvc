<?php

namespace libs\nate\CRUD;

/*
 * recives 2 queries and/or 2 arrays
 * methods within compare/contrast various parts of them
 */

class compare_db extends read_db {
    
    protected $sql_result_a = array();
    protected $sql_result_b = array();
    protected $array_a = array();
    protected $array_b = array();
    
    // sets up in-class parameters and routes to correct methods
    function __construct($dbh, $sqla = false, $parama = false, $sqlb = false, $paramb = false, $arraya = false, $arrayb = false,  $debug = false) {
        if(is_object($dbh)){
        if($sqla !== FALSE && $parama != FALSE && is_array($sqla) && is_array($parama)){
            $this->sql_result_a = new read_db($dbh,$sqla,$parama,$debug);            
        }
        if($sqlb !== FALSE && $paramb != FALSE && is_array($sqlb) && is_array($paramb)){
            $this->sql_result_b = new read_db($dbh,$sqlb,$paramb,$debug);            
        }
        
        if($arraya !== FALSE && is_array($arraya)){
            $this->array_a = $arraya;
        }

        if($arrayb !== FALSE && is_array($arrayb)){
            $this->array_b = $arrayb;
        }
        
        }
    }
    
    public function query_a_b_match(){
        if($this->array_a = $this->array_b){
        return true;      
        } else {
        return false;
        }
    }
    
    public function query_a_matches_array_a_match(){
        if($this->array_a = $this->array_b){
        return true;      
        } else {
        return false;
        }    
    }
}
