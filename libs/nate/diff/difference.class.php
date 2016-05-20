<?php

namespace libs\nate\diff;

/*

 */

class difference {

    private $difference = '';
    private $string_a = '';
    private $string_b = '';
    private $output;
    private $on;

    public function get(){
        return $this->output;
    }

    public function get_diff($string_a,$string_b){
        $this->output = $this->diffline($string_a, $string_b);
        return $this->output;
    }

    private function array_diff($from, $to) {
        $diffValues = array();
        $diffMask = array();

        $dm = array();
        $n1 = count($from);
        $n2 = count($to);

        for ($j = -1; $j < $n2; $j++)
            $dm[-1][$j] = 0;
        for ($i = -1; $i < $n1; $i++)
            $dm[$i][-1] = 0;
        for ($i = 0; $i < $n1; $i++) {
            for ($j = 0; $j < $n2; $j++) {
                if ($from[$i] == $to[$j]) {
                    $ad = $dm[$i - 1][$j - 1];
                    $dm[$i][$j] = $ad + 1;
                } else {
                    $a1 = $dm[$i - 1][$j];
                    $a2 = $dm[$i][$j - 1];
                    $dm[$i][$j] = max($a1, $a2);
                }
            }
        }

        $i = $n1 - 1;
        $j = $n2 - 1;
        while (($i > -1) || ($j > -1)) {
            if ($j > -1) {
                if ($dm[$i][$j - 1] == $dm[$i][$j]) {
                    $diffValues[] = $to[$j];
                    $diffMask[] = 1;
                    $j--;
                    continue;
                }
            }
            if ($i > -1) {
                if ($dm[$i - 1][$j] == $dm[$i][$j]) {
                    $diffValues[] = $from[$i];
                    $diffMask[] = -1;
                    $i--;
                    continue;
                }
            } {
                $diffValues[] = $from[$i];
                $diffMask[] = 0;
                $i--;
                $j--;
            }
        }

        $diffValues = array_reverse($diffValues);
        $diffMask = array_reverse($diffMask);
        
        return array('values' => $diffValues, 'mask' => $diffMask);
    }

    public function diffline($line1, $line2) {
        $diff = $this->array_diff(str_split($line1), str_split($line2));
        $diffval = $diff['values'];
        $diffmask = $diff['mask'];
        
        $subtract = 0;
        $add = 0;

        $n = count($diffval);
        $skip = array(' ',"\r","\n");
        $pmc = 0;
        $result = "<style>#y{background-color:yellow;}#o{background-color:#99ff99;}</style>";
        $previous = null;
        $span = false;
        //$on = true;
        for ($i = 0; $i < $n; $i++) {
            // if($diffval[$i] == "<" ) {$on = false; echo '<b>off:' . $i . '</b>';} else {echo '|' . $i . '|';}
            // if($diffval[$i] == ">") {$on = true; echo '<b>on:' . $i . '</b>';}
            $mc = $diffmask[$i];
            if(!in_array($diffval[$i],$skip)){
                if (($mc != $pmc) && isset($diffmask[$i - 1]) && ($diffmask[$i - 1] != $diffmask[$i])) {
                    switch ($mc) {
                        case -1: $result .= '<span id="y">';
                        $subtract++;
                            break;
                        case 1: $result .= '<span id="o">';
                        $add++;
                            break;
                    }
                    $span = true;
                }
                $result .= $diffval[$i];
                if(isset($diffmask[$i + 1]) && $diffmask[$i + 1] != $diffmask[$i] && $span == true){
                    $result .= '</span>';
                    $span = null;
                }
                if($i == $n && $span == true) {
                    $result .= '</span>';
                    $span = null;
                }
            } else {
                $result .= $diffval[$i];
            }
            
        }
       return $result;
    }

}
