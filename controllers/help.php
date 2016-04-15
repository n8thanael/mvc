<?php

/**
 * 	Help controller
 */
class Help extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->get_blah();

    }
    
    function display_view()
    {
        $this->view->render('help/help_view');
    }

    public function other($arg = false)
    {
        require 'models/help_model.php';
        $model = new Help_Model();
        echo '<pre>';
        echo var_dump($arg) . '<br>';
        echo '</pre>';
        $this->display_view();
    }

    public function get_blah() {
        var_dump($this);
        echo "get_blah fired.";
    }
    

}