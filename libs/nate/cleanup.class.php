<?php

namespace libs\nate;

/*
 * this class will load cleanup regex information ready for sanitizing varrious desciprtions within the models, controllers, or views
 * requires a library of resources within a folder within the same folder to function
 * resrouces simply return a text file that is passed through them
 * METHODS:
 *   washall  -  simply recives string and returns string back once it's passed through all washing classes below
 *   washall_with_report - recives a string and returns an array which has the input string as well as a report of all classes that made a change
 *   washall_with_report_toggle - allows the ability to toggle certain sub-classes on/off
 */


class cleanup {

    public function washall($text) {
        $S1_BAD_CHARS = new resources\S1_BAD_CHARS();   $A = 1;
        $S1_HTML = new resources\S1_HTML();             $B = 1;
        $S2_BREAKS = new resources\S2_BREAKS();         $C = 1;
        $S3_LIST = new resources\S3_LIST();             $D = 1;
        $S4_FORMAT_P = new resources\S4_FORMAT_P();     $E = 1;
        $S5_BAD_P = new resources\S5_BAD_P();           $F = 1;
        $S6_FRAG = new resources\S6_FRAG();             $G = 1;
        $SX_APPEND = new resources\SX_APPEND();         $H = 1;

        if ($A === 1) {
            $text = $S1_BAD_CHARS->wash($text);
        }

        if ($B === 1) {
            $text = $S1_HTML->wash($text);
        }

        if ($C === 1) {
            $text = $S2_BREAKS->wash($text);
        }

        if ($D === 1) {
            $text = $S3_LIST->wash($text);
        }
        if ($E === 1) {
            $text = $S4_FORMAT_P->wash($text);
        }

        if ($F === 1) {
            $text = $S5_BAD_P->wash($text);
        }

        if ($G === 1) {
            $text = $S6_FRAG->wash($text);
        }

        if ($H === 1) {
            $text = $SX_APPEND->wash($text);
        }

        return $text;
    }

    //each resource which washes text will generate a report if it changed something.
    public function washall_with_report($text = '') {
        if ($text != '') {
            $textarray = array('text' => $text, 'report' => '');

            //instantiate all requierd resources
            $S1_BAD_CHARS = new resources\S1_BAD_CHARS();
            $S1_HTML = new resources\S1_HTML();
            $S2_BREAKS = new resources\S2_BREAKS();
            $S3_LIST = new resources\S3_LIST();
            $S4_FORMAT_P = new resources\S4_FORMAT_P();
            $S5_BAD_P = new resources\S5_BAD_P();
            $S6_FRAG = new resources\S6_FRAG();
            $SX_APPEND = new resources\SX_APPEND();


            $check = $textarray['text'];
            $textarray['text'] = $S1_BAD_CHARS->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'S1_BAD_CHARS | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $S1_HTML->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'S1_HTML | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $S2_BREAKS->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'S2_BREAKS | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $S1_BAD_CHARS->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'S1_BAD_CHARS | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $S3_LIST->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'S3_LIST | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $S4_FORMAT_P->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'S4_FORMAT_P | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $S5_BAD_P->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'S5_BAD_P | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $S6_FRAG->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'S6_FRAG | ';
            }


            $check = $textarray['text'];
            $textarray['text'] = $SX_APPEND->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'SX_APPEND | ';
            }

            return $textarray;
        }
    }

    //each resource which washes text will generate a report if it changed something.
    //resources can be toggled on/off from instatiaiton when method is called
    //toggle string must beformatted as comma-delimited list RESOURCE_1,RESOURCE_B,RESOURCE_3 etc.
    public function washall_with_report_toggle($text = '', $toggle_string) {
        if ($text != '') {
            $textarray = array('text' => $text, 'report' => '');

            //instantiate all requierd resources
            $S1_BAD_CHARS = new resources\S1_BAD_CHARS();
            $S1_HTML = new resources\S1_HTML();
            $S2_BREAKS = new resources\S2_BREAKS();
            $S3_LIST = new resources\S3_LIST();
            $S4_FORMAT_P = new resources\S4_FORMAT_P();
            $S5_BAD_P = new resources\S5_BAD_P();
            $S6_FRAG = new resources\S6_FRAG();
            $SX_APPEND = new resources\SX_APPEND();

            if (strpos(strtoupper($toggle_string), 'S1_BAD_CHARS') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $S1_BAD_CHARS->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'S1_BAD_CHARS,';
                }
            }

            if (strpos(strtoupper($toggle_string), 'S1_HTML') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $S1_HTML->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'S1_HTML,';
                }
            }

            if (strpos(strtoupper($toggle_string), 'S2_BREAKS') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $S2_BREAKS->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'S2_BREAKS,';
                }
            }

            if (strpos(strtoupper($toggle_string), 'S1_BAD_CHARS') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $S1_BAD_CHARS->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'S1_BAD_CHARS,';
                }
            }

            if (strpos(strtoupper($toggle_string), 'S3_LIST') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $S3_LIST->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'S3_LIST,';
                }
            }

            if (strpos(strtoupper($toggle_string), 'S4_FORMAT_P') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $S4_FORMAT_P->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'S4_FORMAT_P,';
                }
            }

            if (strpos(strtoupper($toggle_string), 'S5_BAD_P') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $S5_BAD_P->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'S5_BAD_P,';
                }
            }

            if (strpos(strtoupper($toggle_string), 'S6_FRAG') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $S6_FRAG->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'S6_FRAG,';
                }
            }

            if (strpos(strtoupper($toggle_string), 'SX_APPEND') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $SX_APPEND->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'SX_APPEND,';
                }
            }

            return $textarray;
        }
    }

}
