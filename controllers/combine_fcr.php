<?php

/**
 * 	combine flag-check-model controller
 */
class combine_fcr extends Controller {

    function __construct() {
        parent::__construct();
    }

    function display_view() {
        $this->view->render('combine_fcr/combine_fcr_view');
        $this->view->test = $this->combine_fcr_model->get('test');
    }
}