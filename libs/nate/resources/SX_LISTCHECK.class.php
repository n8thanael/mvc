<?php

namespace libs\nate\resources;

/*
 * counts <BR>, <LI>, <UL>
 * designed to be a parent-class
 */

class SX_LISTCHECK
{
    protected $ul = false;
    protected $li = false;
    protected $averageline = 0;
    protected $brnum = 0;
    protected $broke = false;
    protected $linum = 0;
    protected $returncalculate = 0;

    protected function calculate($num){
        $this->returncalculate = 5 * $num;
    }
    
    protected function check($text)
    {
        //Count <br>
        $this->brnum = substr_count($text, '<br>');

        //count UL & LI's
        $array = array();
        array_push($array, "UL: " . substr_count($text, '<ul>'));
        array_push($array, "/UL: " . substr_count($text, '</ul>'));
        array_push($array, "li: " . substr_count($text, '<li>'));
        array_push($array, "/li: " . substr_count($text, '</li>'));

        if (substr_count($text, '<ul>') === substr_count($text, '</ul>')) {
            $this->ul = true;
        }

        if (substr_count($text, '<li>') == (substr_count($text, '</li>'))) {
            $this->li    = true;
            $this->linum = substr_count($text, '<li>') + substr_count($text, '</li>');
            }

        if ((substr_count($text, '<li>') !== substr_count($text, '</li>')) || (substr_count($text, '<ul>') !== substr_count($text, '</ul>'))){
            $this->broke = true;
        }
    }

    function averagelinecheck($array){
        if(array_sum($array) !== 0 && count($array) !== 0)
        $this->averageline = array_sum($array) / count($array);
        return $this->averageline;
    }
}