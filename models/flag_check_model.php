<?php

class flag_check_Model extends model {

    // fetchrecord is pushed the output of fetch() then the controller pulls this information back through public function get();
    private $fetchrecord = array();
    private $comparerecord = array();
    private $fetchpicinfo = array();
    private $fetchname = "";
    private $comparename = "";
    private $fetchdesc = "";
    private $comparedesc = "";
    private $fetchshort = "";
    private $compareshort = "";
    private $currenttable = "";
    private $itemdata = array();
    private $itemlist = array();
    private $item_nav = 0;
    private $compare_nav = 0;
    private $form_action = ""; // directs on POST to different functions wihin the model.
    private $paramstring = "";
    private $param_url_string = "";
    private $deptstring = "";
    private $brandstring = "";
    private $statusstring = "";
    private $status_message = "";
    private $currentset = "";
    private $e = ""; // error string to view
    private $s = ""; // success string to view

    function __construct() {
        parent::__construct();
    }

    /*
     * recives the post information FROM $this->process_form
     * sanatizes and double checks prior to DB entry
     * throws errors as necessary or sends approvals
     */



    // !!!!refactor: this update class seems like it's got speghetti in it...    
    private function update_db($post, $id, $table) {
        
        // !!!!refactor: Do we need to set a base array to update the function...this should be removed backwards to the instance outside of the core
        $basearray = [':new_name' => '', ':new_description' => '', ':new_short' => '', ':status' => '', ':mod_date' => ''];  
        $date = date('Y-m-d H:i:s');
        
        $write_to_db = false;
        if ($post['submit'] == "Apply Updates") {
            
            // !!!!refactor: these additinal values need set OUTSIDE of this update_db since they only pertain to a 
            $additional_values = [':status' => 'updated', ':mod_date' => $date];
            $dbh = $this->db->prepare(
                    "SELECT name,description,short FROM " . $table . " WHERE id = $id"
            );
            $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $dbh->execute();
            $original_record = $dbh->fetchAll();

            
            //// !!!!refactor: Needs to decide if something changed between two records...doesnt actually work...
            //  !!!!refactor:  could be it's own separate function
            if (isset($post['comparedesc'])) {
                if ($original_record[0]['description'] != $post['comparedesc']) {
                    $array[':new_description'] = $post['comparedesc'];
                    $write_to_db = true;
                }
            }
            if (isset($post['comparename'])) {
                if ($original_record[0]['name'] != $post['comparename']) {
                    $array[':new_name'] = $post['comparename'];
                    $write_to_db = true;
                }
            }
            if (isset($post['compareshort'])) {
                if (isset($post['compareshort']) || $original_record[0]['short'] != $post['compareshort']) {
                    $array[':new_short'] = $post['compareshort'];
                    $write_to_db = true;
                }
            }
            if (!$write_to_db) {
                $this->e = "There was no change in the data...don't push [ Apply Updates ] unless you actually update something.";
            }
            
        //  !!!!refactor:  checks the button, if it's a certain thing it's going to update the DB with that....needs handled OUTSIDE of this.
        } elseif ($post['submit'] == "Visual Inspection Required") {
            $additional_values = [':status' => 'inspect', ':mod_date' => $date];
            $write_to_db = true;
        } elseif ($post['submit'] == "Error") {
            $additional_values = [':status' => 'error', ':mod_date' => $date];
            $write_to_db = true;
        } elseif ($post['submit'] == "Approved") {
            $additional_values = [':status' => 'approved', ':mod_date' => $date];
            $write_to_db = true;
        }

        /* Outside of class:
         * A.)  all values are set as basearray1
         * B.)  inputarray is pre-built to specs
         * c.)
         * 
         * new class = $update_db($basearray,$inputarray,$table,$returnerror,$echoerrors) 
         * So this class:
         *  1.)  recieves a BASE ARRAY of all fields to lookup/update
         *  2.)  looks up those fields in DB
         *  3.)  compares all fields with each other...which are different?
         *          -> None are different, return an error
         *          -> something changed...only update changed
         *  4.)  
         *  2.)  SETS A DATE
         *  3.)  Checks did the input form values change (needs to be "compare from DB" -> return TRUE/FALSE
         *          - instead, we can set a separate parameter to FIRST check if values are different - if they are, then TRUE, not FALSE
         *  4.)  
         */

        if ($write_to_db) {
            $array = array_merge($array, $additional_values);
            $basearray = array_merge($basearray, $array);
            $sql = "update " . $table . " SET "
                    . "new_name=:new_name,"
                    . "new_description=:new_description,"
                    . "new_short=:new_short,"
                    . "status=:status,"
                    . "mod_date=:mod_date"
                    . " WHERE id = $id";
            $dbh = $this->db->prepare($sql);
            $dbh->execute($basearray);
            // echo 'error:';  var_dump($dbh->errorInfo());
        } else {
            if (!$write_to_db) {
                $this->e = "There was no change in the data...don't push [ Apply Updates ] unless you actually update something.";
            }
        }
        // var_dump($post);
    }

