<?php

namespace libs\nate\resources;

/* REMOVES LINE BREAKS that break up descriptions.
 * 
 * setup an array of line breaks to remove if the line DOES NOT contain a long regexSKU
 * takes in a file to read it.
 * opens another file to output it.
 * starts a counter....so the first line doesn't get a line break before it for no reason (first line SHOULD be a SKU)
 * while the file has length left to scan (!feof())
 * fgets each line by the line breaks
 * removes all line breaks within that line
 * if it finds a SKU it re-inserts a line break PRIOR to the SKU (the line before)
 * it then will ignore any line wihout a SKU, having cleaned them of any issues.
 */

class P1_PREP {
    function __construct($names) {

        $input = dirname(__FILE__).'\\files\\' . $names[0] ;
        $output =  dirname(__FILE__).'\\files\\' . $names[1] ;


        $linebreaks = array("\r\n", "\n", "\r");
        $regexSKU = '/^[0-9-\t]{5,18}/';

        if (file_exists($input)) {
            $file = fopen($input, "r");
            $out = fopen($output, 'wb+');
            $i = 0;
            while (!feof($file)) {
                $line = fgets($file);
                $line = str_replace($linebreaks, "", $line);

                if (preg_match($regexSKU, $line) && ($i != 0)) {
                    fwrite($out, "\r\n" . $line);
                } else {
                    fwrite($out, $line);
                }
                $i++;
            }
            fclose($file);
            fclose($out);
            ECHO 'file ready;';
        } else {
            ECHO 'FILE ERROR<br>';
        }
    }

}
