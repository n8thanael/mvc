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
        $s1_bad_chars = new resources\s1_bad_chars();   $A = 1;
        $s1_html = new resources\s1_html();             $B = 1;
        $s2_breaks = new resources\s2_breaks();         $C = 1;
        $s3_list = new resources\s3_list();             $D = 1;
        $s4_format_p = new resources\s4_format_p();     $E = 1;
        $s5_bad_p = new resources\s5_bad_p();           $F = 1;
        $s6_frag = new resources\s6_frag();             $G = 1;
        $sx_append = new resources\sx_append();         $H = 1;

        if ($A === 1) {
            $text = $s1_bad_chars->wash($text);
        }

        if ($B === 1) {
            $text = $s1_html->wash($text);
        }

        if ($C === 1) {
            $text = $s2_breaks->wash($text);
        }

        if ($D === 1) {
            $text = $s3_list->wash($text);
        }
        if ($E === 1) {
            $text = $s4_format_p->wash($text);
        }

        if ($F === 1) {
            $text = $s5_bad_p->wash($text);
        }

        if ($G === 1) {
            $text = $s6_frag->wash($text);
        }

        if ($H === 1) {
            $text = $sx_append->wash($text);
        }

        return $text;
    }

    //each resource which washes text will generate a report if it changed something.
    public function washall_with_report($text = '') {
        if ($text != '') {
            $textarray = array('text' => $text, 'report' => '');

            //instantiate all requierd resources
            $s1_bad_chars = new resources\s1_bad_chars();
            $s1_html = new resources\s1_html();
            $s2_breaks = new resources\s2_breaks();
            $s3_list = new resources\s3_list();
            $s4_format_p = new resources\s4_format_p();
            $s5_bad_p = new resources\s5_bad_p();
            $s6_frag = new resources\s6_frag();
            $sx_append = new resources\sx_append();


            $check = $textarray['text'];
            $textarray['text'] = $s1_bad_chars->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 's1_bad_chars | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $s1_html->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 's1_html | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $s2_breaks->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 's2_breaks | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $s1_bad_chars->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 's1_bad_chars | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $s3_list->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 's3_list | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $s4_format_p->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 's4_format_p | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $s5_bad_p->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 's5_bad_p | ';
            }

            $check = $textarray['text'];
            $textarray['text'] = $s6_frag->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 's6_frag | ';
            }


            $check = $textarray['text'];
            $textarray['text'] = $sx_append->wash($textarray['text']);
            if ($check != $textarray['text']) {
                $textarray['report'] .= 'sx_append | ';
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
            $s1_bad_chars = new resources\s1_bad_chars();
            $s1_html = new resources\s1_html();
            $s2_breaks = new resources\s2_breaks();
            $s3_list = new resources\s3_list();
            $s4_format_p = new resources\s4_format_p();
            $s5_bad_p = new resources\s5_bad_p();
            $s6_frag = new resources\s6_frag();
            $sx_append = new resources\sx_append();

            if (strpos(strtoupper($toggle_string), 's1_bad_chars') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $s1_bad_chars->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 's1_bad_chars,';
                }
            }

            if (strpos(strtoupper($toggle_string), 's1_html') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $s1_html->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 's1_html,';
                }
            }

            if (strpos(strtoupper($toggle_string), 's2_breaks') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $s2_breaks->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 's2_breaks,';
                }
            }

            if (strpos(strtoupper($toggle_string), 's1_bad_chars') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $s1_bad_chars->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 's1_bad_chars,';
                }
            }

            if (strpos(strtoupper($toggle_string), 's3_list') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $s3_list->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 's3_list,';
                }
            }

            if (strpos(strtoupper($toggle_string), 's4_format_p') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $s4_format_p->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 's4_format_p,';
                }
            }

            if (strpos(strtoupper($toggle_string), 's5_bad_p') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $s5_bad_p->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 's5_bad_p,';
                }
            }

            if (strpos(strtoupper($toggle_string), 's6_frag') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $s6_frag->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 's6_frag,';
                }
            }

            if (strpos(strtoupper($toggle_string), 'sx_append') !== FALSE) {
                $check = $textarray['text'];
                $textarray['text'] = $sx_append->wash($textarray['text']);
                if ($check != $textarray['text']) {
                    $textarray['report'] .= 'sx_append,';
                }
            }

            return $textarray;
        }
    }

}
