<?php

/**
 * 	Record controller
 */
class flag_check_b extends Controller {

    function __construct() {
        parent::__construct();
        
            if (!Session::get('loggedIn') == true){
            header('location: ' . URL . 'login/doa');
        }
    }

    function display_view() {
        
        // get display renders
        $this->view->item_nav = $this->flag_check_b_model->get('item_nav');
        
        // get array properties
        $this->view->itemlist = $this->flag_check_b_model->get('itemlist');
        $this->view->record = $this->flag_check_b_model->get('record');
        $this->view->picinfo = $this->flag_check_b_model->get('picinfo');

        // get string properties
        $this->view->flagname = $this->flag_check_b_model->get('flagname');
        $this->view->originalname = $this->flag_check_b_model->get('originalname');
        $this->view->diffname = $this->flag_check_b_model->get('diffname');
        $this->view->flagdesc = $this->flag_check_b_model->get('flagdesc');
        $this->view->originaldesc = $this->flag_check_b_model->get('originaldesc');
        $this->view->diffdesc = $this->flag_check_b_model->get('diffdesc');
        $this->view->flagshort = $this->flag_check_b_model->get('flagshort');
        $this->view->originalshort = $this->flag_check_b_model->get('originalshort');
        $this->view->diffshort = $this->flag_check_b_model->get('diffshort');

        // form related data
        $this->view->form_action = $this->flag_check_b_model->get('form_action');
        $this->view->status_message = $this->flag_check_b_model->get('status_message');
        $this->view->deptstring = $this->flag_check_b_model->get('deptstring');
        $this->view->fbrandstring = $this->flag_check_b_model->get('brandstring');
        $this->view->statusstring = $this->flag_check_b_model->get('statusstring');
        $this->view->current_sort = $this->flag_check_b_model->get('current_sort');
        $this->view->e = $this->flag_check_b_model->get('e');
        $this->view->s = $this->flag_check_b_model->get('s');
        
        // output the view
        $this->view->render('flag_check_b/flag_check_b_view');
    }

}
