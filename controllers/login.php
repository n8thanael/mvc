<?php

/**
 * 	Login controller
 */
class Login extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function display_view()
    {

        $this->view->render('login/login_view');
    }

    function run()
    {
        $this->model->run();
    }
}