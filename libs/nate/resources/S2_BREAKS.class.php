<?php

namespace libs\nate\resources;

/*
 * Designed to read groups of information using periods and colons
 * the groups are then separted with a <br> as best as possible
 * will not run if there are at least 4 <li> or </li> tags
 */
class s2_breaks extends sx_listcheck
{
    function wash($text)
    {
        $remix = true;


// how many <LI> & </LI> tags exist at this point?  DON'T RUN - designed TO MAKE these types of lists.
// run function from SX_LISTCHECK parent that prepares following protected properties.
        $this->check($text);
        if ($this->li > 4) {
            $remix = false;
        }


        // if there are miss-matched amounts of tags...it's broke and must $remix

        if ($this->broke) {
            $regex_a = "/<ul>|<li>|<\/ul>|<\/li>/";
            $text    = preg_replace($regex_a, "<br>", $text);
            $remix   = true;
        }
        
        if ($remix) {
            // grab all decimal measurements and replace the period if it exists to keep later sentence-matching by periods from breaking
            $regex_01 = array("/(\s[0-9]{1,3})([.])([0-9]{1,3}[\"' ])/" => "QQQ",
                "/([^a-zA-Z])([.])([0-9]{1,3}[\"' ])/" => "QQQ");

            // matches goups of short sentances that have semi-colons in them: Special Bladelock: Alcatraz - Patented. (break goes in front)
            $regex_02 = array("/([A-Z]{1}[A-Za-z ]*?[a-z0-9]{2,}[ ]{0,1})(:)(.*?[a-z\"'][.])/" => "<br>");

            // matches 3 groups looking for Short Sentances: Clam packed. 3 Pack.
            $regex_03 = array("/([A-Z0-9][ \-_\"\'a-zA-Z0-9]{3,30}[.])(.)([A-Z0-9][ \-_\"\'a-zA-Z0-9]{3,30}[.])/" => "<br>"
                //,"/([a-z]{2})(\.)([A-Z]|[0-9])/" => ".  <br>"
                );

            // aggressive period matching based on capital letters
            $regex_03b = array("/([a-z]{2})(\.)([A-Z]|[0-9])/" => ".  <br>"
             );

            // matches period groups of as few as 7 and as great as 50 characters
            $regex_04 = array("/([.] )([A-Z]|[0-9])(.{7,70}?[.])/" => ".  <br>");

            // returns all instances of 'QQQ' to a period again.
            $regex_05 = array("/()(QQQ)()/" => ".");


            // find patterns of measurements
            // 4.5 lbs. or 10.5" etc.
            // replace the period with 'QQQ'
            // 'QQQ' will be exchanged again to period later
            foreach ($regex_01 as $regex => $insert) {
                $text = preg_replace_callback(
                    $regex,
                    function($match) use ($insert) {
                    return ($match[1].$insert.$match[3]);
                }, $text
                );
            };

/*           // TURNED OFF --- seemed too greedy
             // run regex_02 code - insert <br> in correct spot where : and period at the end appears to be making a list.
            foreach ($regex_02 as $regex => $insert) {
                $text = preg_replace_callback(
                    $regex,
                    function($match) use ($insert) {
                    return ($insert.$match[1].$match[2].$match[3]);
                }, $text
                );
            };

  * 
 */    
            // run regex_03 code - insert <br> in correct spot
            foreach ($regex_03 as $regex => $insert) {
                $text = preg_replace_callback(
                    $regex,
                    function($match) use ($insert) {
                    return ($insert.$match[1].$insert.$match[3].$insert);
                }, $text
                );
            };

       
            // turns "QQQ" strings back into periods
            foreach ($regex_03b as $regex => $insert) {
                $text = preg_replace_callback(
                    $regex,
                    function($match) use ($insert) {
                    return ($match[1].$insert.$match[3]);
                }, $text
                );
            };
            
            // looks for more short-sentance groups and tries to lead them with <br>
            foreach ($regex_04 as $regex => $insert) {
                $text = preg_replace_callback(
                    $regex,
                    function($match) use ($insert) {
                    return ($insert.$match[2].$match[3]);
                }, $text
                );
            };


            // turns "QQQ" strings back into periods
            foreach ($regex_05 as $regex => $insert) {
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
}