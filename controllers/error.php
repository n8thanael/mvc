<?php

/**
 * 	Error controller
 */
class Error extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function display_view()
    {
        $this->view->render('error/error_view');
    }
}