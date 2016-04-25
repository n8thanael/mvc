<?php

namespace libs\nate\navigation;

/* 
 * designed to recieve query strings from this MVC and return a query-useful array or string
 * an even number of attributes sent in pairs within the URL is recomended:
 * key1/value1/key2/value2/key3/value/3
 * if not, an array can be set with uprep_url_array and until even values result
 */

class translate_url_string {
    
    private $urlstring;  // simple html string
    private $urlarray;  // simple html array
    private $whereurlstring;  // simple html string returned
    // as a " WHERE 'column' = 'value' " string for mysql
        
    
    public function prep_url_array($array,$unset = 0){
        for($i = 0; $i < $unset; $i++){
            unset($array[$i]);
        }
        return implode($array, '/');
    }   

    public function prep_url_string($string,$unset = 0){
        $array = explode('/',$string);
        for($i = 0; $i < $unset; $i++){
            unset($array[$i]);
        }
        return implode($array, '/');
    }   
  
    public function urlstring($string = '') {
        $a = explode('/', strtolower($string));
        $string = '';
        foreach($a as $k => $v){
            if($k % 2 == 0){
            $string .= $v;
            } else {
            $string .= ": " . $v . ", ";
            }
        }
        return $string;
    }
    

    public function urlwherestring($string = '') {
        $a = explode('/', strtolower($string));
        $string = '';
        $count = count($a);
        if ($count > 1) {
        $string = ' AND ';
        foreach($a as $k => $v){
            if($k % 2 == 0){
            $string .= $v;
            } else {
            $string .= " = '" . $v . "' ";
            }
            if((($k +1) >= 2) && (($k + 1) % 2 == 0) && (($k + 1) !== $count)){
            $string  .= ' AND ';
            }
        }
        }
        return $string;
    }
    
    public function urlarray($string = '') {
        $a = explode('/', strtolower($string));
        $array = array();
        $key = '';
        foreach($a as $k => $v){
            if($k % 2 == 0){
            $key = $v;
            } else {
            $array[$key] = $v;
            }
        }
        return $array;
    }
}