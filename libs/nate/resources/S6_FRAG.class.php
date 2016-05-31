<?php

namespace libs\nate\resources;

/*
 * Looks for fragments of sentances and tries to create a sentence between them
 * Most often occures with - or ; separating them.
 */

class s6_frag
{

    function wash($text)
    {

        $step1_ARRAY = array(        
               "/([A-Z0-9a-z]+? [A-Za-z]+? [A-Za-z]+?)( [;-]|[;-])( [A-Za-z]+?)( [A-Za-z]+? [A-Za-z]+?)/" => ". " // finds a group of 3 words a dash or semi-color and another group of 3 words... trying to eliminate sentence fragments
    );
                
    
        foreach ($step1_ARRAY as $regex => $replace) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($replace) {
                return ($match[1] . ($match[2] = $replace) . ucwords($match[3]) . $match[4]);
            }, $text
            );
        };
        return $text;
    }
}