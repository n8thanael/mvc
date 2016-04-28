<?php

class flag_check_b_Model extends model {

    // record is pushed the output of fetch() then the controller pulls this information back through public function get();
    private $record = array();
    private $picinfo = array();
    private $flagname = "";
    private $originalname = "";
    private $flagdesc = "";
    private $originaldesc = "";
    private $flagshort = "";
    private $originalshort = "";
    private $currenttable = "";
    private $itemdata = array();
    private $itemlist = array();
    private $item_nav = 0;
    private $compare_nav = 0;
    private $form_action = ""; // directs on POST to different functions wihin the model.
    private $paramstring = "";
    private $param_url_string = "";
    private $param_url_array = array();
    private $deptstring = "";
    private $brandstring = "";
    private $statusstring = "";
    private $status_message = "";
    private $currentset = "";
    private $classurl = "";
    private $e = ""; // error string to view
    private $s = ""; // success string to view
    // required parameter to be set:
    private $possible_statuses = array('approved', 'check', 'updated', 'inpsect', 'error');

    function __construct() {
        parent::__construct();
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

        $urlmod = new \libs\nate\navigation\translate_url_string();
        $this->param_url_string = strtolower($urlmod->urlwherestring($urlmod->prep_url_array($param, 2)));
        $this->paramstring = strtolower($urlmod->prep_url_array($param, 2));
        $this->param_url_array = $urlmod->urlarray($this->paramstring);
        $this->classurl = str_replace('_Model::', '/', __METHOD__) . '/';

        if (isset($id) && isset($table)) {

            $dbh = $this->db->prepare(
                    "SELECT * FROM " . $table . " WHERE id = " . $id
            );
            $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $dbh->execute();
            // record is avaialable to rennder on the view
            $this->record = $dbh->fetchAll();
            $sku_id = $this->record[0]["style_id"];

            // sends records to be compared
            $this->flagname = $this->record[0]["flag_name"];
            $this->flagdesc = $this->record[0]["flag_description"];
            $this->flagshort = $this->record[0]["flag_short"];
            $this->originalname = $this->record[0]["name"];
            $this->originaldesc = $this->record[0]["description"];
            $this->originalshort = $this->record[0]["short"];

            // refacoring this->
            $this->item_nav = $this->render_item_nav($table, $id);
            $this->dashboard = $this->render_dashboard($table, $id, $status, URL . $this->classurl . $id . '/' . $table . '/');
        }

        $dbh = $this->db->prepare(
                "SELECT url,picture_id FROM " . $table . " WHERE id = " . $id
        );
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $this->picinfo = $dbh->fetchAll();
        $this->form_action = URL . 'flag_check_b/process_form/' . $id . '/' . $table . $this->param_url_string;

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
     * !!!! new CRUD libraries are availble for refactoring
     * recives the post information FROM $this->process_form
     * sanatizes and double checks prior to DB entry
     * throws errors as necessary or sends approvals
     */

    private function update_db($post, $id, $table) {

        //  !!!!refactor:  new CRUD libraries need to clean this up
        $basearray = [':new_name' => '', ':new_description' => '', ':new_short' => '', ':status' => '', ':mod_date' => ''];
        $date = date('Y-m-d H:i:s');

        $write_to_db = false;
        if ($post['submit'] == "Apply Updates") {

            //  !!!!refactor:  new CRUD libraries need to clean this up
            $additional_values = [':status' => 'updated', ':mod_date' => $date];
            $dbh = $this->db->prepare(
                    "SELECT name,description,short FROM " . $table . " WHERE id = $id"
            );
            $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $dbh->execute();
            $original_record = $dbh->fetchAll();


            //  !!!!refactor:  new CRUD libraries need to clean this up
            if (isset($post['originaldesc'])) {
                if ($original_record[0]['description'] != $post['originaldesc']) {
                    $array[':new_description'] = $post['originaldesc'];
                    $write_to_db = true;
                }
            }
            if (isset($post['originalname'])) {
                if ($original_record[0]['name'] != $post['originalname']) {
                    $array[':new_name'] = $post['originalname'];
                    $write_to_db = true;
                }
            }
            if (isset($post['originalshort'])) {
                if (isset($post['originalshort']) || $original_record[0]['short'] != $post['originalshort']) {
                    $array[':new_short'] = $post['originalshort'];
                    $write_to_db = true;
                }
            }
            if (!$write_to_db) {
                $this->e = "There was no change in the data...don't push [ Apply Updates ] unless you actually update something.";
            }

            //  !!!!refactor:  new CRUD libraries need to clean this up
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

        //  !!!!refactor:  new CRUD libraries need to clean this up
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

    /*
     * function intercepts the POST form
     * and processes information contained as well by triggering method: update_db
     * figures out the next id within the same sort using method: get_next_prev_ids
     * uses switch to interperate and update the user while it passes to the next record
     */

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

        // direct post used insead of filter so we can recieve TinyMCE html-rich code
        // $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $post = $_POST;
        foreach ($post as $key => $value) {
            // post is NOT sanitized to allow TinyMCE html-rich code to pass through
            // $post[$key] = html_entity_decode(html_entity_decode($value, ENT_QUOTES, "UTF-8"));
            $post[$key] = $value;
        }
        unset($_POST);

        // udpate the user on the next record
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
            $url = "Location: " . URL . "flag_check_b/fetch/" . $next . "/" . $table . $this->param_url_string . '/' . $code;
            header($url);
        }
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

    /*
     * receives the table and id
     * also calculates what status is currently requested by using session::get('show_only_Stats')
     */

    public function get_next_prev_ids($table, $id) {
        $out = array();
        // $this->show_only_status cleans out any possible non-statuses
        // select the next and previous id's - reguardless of flag
        $sql = 'SELECT * FROM (select max(id) as id '
                . ' FROM ' . $table . ' WHERE id < ' . $id . ' ' . $this->paramstring . ' ORDER BY id DESC LIMIT 1) as A '
                . 'UNION (select min(id) as id FROM ' . $table . ' WHERE id > ' . $id . ' ' . $this->paramstring . ' ORDER BY id ASC LIMIT 1);';
        $dbh = $this->db->prepare($sql);


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

    /*
     * runs queries to retrieve bottom dashboard information
     * queries are based on current table, id and status
     * status is passed via the session prior to this function being called
     */

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
                //<a href="http://127.0.0.1/MVC/flag_check_b/show_only_status/704/flagged_desc/approved">approved</a>
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
        $status_exists = false;
        $displaystatus = '';
        $switchstring = '';
        $this->currenttable = $table;

        // prepare the prev-next strings...
        $itemnavobj = new \libs\nate\navigation\get_next_prev_ids($id, $table, $this->param_url_string, $this->classurl, $this->paramstring, $this->db);
        $this->current_sort = $this->current_sort($this->param_url_string);
        $prevnextstring = $itemnavobj->get();

        // Setup the string that will output to the Nav Bar
        $out = '<div style="width:100%;"><div style="float:left">';

        // With the current sort, is the prevnext string possible?
        if (strlen($prevnextstring) > 0) {
            $out .= $prevnextstring;
        } else {
            $out .= '<i>no result set to browse...select "show only status: all"</i>';
        }

        // if we have a status set...
        // is it a propper status?  Check against class property array: this->possible_statuses
        // if yes, then status exists and output a string to show which status we're sorting by
        // if not...perhaps we have other sorts...pass those on to $switchstring
        if (isset($this->param_url_array['status'])) {
            $switchstring = str_replace('status/' .$this->param_url_array['status'],'', $this->paramstring);
            if (in_array($this->param_url_array['status'], $this->possible_statuses, TRUE)) {
                $displaystatus = 'Only show status: ' . $this->param_url_array['status'];
                $status_exists = true;
            }
        } else {
            $switchstring = $this->paramstring;
        }

        // some HTML for the end of the div and start of the next
        $out .= '</div>';
        $out .= '<div style="float:right">';
        $out .= ' <span>' . $displaystatus . '</span> &nbsp;&nbsp;|&nbsp;';

        // Check against class property array: this->possible_statuses - highlight the current status
        foreach ($this->possible_statuses as $v) {
                If ($status_exists && $this->param_url_array['status'] == $v) {
                    $highlight = 'style="background-color:yellow; padding:5px; display:inline-block;"';
                } else {
                    $highlight = '';
                }
                                
                $out .= ' <a href="' . URL . $this->classurl . '/' . $id . '/' . $table . '/status/' . $v  . '/'. $switchstring .'" ' . $highlight . ' >' . $v . '</a> | ';
            }
        if ($status_exists) {
            $highlight = '';
        } else {
            $highlight = 'style="background-color:yellow; padding:5px; display:inline-block;"';
        }
        
        $out .= ' <a href="' . URL . $this->classurl . '/' . $id . '/' . $table . '/" ' . $highlight . ' >all</a>';
        $out .= '</div></div>';
        return $out;
    }

    public function get($var) {
        return $this->{$var};
    }

}