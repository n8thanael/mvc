<?php

class Model
{

    function __construct()
    {
       $this->db = new database();
       $db = $this->db;
    }
}