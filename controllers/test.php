<?php

/**
 * 	Record controller
 */
class test extends Controller {

    function __construct() {
        parent::__construct();
    }

    function display_view() {
        $this->view->render('test/test_view');
    }

}