    public function process_form($param) {
        $id = $param[0];
        $table = $param[1];
        $next = 0;
        $nextprev = array();

        unset($param[0], $param[1]);
        $nextprev = $this->get_next_prev_ids($table, $id);

        if (isset($nextprev['next'])) {
            $next = intval($nextprev['next']);
        } else {
            $next = intval($id) + 1;
        }

        unset($param[0], $param[1]);

        for ($i = 2; $i < (count($param) + 1); $i++) {
            $this->param_url_string .= '/' . $param[$i] . '/' . $param[$i + 1];
            $i++;
        }

        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        foreach ($post as $key => $value) {
            $post[$key] = html_entity_decode(html_entity_decode($value, ENT_QUOTES, "UTF-8"));
        }
        unset($_POST);

        switch ($post['submit']) {
            case 'Apply Updates':
                $code = 'u';
                break;
            case 'Visual Inspection Required':
                $code = 'v';
                break;
            case 'Error':
                $code = 'e';
                break;
            case 'Approved':
                $code = 'a';
                break;
        }
        // run through a gambit of updating the database
        $this->update_db($post, $id, $table);

        if (empty($this->e)) {
            $url = "Location: " . URL . "flag_check/fetch/" . $next . "/" . $table . $this->param_url_string . '/' . $code;
            header($url);
        }
    }

    /*
     *  Fetches the original record FROM the default DB: item
     *  If there is a second parameter passed, it can also call a item to compare for display.
     *  also fetches the original record FROM web
     *  also fetches the picture information FROM web_pic
     */

    public function fetch($param) {
        $id = $param[0];
        $table = $param[1];
        $status = '';

        unset($param[0], $param[1]);

        for ($i = 2; $i < (count($param) + 1); $i++) {
            $this->paramstring .= ' and ' . $param[$i] . ' = "' . $param[$i + 1] . '" ';
            $this->param_url_string .= '/' . $param[$i] . '/' . $param[$i + 1];
            $i++;
        }

        if (Session::get('show_only_status') !== NULL) {
            $status = Session::get('show_only_status');
        }
        if (isset($id) && isset($table)) {

            $dbh = $this->db->prepare(
                    "SELECT * FROM " . $table . " WHERE id = " . $id
            );
            $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $dbh->execute();
            // comparerecord is avaialable to rendner on the view
            $this->comparerecord = $dbh->fetchAll();
            $web_id = $this->comparerecord[0]["web_id"];
            $sku_id = $this->comparerecord[0]["style_id"];

            // sends records to be compared
            $this->comparename = $this->comparerecord[0]["flag_name"];
            $this->comparedesc = $this->comparerecord[0]["flag_description"];
            $this->compareshort = $this->comparerecord[0]["flag_short"];
            $this->fetchname = $this->comparerecord[0]["name"];
            $this->fetchdesc = $this->comparerecord[0]["description"];
            $this->fetchshort = $this->comparerecord[0]["short"];
            $this->item_nav = $this->render_item_nav($table, $id);
            $this->dashboard = $this->render_dashboard($table, $id, $status, URL . 'flag_check/fetch/' . $id . '/' . $table . '/');
        }

        $dbh = $this->db->prepare(
                "SELECT url,picture_id FROM " . $table . " WHERE id = " . $id
        );
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $this->fetchpicinfo = $dbh->fetchAll();
        $this->form_action = URL . 'flag_check/process_form/' . $id . '/' . $table . $this->param_url_string;

        // based on return inFROMation FROM the url (set by $this->process_form) we can update the user of progress.
        switch (end($param)) {
            case 'u' :
                $this->s = 'Updates applied on previous record.';
                break;
            case 'v' :
                $this->s = 'Visual inspection reqested on previous record.';
                break;
            case 'e' :
                $this->s = 'Error status set on previous record.';
                break;
            case 'a' :
                $this->s = 'Approved previous record.';
                break;
        }
    }

