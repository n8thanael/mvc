<?php

namespace libs\nate;

/*
 * this class will recieve an array of information and attempt to flag the record if something is found by running regex codes on it.
 * it can be extended by adding more classes to the flagall method
 */

class check
{

    function __construct()
    {
        //echo 'check library instantiated';
    }

    public function flagall($array)
    {
        $F1_BOX = new resources\F1_BOX();       $A = 1;

        if ($A === 1) {
            $array = $F1_BOX->flag($array);
        }
        
        return $array;
    }
}