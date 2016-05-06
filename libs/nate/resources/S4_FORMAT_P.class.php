<?php

namespace libs\nate\resources;

/*
 * Run if there are no <ul> r <p> formatting tags (maybe there were removed) ---
 * First, attempt to split any run-on sentances that have no space inbetween:  end.Start = end. Start
 * this function will attempt to group sentences together into paragraphs by sentence length.
 * searches for periods but skips periods in/around numbers
 * clusters groups of sentences into a paragraph if they are less than 300 characters into an array.
 * the array goes through if-statements to group paragraphs into 300-character lengths
 *  - or more so fragments of less than 30 characters aren't left off.
 */

class S4_FORMAT_P
{

    function wash($text)
    {
        if ((strpos($text, '<ul>') === false ) && (strpos($text, '<p>') === false) && (strlen($text) > 500)) {

            // somtimes sentances don't have a period after them for easy recognition...this fixes that.
            $regex_a = array("/([a-z]{2})([.])([A-Za-z]{2})/" => ". ");
            foreach ($regex_a as $regex => $insert) {
                $text = preg_replace_callback(
                    $regex,
                    function($match) use ($insert) {
                    return ($match[1].$insert.$match[3]);
                }, $text
                );

            };


            $total = "";
            $diff = 0;
            $leftover = "";
            $buffer    = "";
            $string    = "";
            $textarray = array();
            $newarray  = array();

            // formual designed to split up sentances within paragraphs
            $regex_b = "/[^.].+?[ A-Z].+?[^0-9][.][^0-9<A-Z]/";
            preg_match_all($regex_b, $text, $textarray, PREG_OFFSET_CAPTURE);

            // for each output a new array is formed of all fragments discovered
            foreach ($textarray[0] as $k => $v)
                array_push($newarray, $v[0]); {
            }

            // for an un-known reason, it will sometimes leave off characters
            foreach ($newarray as $v){
                $total .= $v;
            }

            // check for a difference in character lengths.
            $diff = strlen($text) - strlen($total);

            // assign the variable leftover with the remaining text if any.
            $leftover = substr($text,-$diff);

            // if there is remaining text...stick it into the array to reconstruct the sentence.
            if($diff > 0) {
            array_push($newarray, $leftover);
            }

            foreach ($newarray as $k => $v) {
                if ((strlen($v) > 300)) {
                    $string .= "<p>".$buffer." ".$v." </p>";
                    $buffer = "";
//echo "IF1: " . $string;
                    } elseif ((strlen($buffer) + strlen($v)) < 300) {
                    $buffer .= " ".$v." ";
//echo "ElseIF 2: " . $buffer;
                } elseif ((strlen($buffer) + strlen($v)) > 300 && strlen($v) < 100) {
                    $buffer .= " ".$v." ";
//echo "ElseIF 3: " . $buffer;
                } elseif ((strlen($buffer) + strlen($v)) >= 300) {
                    $string .= "<p>".$buffer." ".$v." </p>";
                    $buffer = "";
//echo "ElseIF 4: " . $buffer;
                } else {
                    $buffer .= " ".$v;
//echo "ElseIF 5: " . $buffer;
                }

            }

            if (strlen($buffer) > 0) {
                $string .= "<p>".$buffer."</p>";
            }
            $text = $string;

        }
        return $text;
    }
}