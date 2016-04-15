<?php

class Dashboard_Model extends Model
{

    function __construct()
    {
        parent::__construct();
        echo '<p>dashboard model</p>';
    }

    public function logout()
    {
        Session::destroy();
        header('location: ' .URL. 'login');
        exit;
    }
}