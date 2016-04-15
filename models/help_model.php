<?php

class Help_Model extends Model
{

    function __construct()
    {
        parent::__construct();
        echo '<p>Help model</p>';
        echo Session::get('loggedIn');
    }

    public function blah($addup = 0)
    {
        echo "<p>"."helpmodel/blah actually ran..."."</p>";
        return $addup + 10;
    }

    public function other($arg = false)
    {
        echo "<p>"."helpmodel/other actuall fired...."."</p>";
    }
}