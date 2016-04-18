<?php
namespace libs\nate\CRUD;


    /*
     * selects information from DB and returns either a single entry, an array or the actual db query result
     * can be set to 'debug' mode to show errorInfo();
     * 
     * @ param: array[0] = input file name
     * @ param: array[1] = output file name
     */

class read_db
{
    private $dbh;
    function __construct($dbh)
    {
        echo '<br/>insde read_db:' ; var_dump($dbh);
        
        // the OBJECT PDO changes it's methods based on if you've prepared a statement.
        echo '<pre>';
        var_dump(get_class_methods($dbh));
        echo '</pre>';
        
        
        echo '<pre>';
        // $this->dbh = $dbh->prepare('Select * from users;');
        echo '</pre>';

        echo '<pre>';
        $dbh = $dbh->prepare('Select * from users;');
        echo '</pre>';


        echo '<pre>';
        var_dump(get_class_methods($dbh));
        echo '</pre>';
        $dbh->execute();
        $result = $dbh->fetchAll();
        var_dump($result);
    }
}