    /*
     * fuction collects 2 paramaters FROM URL
     * 1: item code
     * 2: current or new table to create / modify
     * will look for the id within table: item
     * will run all of $cleanup() within libs\nate
     * will save modified record to current/new table
     */

    public function show_only_status($param) {
        $item = intval($param[0]);
        $table = $param[1];
        $status = $param[2];
        unset($param[0], $param[1], $param[2]);
        for ($i = 3; $i < (count($param) + 2); $i++) {
            $this->paramstring .= ' and ' . $param[$i] . ' = "' . $param[$i + 1] . '" ';
            $this->param_url_string .= '/' . $param[$i] . '/' . $param[$i + 1];
            $i++;
        }

        $dbh = $this->db->prepare("select distinct status FROM " . $table);
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $result_array = $dbh->fetchAll();
        
        
        if (in_array($status , array_map(function($element){return $element['status'];}, $result_array))) {
//      if (in_array($status, array_column($result_array, 'status'))) {
            
            Session::set('show_only_status', $status);
        } else {
            Session::set('show_only_status', null);
        }

         header("location: " . URL . 'flag_check/fetch/' . $item . '/' . $table . $this->param_url_string);
    }

    /*
     * method takes a table
     * creates a temporary table
     * parces the inv_num into style_id,sku_id,store_id if they exist
     * leaves a 0 if they don't
     * updates the previous table
     * deletes the temp_table when finished
     */

    public function split_sku_ids($input) {
        $table = $input[0];
        $dbh = $this->db->prepare('select id,inv_num,style_id FROM ' . $table);
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $record = $dbh->fetchAll();
        $explode = array();
        // keeps the array later array FROM out putting nothing.
        $template = array(null, null, null, null);
        // sets up values that run the 1000-item insert buffer 
        $count = 0;
        $batch_count = 0;
        $total_count = count($record);
        // generate a 5-string charater name for the temporary table
        $temp_table = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
        $dbh = $this->db->prepare("CREATE TABLE " . $temp_table . " LIKE " . $table);
        $dbh->execute();
        $sql_values = "";

        // timer tells how long the process took
        //$msc=microtime(true);
        foreach ($record as $value) {
            $count++;
            //Possible to have it run over a table that already has some fields filled out.
            //if($value['style_id'] == NULL){   
            $explode = explode("-", $value['inv_num']);
            $explode = array_merge($explode, $template);
            $sql_values .= '("';
            $sql_values .= $value['id'];
            $sql_values .= '","' . $explode[0];
            $sql_values .= '","' . $explode[1];
            $sql_values .= '","' . $explode[2];
            $sql_values .= '"),';
            $batch_count++;
            if ($batch_count == 1000 || $total_count == $count) {
                $sql_values = rtrim($sql_values, ",");

                $dbh = $this->db->prepare('Insert into ' . $temp_table . '(id,style_id,sku_id,store_id) VALUES ' . $sql_values);
                $dbh->execute();
                $sql_values = "";
                $batch_count = 0;
            }
            // } // if skip the record if there is already a style_id
        }

        $dbh = $this->db->prepare('UPDATE ' . $table . ' A JOIN ' . $temp_table . ' B ON A.id = B.id SET A.style_id = B.style_id, A.sku_id = B.sku_id, A.store_id = B.store_id WHERE A.id = B.id');
        $dbh->execute();
        $dbh = $this->db->prepare("DROP table " . $temp_table);
        $dbh->execute();

        // timer tells us how long it took
        // $msc=microtime(true)-$msc;
        // echo $msc.' seconds<br>'; // in seconds
    }

