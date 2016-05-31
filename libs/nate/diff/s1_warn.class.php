<?php

namespace libs\nate\diff;

/*
 * Attempts to find camel case instances and replace them with a period and a line break
 * it does this by selecting upto 50 characters before and 50 after the instance if possible count other cammel case instances and periods to see if they qualify
 */

class s1_warn {
	
    function washall($text) {


        // runs though various regex checks to discover issues within.
        
        $orig = $text;
        $shortie = strlen($text);
         
        // designed to look for array(0)character set for array(1)times and replace it with array(2)
         $regex_fastfix = array(
            array(':',3,'<span style="background-color:#ff6600;color:white;padding:2px;display:inline-block;">:</span>')
        );

        
        $regex_01 = array(
            "/(.{10}[a-z])()([A-Z].{10})/" => "&nbspC-C&nbsp",  // Camel Case found
            "/([a-z])()(The |THE |the )/" => "&nbspThe?&nbsp",   // Occures right after a word :  wordThe -- wordthe
        );
        
        $regex_02 = array(
            "/()([A-Z0-9 ]{20,})()/" => "", // over 20 characters of capital letters and spaces together
            "/()(available in|AVAILABLE IN|Available In)()/" => "",   // Available In occures
            "/()([A-Z][a-z]*\?)()/" => "",   // Odd use of capitalized single-word with a ?-mark after it. 
            "/()([ ]\?[ ])()/" => "",   // floating questionmark - seems like a problem
            "/()([Ww]arning|WARNING)()/" => ""   // Some form or Warning occurs 
        );

        $regex_03 = array(
            "/(\(.*\).*)(\(.*\).*)(\(.*\))/" => ""   // A trio of () groups occues within the document
        );
        
        // MUST COME FIRST
        $count = count($regex_fastfix);
        for($i = 0; $i < $count; $i++) {
            if(substr_count($text,$regex_fastfix[$i][0]) > $regex_fastfix[$i][1]){
            $text = str_replace($regex_fastfix[$i][0], $regex_fastfix[$i][2], $text);}
        }
        
        foreach ($regex_01 as $regex => $insert) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($insert) {
                return ($match[1] . '<span style="background-color:#ff6600;color:white;padding:2px;display:inline-block;">'.$insert.'</span>' . $match[3]);
            }, $text
            );
        };

        foreach ($regex_02 as $regex => $insert) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($insert) {
                return ($match[1] . '<span style="background-color:#ff6600;color:white;padding:2px;display:inline-block;">'.$match[2].'</span>' . $match[3]);
            }, $text
            );
        };

        foreach ($regex_03 as $regex => $insert) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($insert) {
                return ('<span style="background-color:#ff6600;color:white;padding:2px;display:inline-block;">' . $match[1] . $match[2]. $match[3] . '</span>' );
            }, $text
            );
        };
        
        if($shortie <= 150 && $shortie > 0 ) {
        $text = '<span id="shortie" style="background-color:#ff6600;color:white;padding:2px;'
                . 'display:inline-block;">CHARACTER COUNT: ' . $shortie 
                . ' - CORRECTION NEEDED</span> &nbsp&nbsp&nbsp&nbsp' . $text;
        }
        
        if($orig != $text){
            return $text;
        } else { 
            return '';
        }
    }
}