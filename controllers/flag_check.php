<?php

/**
 * 	Record controller
 */
class flag_check extends Controller {

    function __construct() {
        parent::__construct();
    }

    function display_view() {
        // get display renders
        $this->view->item_nav = $this->flag_check_model->get('item_nav');
        
        // get array properties
        $this->view->itemlist = $this->flag_check_model->get('itemlist');
        $this->view->fetchrecord = $this->flag_check_model->get('fetchrecord');
        $this->view->comparerecord = $this->flag_check_model->get('comparerecord');
        $this->view->fetchpicinfo = $this->flag_check_model->get('fetchpicinfo');

        // get string properties
        $this->view->fetchname = $this->flag_check_model->get('fetchname');
        $this->view->comparename = $this->flag_check_model->get('comparename');
        $this->view->fetchdesc = $this->flag_check_model->get('fetchdesc');
        $this->view->comparedesc = $this->flag_check_model->get('comparedesc');
        $this->view->fetchshort = $this->flag_check_model->get('fetchshort');
        $this->view->compareshort = $this->flag_check_model->get('compareshort');

        // form related data
        $this->view->form_action = $this->flag_check_model->get('form_action');
        $this->view->status_message = $this->flag_check_model->get('status_message');
        $this->view->deptstring = $this->flag_check_model->get('deptstring');
        $this->view->fbrandstring = $this->flag_check_model->get('brandstring');
        $this->view->statusstring = $this->flag_check_model->get('statusstring');
        $this->view->current_sort = $this->flag_check_model->get('current_sort');
        $this->view->e = $this->flag_check_model->get('e');
        $this->view->s = $this->flag_check_model->get('s');
        
        // output the view
        $this->view->render('flag_check/flag_check_view');
    }

}
