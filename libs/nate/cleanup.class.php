<?php

namespace libs\nate;

/*
 * this class will load cleanup regex information ready for sanitizing varrious desciprtions within the models, controllers, or views
 */

class cleanup
{

    function __construct()
    {
        //echo 'cleanup library instantiated';
    }

    public function double($calc)
    {
        return $calc + $calc;
    }

    public function washall($text)
    {
        $S1_BAD_CHARS = new resources\S1_BAD_CHARS();       $A = 1;
        $S1_HTML      = new resources\S1_HTML();            $B = 1;
        $S2_BREAKS    = new resources\S2_BREAKS();          $C = 1;
        $S3_LIST      = new resources\S3_LIST();            $D = 1;
        $S4_FORMAT_P  = new resources\S4_FORMAT_P();        $E = 1;
        $S5_BAD_P     = new resources\S5_BAD_P();           $F = 1;
        $S6_FRAG    = new resources\S6_FRAG();              $G = 1;
        $SX_APPEND    = new resources\SX_APPEND();          $H = 1;

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
}