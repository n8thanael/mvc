<?php

namespace libs\nate\resources;

/*
 * trims string
 * counts the number of <p> and </p>
 * if there is only two and they occure at JUST the start and END...delete them both.
 */

class s5_bad_p
{

    function wash($text)
    {

// counts <p> and </p> within a string

        $text = trim($text);

        if ((substr_count($text, '<p>') === 1) && (substr_count($text, '</p>') === 1)) {
            $regex_01 = array("/^(<p>)(.*)(<\/p>)$/" => "");

            foreach ($regex_01 as $regex => $insert) {
                $text = preg_replace_callback(
                    $regex,
                    function($match) use ($insert) {
                    return ($match[2]);
                }, $text
                );
            };
        }

        // cleans up formatting issues within Paragraphs of having to many line breaks or too much white space after a tag.
        $regex_02 = array("/(<p>)([ ]*)()/" => "",
            "/(<p>)(<br>)()/" => "",
            "/(<li>)(<br>)()/" => ""
        );

        foreach ($regex_02 as $regex => $insert) {
            $text = preg_replace_callback(
                $regex,
                function($match) use ($insert) {
                return ($match[1].$insert.$match[3]);
            }, $text
            );
        };
        return $text;
    }
}