    public function get_next_prev_ids($table, $id) {
        $out = array();
        // select the next and previous id's - if Session::get() shows the check flag.
        // $this->show_only_status cleans out any possible non-statuses
        if (Session::get('show_only_status') !== null) {
            $status = Session::get('show_only_status');
            $sql = 'SELECT * FROM (select max(id) as id '
                    . ' FROM ' . $table . ' WHERE id < ' . $id . ' and status="' . $status . '" ' . $this->paramstring . ' ORDER BY id DESC LIMIT 1) as A '
                    . 'UNION (select min(id) as id FROM ' . $table . ' WHERE id > ' . $id . ' and status="' . $status . '" ' . $this->paramstring . ' ORDER BY id ASC LIMIT 1);';
            $dbh = $this->db->prepare($sql);
        } else {
            // select the next and previous id's - reguardless of flag
            $sql = 'SELECT * FROM (select max(id) as id '
                    . ' FROM ' . $table . ' WHERE id < ' . $id . ' ' . $this->paramstring . ' ORDER BY id DESC LIMIT 1) as A '
                    . 'UNION (select min(id) as id FROM ' . $table . ' WHERE id > ' . $id . ' ' . $this->paramstring . ' ORDER BY id ASC LIMIT 1);';
            $dbh = $this->db->prepare($sql);
        }
        

        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $temp = $dbh->fetchAll();
        if (isset($temp[0]) || isset($temp[1])) {

            if ($temp[0]['id'] != NULL) {
                $out['prev'] = $temp[0]['id'];
            }
            if (isset($temp[1])) {
                if ($temp[1]['id'] != NULL) {

                    $out['next'] = $temp[1]['id'];
                }
             }
        }

        return $out;
    }

    public function render_dashboard($table, $id, $status, $url) {
        $deptstring = '';
        $brandstring = '';
        $statusstring = '';

        if ($status != '') {
            $this->status_message = 'How many records have a certain staus.... ex: have status: ' . $status . ' / of total records';
            $status = ' WHERE status = "' . $status . '" ';
        } else {
            $this->status_message = 'How many total records have been changed... ex: changed / unchanged';
            $status = ' WHERE status != "check" ';
        }
        // render dept status line 
        $sql = 'SELECT A.dept,IFNULL(B.count,0) as done,count(*) as count '
                . 'FROM ' . $table . ' as A '
                . 'LEFT JOIN(select dept, count(*) as count '
                . 'FROM ' . $table . ' ' . $status . ' GROUP BY dept) as B '
                . 'ON A.dept = B.dept '
                . 'GROUP BY dept ORDER BY count DESC LIMIT 20;';
        $dbh = $this->db->prepare($sql);
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $temp = $dbh->fetchAll();
        for ($i = 0; $i < count($temp); $i++) {
            $deptstring .= '<li>';
            foreach ($temp[$i] as $k => $v) {
                if ($k == 'dept') {
                    $deptstring .= '<a href="' . $url . 'OF19/1/dept/' . $v . '">1</a> | <a href="' . $url . 'OF19/89/dept/' . $v . '">89</a> | <a href="' . $url . 'dept/' . $v . '">' . $v . '</a>: ';
                } elseif ($k == 'done') {
                    $deptstring .= $v . '/';
                } elseif ($k == 'count') {
                    $deptstring .= $v;
                }
            }
            $deptstring .= '</li>';
        }
        $this->deptstring = '<ul>' . $deptstring . '</ul>';

        $sql = 'SELECT A.brand,IFNULL(B.count,0) as done,count(*) as count '
                . 'FROM ' . $table . ' as A '
                . 'LEFT JOIN(select brand, count(*) as count '
                . 'FROM ' . $table . ' ' . $status . ' GROUP BY brand) AS B '
                . 'ON A.brand = B.brand '
                . 'GROUP BY brand ORDER BY count DESC LIMIT 20;';
        $dbh = $this->db->prepare($sql);
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $temp = $dbh->fetchAll();
        // var_dump($dbh->errorInfo());
        for ($i = 0; $i < count($temp); $i++) {
            $brandstring .= '<li>';
            foreach ($temp[$i] as $k => $v) {
                if ($k == 'brand') {
                    $brandstring .= '<a href="' . $url . 'OF19/1/brand/' . $v . '">1</a> | <a href="' . $url . 'OF19/89/brand/' . $v . '">89</a> | <a href="' . $url . 'brand/' . $v . '">' . $v . '</a>: ';
                } elseif ($k == 'done') {
                    $brandstring .= $v . '/';
                } elseif ($k == 'count') {
                    $brandstring .= $v;
                }
            }
            $brandstring .= '</li>';
        }
        $this->brandstring = '<ul>' . $brandstring . '</ul>';

        $sql = 'SELECT status, count(*) as count FROM ' . $table . ' GROUP BY status ORDER BY count DESC;';
        $dbh = $this->db->prepare($sql);
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $temp = $dbh->fetchAll();
        // var_dump($dbh->errorInfo());
        for ($i = 0; $i < count($temp); $i++) {
            $statusstring .= '<li>';
            foreach ($temp[$i] as $k => $v) {
                //<a href="http://127.0.0.1/MVC/flag_check/show_only_status/704/flagged_desc/approved">approved</a>
                if ($k == 'status') {
                    $statusstring .= '<a href="' . str_replace('fetch', 'show_only_status', $url) . $v . '">' . $v . '</a>: ';
                } elseif ($k == 'done') {
                    $statusstring .= $v . '/';
                } elseif ($k == 'count') {
                    $statusstring .= $v;
                }
            }
            $statusstring .= '</li>';
        }
        $this->statusstring = '<ul>' . $statusstring . '</ul>';
    }
    
