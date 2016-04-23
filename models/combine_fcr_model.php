<?php

class combine_fcr_model extends Model {

    // fetchrecord is pushed the output of fetch() then the controller pulls this information back through public function get();
    private $test = 'working';
    private $fetchrecord = array();
    private $comparerecord = array();
    private $item_nav = null;

    function __construct() {
        parent::__construct();
    }

    /*
     *  Fetches the original record from the default DB: item
     *  If there is a second parameter passed, it can also call a item to compare for display.
     */

    public function fetch($record) {
        // record is returned as an array from the primary controller through the url
        // record[0] is the id
        // record[1] is the table 

        // use a class in the library to run the query
        $dbh = new database();
        $sql = 'SELECT :a,:b,:c,:d,:e,:f from :t1 where id = :id';
        //$param['whitelist'] = array('flagged_desc,short,new_short,description,new_description,name,new_name');
        $param['fields'] = array(':id' => $record[0]);
        $param['table'] = array(':t1' => $record[1]);
        $param['columns'] = array(
            ':a' => 'short',
            ':b' => 'new_short',
            ':c' => 'description',
            ':d' => 'new_description',
            ':e' => 'name',
            ':f' => 'new_name',);
        $query_object = new \libs\nate\CRUD\read_db($dbh, $sql, $param);
        $this->fetchrecord = $query_object->result;
        
        // run through each record passed to a class from the library that
        // compares it and displays differences
        if ($this->fetchrecord[0]['new_name'] != '') {
            $name_dif = new \libs\nate\diff\difference($this->fetchrecord[0]['name'], $this->fetchrecord[0]['new_name']);
            $this->comparerecord['name'] = $name_dif->get();
        } else {
            $this->comparerecord['name'] = '';
        }

        if ($this->fetchrecord[0]['new_description'] != '') {
            $desc_dif = new \libs\nate\diff\difference($this->fetchrecord[0]['description'], $this->fetchrecord[0]['new_description']);
            $this->comparerecord['desc'] = $desc_dif->get();
        } else {
            $this->comparerecord['desc'] = '';
        }
        
        if ($this->fetchrecord[0]['new_short'] != '') {
            $short_dif = new \libs\nate\diff\difference($this->fetchrecord[0]['short'], $this->fetchrecord[0]['new_short']);
            $this->comparerecord['short'] = $short_dif->get();
        } else {
            $this->comparerecord['short'] = '';
        }
        
        // finally, setup nav click-buttons using a library that returns prev/next links
        $andwhereclause = 'AND STATUS = "updated" ';
        $classurl = 'combine_fcr/fetch/';
        $itemnavobj = new \libs\nate\navigation\get_next_prev_ids($record[0], $record[1], $andwhereclause, $classurl, $dbh);
        $this->item_nav = $itemnavobj->get();
    }

    public function get($var) {
        return $this->{$var};
    }

}
