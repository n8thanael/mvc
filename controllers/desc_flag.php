<?php

/**
 * 	Record controller
 */
class desc_flag extends Controller {

    function __construct() {
        parent::__construct();
    }

    function display_view() {
        $this->view->render('desc_flag/desc_flag_view');
    }

}
