<?php

/**
 * 	Record controller
 */
class desc_fix extends Controller {

    function __construct() {
        parent::__construct();
        
            if (!Session::get('loggedIn') == true){
            header('location: ' . URL . 'login/doa');
        }
    }

    function display_view() {
        
        // get display renders
        $this->view->item_nav = $this->desc_fix_model->get('item_nav');
        
        // get array properties
        $this->view->itemlist = $this->desc_fix_model->get('itemlist');
        $this->view->record = $this->desc_fix_model->get('record');
        $this->view->picinfo = $this->desc_fix_model->get('picinfo');

        // get string properties
        $this->view->flagname = $this->desc_fix_model->get('flagname');
        $this->view->originalname = $this->desc_fix_model->get('originalname');
        $this->view->diffname = $this->desc_fix_model->get('diffname');
        $this->view->flagdesc = $this->desc_fix_model->get('flagdesc');
        $this->view->originaldesc = $this->desc_fix_model->get('originaldesc');
        $this->view->diffdesc = $this->desc_fix_model->get('diffdesc');
        $this->view->flagshort = $this->desc_fix_model->get('flagshort');
        $this->view->originalshort = $this->desc_fix_model->get('originalshort');
        $this->view->diffshort = $this->desc_fix_model->get('diffshort');

        // form related data
        $this->view->form_action = $this->desc_fix_model->get('form_action');
        $this->view->status_message = $this->desc_fix_model->get('status_message');
        $this->view->deptstring = $this->desc_fix_model->get('deptstring');
        $this->view->fbrandstring = $this->desc_fix_model->get('brandstring');
        $this->view->statusstring = $this->desc_fix_model->get('statusstring');
        $this->view->current_sort = $this->desc_fix_model->get('current_sort');
        $this->view->paramstring = $this->desc_fix_model->get('paramstring');
        $this->view->e = $this->desc_fix_model->get('e');
        $this->view->s = $this->desc_fix_model->get('s');
        
        // output the view
        $this->view->render('desc_fix/desc_fix_view');
    }

}
