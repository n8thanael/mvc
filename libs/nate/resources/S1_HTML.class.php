<?php

namespace libs\nate\resources;

/*
 * strip any HTML that is NOT in the regexARRAY
 * allowed : <p> <ul> <li> <br>
 * replace anything else found in HTML into a <p>
 */

class s1_html{

    function wash($text) {

// finds empty tags that may or may not have spaces or &nbsp; within the tags
        $step0_ARRAY = array("/(<[A-Za-z]{1,3}>)(&nbsp; +?|&nbsp;| +?|)(<\/[A-Za-z]{1,3}>)/" => "");


// Identify all tags that are to be kept, and change to a unique string to prevent them from changing when strip_tags($line) runs
        $step1_ARRAY = array(
            "/()(<p.*?>)()/" => "!!!p!!!",        // - used to work as <p>...exchangd for simple <br>
            "/()(<\/p>)()/" => "!!!???p!!!",         // - used to work as </p>...exchangd for simple <br>
            "/()(<ul.*?>)()/" => "!!!ul!!!",
            "/()(<\/ul>)()/" => "!!!???ul!!!",
            "/()(<li.*?>)()/" => "!!!li!!!",
            "/()(<\/li>)()/" => "!!!???li!!!",
            "/()(<br.*?>|<BR.*?>)()/" => "!!!br!!!", 
            "/()(<[^\/]+?>)()/" => "!!!br!!!",
            "/()(<[\/.]+?>)()/" => "!!!br!!!", 
            "/()(\/)()/" => "!!!47!!!",
            "/()(&)()/" => "!!!38!!!",
            "/()([+])()/" => "!!!43!!!"
        );


// all tags that were kept are now returned to actual tags.
        $step2_ARRAY = array(
            "/()(!!!p!!!)()/" => "<p>",             // - used to work as <p>...exchanged for simple <br>
            "/()(!!!\?\?\?p!!!)()/" => "</p>",      // - used to work as </p>...exchanged for simple <br>
            "/()(!!!ul!!!)()/" => "<ul>",
            '/()(!!!\?\?\?ul!!!)()/' => "</ul>",
            "/()(!!!li!!!)()/" => "<li>",
            "/()(!!!\?\?\?li!!!)()/" => "</li>",
            "/()(!!!br!!!)()/" => "<br>",
            "/()(!!!47!!!)()/" => "/",
            "/()(!!!38!!!)()/" => "&",
            "/()(!!!43!!!)()/" => "&#43;"
        );


        $REGEX_match_bad_characters = '/[^\s\da-zA-Z!?#$%&^*()_`".,;:\'\-]*/';

        // Wipes out empty tags
        foreach ($step0_ARRAY as $regex => $insert) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($insert) {
                return ($insert);
            }, $text
            );
        };


        // erase anomolies with $step1~ array
        foreach ($step1_ARRAY as $regex => $replace) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($replace) {
                return ($match[1] . ($match[2] = $replace) . $match[3]);
            }, $text
            );
        };

                

        // $text undergoes HTML related washing
        $text = strip_tags($text);
        $text = htmlspecialchars_decode($text);
        $text = htmlspecialchars($text);

        // remove any bad characters using REGEX
        $text = preg_replace($REGEX_match_bad_characters, "", $text);


        // restore anomolies with $step3~ array
        foreach ($step2_ARRAY as $regex => $replace) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($replace) {
                return ($match[1] . $replace . $match[3]);
            }, $text
            );
        };

        $text = preg_replace("/(?:<br>)+/", "<br>", $text);
 
        return $text;
    }

}
