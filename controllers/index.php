<?php

/**
 * 	Index controller 
 */
class Index extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function display_view()
    {
        $this->view->render('index/index_view');
    }

    function details($value = null)
    {
        $this->view->render('index/index_view');
        echo 'you are insde the index-details ' . $value;
    }
}