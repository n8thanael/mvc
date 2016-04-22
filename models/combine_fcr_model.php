<?php


class combine_fcr_model extends Model {

    // fetchrecord is pushed the output of fetch() then the controller pulls this information back through public function get();
    private $test = 'working';

    function __construct() {
        parent::__construct();
        echo 'why not?  ';
    }

    public function get($var) {
        return $this->{$var};
    }

}
