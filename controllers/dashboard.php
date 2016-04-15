<?php

/**
 * 	Error controller
 */
class Dashboard extends Controller
{

    function __construct()
    {
        parent::__construct();
        /*
        $logged = Session::get('loggedIn');
        if ($logged == false) {
            Session::destroy();
            header('location: ' . URL . 'login');
            exit;
        }
         * 
         */
    }

    function display_view()
    {
        $this->view->render('dashboard/dashboard_view');
    }
}