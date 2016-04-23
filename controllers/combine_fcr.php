<?php

/**
 * 	combine flag-check-model controller
 */
class combine_fcr extends Controller {

    function __construct() {
        parent::__construct();
    }

    function display_view() {
        $this->view->item_nav = $this->combine_fcr_model->get('item_nav');
        $this->view->fetchrecord = $this->combine_fcr_model->get('fetchrecord');
        $this->view->comparerecord = $this->combine_fcr_model->get('comparerecord');
        
        $this->view->render('combine_fcr/combine_fcr_view');
    }
}