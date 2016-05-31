<?php

namespace libs\nate\resources;

/*
 * receives an array of text called $input and uses a ()()() 3- grouped regex function to find text withing
 * Once it finds text, it will highlight it with a style before and after <span></span>
 * Outputs a new array similar to the first with highlighted text if there are changes.
 * If there are no changes it returns FALSE
 */
class f1_box{

    // We need to set some variables that are static for this opperation
    private $flag = false;
    private $output = array();

    function flag($input) {
        // Include "# pks", "# pk", "#/box" and "# per each" to flag-list
        // 
        // set up an array of things for the function to search for in the array
        $regex_find_1 = ["/(PER |[pP]er |[0-9] |[0-9])([Bb]ox|OX)(s|es|S|ES|)/" => "", // per|0-9 box etc.
            "/([Bb]ox|OX)(s|es|S|ES|)( [pP]er| PER)/" => "",                // box per etc.
            "/(PER |[pP]er |[0-9] |[0-9])([Cc]ase|ASE)(s|S|)/" => "",       // case etc.
            "/([Cc]ase|ASE)(s|S|)( [pP]er| PER)/" => "",                    // case etc.
            "/(PER |[pP]er |[0-9] |[0-9])([Pp]ack|ACK)(s|S|)/" => "",       // pack etc.
            "/([Pp]ack|ACK)(s|S|)( [pP]er| PER)/" => "",                    // pack etc.
            "/(PER |[pP]er |[0-9] |[0-9])([Bb]ag|AG)(s|S|)/" => "",         // bag etc.
            "/([Bb]ag|AG)(s|S|)( [pP]er| PER)/" => "",                      // bag etc.
            "/(PER |[pP]er |[0-9] |[0-9])(EACH|Each|each)()/" => "",        // each etc.
            "/(EACH|Each|each)([ ]*?|)([pP]er|PER)/" => "",                 // each etc.
            "/(PER |[pP]er |[0-9] |[0-9])([Bb]undle|UNDLE)(s|S|)/" => "",   // bundles
            "/([Bb]undle|UNDLE)(s|S|)( [pP]er| PER)/" => "",                // bundles
            "/(PER |[pP]er |[0-9] |[0-9])([Kk]it|IT)(s|S|)/" => "",         // kits
            "/([Kk]it|IT)(s|S|)( [pP]er| PER)/" => "",                      // kits
            "/([0-9])([ ]*?|\/|[ ]*\/?[ ]*?)(PKG|Pkg|pkg|pk|PK|BOX|Box|box|BX|Bx|bx|BAG|Bag|bag|BG|Bg|bg|CASE|Case|case|CS|Cs|cs|KIT|Kit|kit|KT|Kt|kt|BUNDLE|Bundle|bundle|BNDL|Bndl|bndl|BDL|Bdl|bdl|EACH|Each|each)/" => "", //   # / Abreviation            
            "/( [Ii])(clude|CLUDE)(s|S|)/" => "", // include, Includes, INCLUDES etc.
            "/( [Cc])(ontains|ONTAINS)()/" => "", // contain, Contains, CONTAINS etc,
            ];

        // for each item in the array to search, pass through each regex function, if it replaces something, then over-write the output and set the flag as true;
        foreach ($input as $textkey => $text) {
            $this->flag = false;
            foreach ($regex_find_1 as $regex => $replace) {
                $remix = preg_replace_callback(
                        $regex, function($match) use ($replace) {
                $this->flag = true;    
                    return ('<span style="color:red;background-color:black;display:inline;">' . $match[1] . $match[2] . $match[3] . '</span>');
                }, $text
                );
                if ($this->flag) {
                    $this->output[$textkey] = $remix;
                    $text = $remix;
                }
            }
        }

        if (count($this->output) > 0) {
            return $this->output;
        } else {
            return false;
        }
    }

}
