<?php

namespace libs\nate\resources;

/*
 * Attempts to find camel case instances and replace them with a period and a line break
 * it does this by selecting upto 50 characters before and 50 after the instance if possible count other cammel case instances and periods to see if they qualify
 */

class S1_CAMELCASE {

    function wash($text) {

// grabs matches of 50 characters twice, once on each side of a camel-case word
        $regex_01 = array("/(.{50}[a-z])()([A-Z].{50})/" => ".  ");
        
        foreach ($regex_01 as $regex => $insert) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($insert) {
                substr_count ($match[1], 
                
                return ($match[1] . $insert . $match[3]);
            }, $text
            );
        };
        
        return $text;
    }
}
