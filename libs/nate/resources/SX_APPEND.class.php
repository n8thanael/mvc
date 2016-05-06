<?php

namespace libs\nate\resources;

/*
 * makes one-off adjustements to code that only works after all the other classes have run
 * contains some crappy-quick-fixing
 */

class SX_APPEND
{

    function wash($text)
    {


// Primary replacement process.  Faster than finding 3 groups.
        $step1_ARRAY = array(
            "/&lt;/" => "&#60;",
            "/&gt;/" => "&#62;",
            "/&amp;/" => "&#38;",
            "/&apos;/" => "&#39;",
            "/&quot;/" => "&#34;",
            "/'/" => "&#39;",
            '/\"/' => "&#34;",
            "/<li>(f|F)eatures(|.){0,3}<\/li>/" => "",
            "/<br>(f|F)eatures(|.){0,3}<br>/" => "",
            "/<li>(f|F)eatures(|.){0,3}<li>/" => "<li>",
            "/<li>(s|S)pecifications(|.){0,3}<\/li>/" => "",
            "/<br>(s|S)pecifications(|.){0,3}<br>/" => "",
            "/<li>(s|S)pecifications(|.){0,3}<li>/" => "<li>",
            "/<li>(t|T)echnical (i|I)nformation(|.){0,3}<\/li>/" => "",
            "/<br>(t|T)echnical (i|I)nformation(|.){0,3}<br>/" => "",
            "/<li>(t|T)echnical (i|I)nformation(|.){0,3}<li>/" => "<li>",
        );

        foreach ($step1_ARRAY as $k => $v) {
            $text = preg_replace($k, $v, $text);
        }

// Unique replacement processes.  find and replaces group 2 with nothing.  Each regex must have 3 groups (before)(delete)(after)
        $step2_ARRAY = array(
            "/([^<])(\/)()/" => "&#47;"
        );

        // erase anomolies with $step1~ array
        foreach ($step1_ARRAY as $regex => $replace) {
            $text = preg_replace_callback(
                $regex,
                function($match) use ($replace) {
                return ($match[1].($match[2] = $replace).$match[3]);
            }, $text
            );
        };

        return $text;
    }
}