    // return a list of where the user is currenlty based on the URL $this->param_url_string
    private function current_sort($string = '') {
        $a = explode('/', strtolower($string));
        if (count($a) > 3) {
            $string = $a[1] . ':' . $a[2] . ', ' . $a[3] . ':' . $a[4];
        } elseif (count($a) > 1) {
            $string = $a[1] . ':' . $a[2];
        }
        return $string;
    }

    // recieves an item number and generates nav bar PREV | NEXT if there are more items.
    public function render_item_nav($table, $id) {
        $this->current_sort = $this->current_sort($this->param_url_string);
        $displaystatus = '';
        $this->currenttable = $table;
        $nextprev = array();
        $nextprev = $this->get_next_prev_ids($table, $id);

        $out = '<div style="width:100%;"><div style="float:left">';

        if (isset($nextprev['prev'])) {
            $out .= '<a href="' . URL . 'flag_check/fetch/' . $nextprev['prev'] . '/' . $table . $this->param_url_string . '">prev</a> |';
        }
        if (isset($nextprev['next'])) {
            $out .= ' <a href="' . URL . 'flag_check/fetch/' . $nextprev['next'] . '/' . $table . $this->param_url_string . '">next</a>';
        }
        
        if (count($nextprev)< 1 ){
            $out .= '<i>no result set to browse...select "show only status: all"</i>';
        }

        if (Session::get('show_only_status') !== NULL) {
            $displaystatus = 'Only show status: ' . Session::get('show_only_status');
        } else {
            $displaystatus = 'Show every status';
        }
        $out .= '</div>';
        $out .= '<div style="float:right">';
        $out .= ' <span>' . $displaystatus . '</span> &nbsp;&nbsp;|&nbsp;';
        
        function output_URL($out,$table,$id,$urlstring){
            $a = array('approved','check','updated','inpsect','error');
                    foreach($a as $v){
                        If(Session::get('show_only_status') == $v){$highlight = 'style="background-color:yellow; padding:5px; display:inline-block;"';} else {$highlight = '';}
                        $out .= ' <a href="' . URL . 'flag_check/show_only_status/' . $id . '/' . $table . '/'.$v . $urlstring . '" '.$highlight.' >'.$v.'</a> | ';
                    }
                    If(Session::get('show_only_status') == ''){$highlight = 'style="background-color:yellow; padding:5px; display:inline-block;"';} else {$highlight = '';}
                    $out .= ' <a href="' . URL . 'flag_check/show_only_status/' . $id . '/' . $table . '/" '.$highlight.' >all</a>';
                    $out .= '</div></div>';
                    return $out;
        }
         return output_URL($out,$table,$id,$this->param_url_string);
    }

    public function get($var) {
        return $this->{$var};
    }

}
