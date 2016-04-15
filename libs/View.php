<?php

class View {

    function __construct() {
    }

       public function render($path) {
           require 'views/header.php';
           require 'views/' . $path . '.php';
           require 'views/footer.php';
       }
}
