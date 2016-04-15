<?php

namespace libs\nate;

/*
 * this class will recieve an array of information and attempt to flag the record if something is found by running regex codes on it.
 * it can be extended by adding more classes to the flagall method
 * 
 * @ param: array[0] = input file name
 * @ param: array[1] = output file name
 */

class prep
{

    function __construct($array)
    {
        echo "<p>Prep result: ";
        $P1_PREP = new resources\P1_PREP($array);
        echo "</p>";
    }

}