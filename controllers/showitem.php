<?php

/**
 * 	Record controller
 */
class showitem extends Controller {

    function __construct() {
        parent::__construct();
    }

    function display_view() {
        $this->view->item_nav = $this->showitem_model->get('item_nav');
        $this->view->fetchrecord = $this->showitem_model->get('fetchrecord');
        $this->view->fetchdesc = $this->showitem_model->get('fetchdesc');
        $this->view->itemlist = $this->showitem_model->get('itemlist');


        
        $this->view->comparerecord = $this->showitem_model->get('comparerecord');
        $this->view->comparedesc = $this->showitem_model->get('comparedesc');
        $cleanup = new libs\nate\cleanup();
         $this->view->fetchwasheddesc = $cleanup->washall($this->showitem_model->get('fetchdesc'));

      
        $this->view->render('showitem/showitem_view');
    }

}
