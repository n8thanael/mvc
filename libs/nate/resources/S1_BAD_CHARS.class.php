<?php

namespace libs\nate\resources;

/*
 * Groupings of characters that just don't see right
 * These replaces ments SHOULD NOT include "prepping" for upcoming class washes
 */

class S1_BAD_CHARS {

    function wash($text) {


        $regex_fix_1 = array(
        "/&tilde;/" => "", // tilde...kill it
        '/ ¼/' => '-1/4', // fraction fix
        '/¼/' => '-1/4', // fraction fix
        '/ ½/' => '-1/2', // fraction fix
        '/½/' => '-1/2', // fraction fix/ fraction fix
        '/ ¾/' => '-3/4', // fraction fix
        '/¾/' => '-3/4', // fraction fix
        '/&frac34\"/' => '3/4\"', // fraction fix
        '/&nbsp;/' => ' ', // space needs converted
        '/[.]{1}[ ]{0,3}Why\?/' => '.', // stupid ". Why?" apears in paragraphs - poor writing.
        "/&amp;#\d{1,4};/" => "" // trashes any codes that have a broken HTML tag: &#123; became &amp;#123; and needs deleted
        );

// Unique replacement processes.  find and replaces group 2 with nothing.  Each regex must have 3 groups (before)(delete)(after)
        $step1_ARRAY = array(
            "/(<[A-Za-z]{1,3}>)(-)()/" => "", // a dash follows immediately after a tag...why? - fake list?
            "/([a-z]{2})([.])([A-Z])/" => ". ", // two lower case letters followed by a period and upper case....strange sentence ending needs
            "/([a-z]{2})(TM )([a-z]{2})/" => " ", // someone put a capital TM after a word instead of the actual symbol for trade-mark
            "/(|^|^  {0,2}|<.{0,10}>|<.{0,10}> {0,2})([*])()/" => "", // removes * when it comes at the beginning of a paragraph or tag
            "/([A-Z0-9a-z]+? [A-Za-z]+? [A-Za-z]+?)( [;-]|[;-])( [A-Za-z]+? [A-Za-z]+? [A-Za-z]+?)/" => ", ", // finds a group of 3 words a dash or semi-color and another group of 3 words... trying to eliminate sentence fragments
            "/([a-z])(\.\.\.)()/" => ": ", // finds the elipsis and replaces it with a semi-colon which makes sentence endings easier to locate with proper punctuation.
            "/([0-9]*)(\s*)(&quot;)/" => "",
            "/()(\s{1},)(\s{1})/" => ",",
            '/()(<)([^"=>\/]{20})/' => "", // will remove hanging < symbols that aren't part of tags within 20 characters of no ",=,>,/
            "/(\t)(\")()/" => "", // useless quote at the beginning of a line
            "/()(&amp;quot;)()/" => '"', // useless amphersans
            "/()(\")(\r)/" => "", // useless quotes at the end of a line are dropped
            "/()(&lt;.{5,50}&gt;)()/" => "", // get rid of any brokent <tags and stuff inside> text
            "/()(it\?s)()/" => "it's", // finds it?s and replaces it to it's (the ' still needs changed)
            "/([a-z ]{5})(\?)([A-Za-z])/" => "", // finds places where a ? seems like it's not at the end of a sentense and removes it.
            "/([A-Z]{1}[a-z]+?)(\?)(.[A-Za-z].+?|.[a-z].+?)/" => "", // takes out question marks next to Brand? names, where the was once a possible (R) or (C) symbol
            "/([0-9A-Za-z ]{2})(w\/)([0-9A-Za-z ]{2})/" => "with", // removes with abreviations: w/
            "/( [a-z]+? [a-z]+?)([.])([A-Z][a-z]+? )/" => ". ", // finds 2 words and a period that's run up against another word startign with a capital letter
            "/( [A-Za-z]+?)(\?s)( [A-Za-z]+? )/" => "'s", // finds a word end in word?s where the ? should be an ' and finds a following word to ensure it's not the end of a sentence.
        );

        foreach ($regex_fix_1 as $k => $v) {
            $text = preg_replace($k, $v, $text);
        }

        $step2_ARRAY = array(
            "/([A-Z]{1}[a-z]+?)(\?)(.[A-Za-z].+?|.[a-z].+?)/" => "" // 2nd pass requied....takes out question marks next to Brand? names, where the was once a possible (R) or (C) symbol
        );



        // erase anomolies with $step1~ array
        foreach ($step1_ARRAY as $regex => $replace) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($replace) {
                return ($match[1] . ($match[2] = $replace) . $match[3]);
            }, $text
            );
        };

        // erase anomolies with $step2~ array
        foreach ($step1_ARRAY as $regex => $replace) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($replace) {
                return ($match[1] . ($match[2] = $replace) . $match[3]);
            }, $text
            );
        };



        return $text;
    }

}
