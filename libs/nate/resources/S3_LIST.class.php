<?php

namespace libs\nate\resources;

/*
 * count to see if there is already a heathy <ul><li> structure...if so, skip
 * reads line break segments <br>
 * calculates length of lines over 60 and then creates a list of the lines if there are 3 or more.
 * encloses all descriptions within a <p> tag
 */

class S3_LIST extends SX_LISTCHECK {

    function wash($text) {
        $remix = false;

        // run function from SX_LISTCHECK parent that prepares following protected properties.
        $this->check($text);

        // if there are few or low numbers of <li></li> and a relative nmber of '<br>' - make some lists.
        if ($this->linum <= 4 && $this->brnum >= 3) {
            $remix = true;
        } else {
            
        }

        if (($this->ul == false) || ($this->li == false)) {
            // there is something wrong with the UL / LI structure...trash them all and start over.
            $regex_li_a = array(
                "/<\/li>|<\/ul>/" => "<br>",
                "/<li>|<ul>/" => ""
            );

            foreach ($regex_li_a as $k => $v) {
                $text = preg_replace($k, $v, $text);
            }

            $remix = true;
        }

        // preserve the current UL - it seems it's well constructed
        if (($this->ul == true) && ($this->li == true) && ($this->linum >= 8)) {
            $remix = false;
        }

        if ($remix) {
            $array = explode('<br>', $text);

            // if the array has more than one line, lines are sorted by length between array_a & array_b then put back into sentence array longest to shortest
            $sentencearray = array();
            if (count($array) > 1) {
                $array_a = array();
                $array_b = array();
                foreach ($array as $sentence) {
                    array_push($sentencearray, trim($sentence));
                    $sentencearray = array_filter($sentencearray);
                }

                foreach ($sentencearray as $key => $value) {
                    $array_a[$key] = strlen($value);
                };

                // run a parent function to get the average length of the lines.
                if ($this->averagelinecheck($array_a) < 100) {
                    arsort($array_a);
                }

                foreach ($array_a as $key => $value) {
                    array_push($array_b, $sentencearray[$key]);
                }
                $sentencearray = $array_b;
            } else {

                $sentencearray[0] = trim($text);
            }

            // Eliminate duplicate entries
            $sentencearray = array_unique($sentencearray);

            $count = count($sentencearray);
            $subcount = 0;
            $subline = "";
            if (count($sentencearray) > 3) {
                foreach ($sentencearray as $key => $value) {
                    if (strlen($value) >= 60) {
                        $subcount++;
                        $subline .= $value . " ";
                    }
                }
                if (($count - $subcount) > 3) {
                    $subline .= '<ul>';
                    foreach ($sentencearray as $key => $value) {
                        if (strlen($value) < 60) {
                            $subline .= "<li>" . $value . "  </li>";
                        }
                    }
                    $subline .= '</ul>';
                } else {
                    foreach ($sentencearray as $key => $value) {
                        if (strlen($value) < 60) {
                            $subline .= $value . "  <br>";
                        }
                    }
                }
            } else {
                foreach ($sentencearray as $key => $value) {
                    $subline .= $value . "  <br>";
                }
            }
            $text = $subline;
        } else {
            
            // IF we're not goign to remix this stuff...we're going to pull all inserted BR tags to make the paragraphs look normalized.
            $regex_fix_1 = array(
                "/<br>/" => "" // wipe out any <br> that exist within paragraph tags
            );
            foreach ($regex_fix_1 as $k => $v) {
                $sentencearray[0] = preg_replace($k, $v, $text);
            }
        }

        $regex_01 = array(
            "/([^0-9])(&#34;|&amp#34;|&quot;|\")([ ]+?<\/[A-Za-z]{1,3}>|<\/[A-Za-z]{1,3}>)/" => "", // whipe-out any tags that end in ", but not ones that donnate a measurement of feet 1"
            "/(^.+?)(<\/[A-Za-z]{1,3}>)()/" => "", // Take out the first leading end-tag if it's missing a match
            "/(<li>.+?)(<\/p><p>|\/p>\s+?<p>)(.+?<\/li>)/" => "</li><li>" // Find and replace hanging <p> </p> miss-matched paragraph tags found within line items.
        );

        // takes out hanging " at the end of tag-lists
        foreach ($regex_01 as $regex => $insert) {
            $text = preg_replace_callback(
                    $regex, function($match) use ($insert) {
                return ($match[1] . $insert . $match[3]);
            }, $text
            );
        };


        $regex_li_b = array(
            "/^<br>|<br\/>|<br \/>/" => ""
        );

        foreach ($regex_li_b as $k => $v) {
            $text = preg_replace($k, $v, $text);
        };

        return $text;
    }

}
