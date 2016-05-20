<?php

class Model
{

    function __construct()
    {
       $this->db = new database();
       $db = $this->db;
       
        function pr($foo = null) { echo "<pre>"; print_r($foo); echo "</pre>"; }
        function vd($foo = null) { echo "<pre>"; var_dump($foo); echo "</pre>"; }
    }
}