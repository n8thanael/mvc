<?php

class desc_fix_Model extends model {

    // record is pushed the output of fetch() then the controller pulls this information back through public function get();
    private $record = array();
    private $picinfo = array();
    private $flagname = "";
    private $originalname = "";
    private $diffname = "";
    private $flagdesc = "";
    private $originaldesc = "";
    private $diffdesc = "";
    private $flagshort = "";
    private $originalshort = "";
    private $diffshort = "";
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
    private $current_sort = "";
    private $classurl = "strtolower";
    private $classname = "";
    private $previously_modified_flag = false;
    private $desc_sim = 0;
    private $short_sim = 0;
    private $e = ""; // error string to view
    private $s = ""; // success string to view
    // required parameter to be set:
    private $possible_statuses = array('approved', 'check', 'updated', 'inspect', 'error', 'skip', 'shortie_warn_auto-fixed','auto-fixed','shortie_warn_check','warn_auto-fixed','warn_check','no-change');

    /*
     *      // record the similar text() differnce percentage of original and newly entered values.
      similar_text(str_replace (" ", "",$post['originaldesc']), str_replace (" ", "",$original_record[0]['description']),$percent);
      $this->desc_sim = number_format($percent, 2);
      similar_text(str_replace (" ", "",$post['originalshort']), str_replace (" ", "",$original_record[0]['short']),$percent);
      $this->short_sim = number_format($percent, 2);

      $additional_values = [':short_sim' => $this->short_sim,':desc_sim' => $this->desc_sim, ':status' => 'updated', ':lastmod_user' => $user, ':mod_date' => $date];
     */

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
        $toggle = false;
        $togglestring = '';
        $id = $param[0];
        $table = $param[1];
        $status = '';

        if (Session::get('user') !== NULL) {
            $user = Session::get('user');
            $dbh = $this->db->prepare(
                    "update " . $table . " SET lastview_stamp = now(), lastview_user = '" . $user . "' where ID = " . $id
            );
            $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $dbh->execute();
        }

        // The toggle string modifies the washed results displayed on the screen for debug purposes
        if (isset($param[2]) && $param[2] == 'tog') {
            $toggle = true;
            if (isset($param[3])) {
                $togglestring = $param[3];
                unset($param[3]);
            } else
                echo "S1_BAD_CHARS,S1_HTML,S1_WARN,S2_BREAKS,S3_LIST,S4_FORMAT_P,S5_BAD_P,S6_FRAG,SX_APPEND";
            unset($param[2]);
            echo $togglestring;
        }

        $urlmod = new \libs\nate\navigation\translate_url_string();
        $this->param_url_string = strtolower($urlmod->urlwherestring($urlmod->prep_url_array($param, 2)));
        $this->paramstring = strtolower($urlmod->prep_url_array($param, 2));
        $this->param_url_array = $urlmod->urlarray($this->paramstring);
        $this->classurl = str_replace('_Model::', '/', __METHOD__) . '/';

        if (isset($id) && isset($table)) {
            $cleanup = new \libs\nate\cleanup();
            $difference = new \libs\nate\diff\difference();
            $warn = new \libs\nate\resources\S1_WARN();

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


            if ($this->record[0]["new_name"] != '') {
                $this->flagname = $this->record[0]["new_name"];
                $this->previously_modified_flag = true;
            }
            if ($this->record[0]["new_description"] != '') {
                $this->flagdesc = $this->record[0]["new_description"];
                $this->previously_modified_flag = true;
            }
            if ($this->record[0]["new_short"] != '') {
                $this->flagshort = $this->record[0]["new_short"];
                $this->previously_modified_flag = true;
            }

            // the toggle string is sent to the cleanup.class
            if ($toggle) {
                if ($this->flagname == '') {
                    $array = $cleanup->washall_with_report_toggle($this->record[0]["name"], $togglestring);
                    $this->flagname = $array['text'];
                    if ($array['report'] != '') {
                        echo "<p>flagname triggered: " . $array['report'] . "</p>";
                    }
                }
                if ($this->flagdesc == '') {
                    $array = $cleanup->washall_with_report_toggle($this->record[0]["description"], $togglestring);
                    $this->flagdesc = $array['text'];
                    if ($array['report'] != '') {
                        echo "<p>flagdesc triggered: " . $array['report'] . "</p>";
                    }
                }
                if ($this->flagshort == '') {
                    $array = $cleanup->washall_with_report_toggle($this->record[0]["short"], $togglestring);
                    $this->flagshort = $array['text'];
                    if ($array['report'] != '') {
                        echo "<p>flagshort triggered: " . $array['report'] . "</p>";
                    }
                }
            } else {

                if ($this->flagname == '') {
                    $this->flagname = $cleanup->washall($this->record[0]["name"]);
                }
                if ($this->flagdesc == '') {
                    $this->flagdesc = $cleanup->washall($this->record[0]["description"]);
                }
                if ($this->flagshort == '') {
                    $this->flagshort = $cleanup->washall($this->record[0]["short"]);
                }
            }
            //$this->diffname = $difference->get_diff($this->record[0]["name"], $this->flagname);
            $this->diffdesc = $difference->get_diff($this->record[0]["description"], $this->flagdesc);
            $this->diffshort = $difference->get_diff($this->record[0]["short"], $this->flagshort);

            $this->originalname = $this->record[0]["name"];
            $washall_desc = $warn->washall($this->record[0]["description"]);
            $washall_short = $warn->washall($this->record[0]["short"]);
            if(strlen($washall_desc) > 1) {$this->originaldesc = $washall_desc;} else {$this->originaldesc = $this->record[0]["description"];}
            if(strlen($washall_short) > 1) {$this->originalshort = $washall_short;} else {$this->originalshort = $this->record[0]["short"];}

            $this->item_nav = $this->render_item_nav($table, $id);
            $this->dashboard = $this->render_dashboard($table, $id, $status, URL . $this->classurl . $id . '/' . $table . '/');
        }

        $dbh = $this->db->prepare(
                "SELECT url,picture_id FROM " . $table . " WHERE id = " . $id
        );
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();

        // hack to make the picture id a default value if it's not yet set...will most likely break beyond this project
        $this->picinfo = $dbh->fetchAll();
        if ($this->picinfo[0]['url'] == NULL) {
            $this->picinfo[0]['url'] = 'http://www.woodburyoutfitters.com/shop/-' . $sku_id;
        };

        $this->classname = str_replace("_model", "", strtolower(__CLASS__));
        $dynamic_url = str_replace('//', '/', $this->classname . '/process_form/' . $id . "/" . $table . '/' . $this->paramstring);
        $this->form_action = URL . $dynamic_url;

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
            case 's' :
                $this->s = 'Previous record was skipped.';
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
        $array = array();
        $basearray = [':new_name' => '', ':new_description' => '', ':new_short' => '', ':status' => '', ':lastmod_user' => '', ':mod_date' => ''];
        $date = date('Y-m-d H:i:s');
        $user = Session::get('user');

        $write_to_db = false;
        if ($post['submit'] == "Apply Updates") {
            //  !!!!refactor:  new CRUD libraries need to clean this up

            $additional_values = [':status' => 'updated', ':lastmod_user' => $user, ':mod_date' => $date];
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
                    $redosim = true;
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
            $additional_values = [':status' => 'inspect', ':lastmod_user' => $user, ':mod_date' => $date];
            $write_to_db = true;
        } elseif ($post['submit'] == "Error") {
            $additional_values = [':status' => 'error', ':lastmod_user' => $user, ':mod_date' => $date];
            $write_to_db = true;
        } elseif ($post['submit'] == "Approved") {
            $additional_values = [':status' => 'approved', ':lastmod_user' => $user, ':mod_date' => $date];
            $write_to_db = true;
        } elseif ($post['submit'] == "Skip" ) {
            $additional_values = [':status' => 'skipped', ':lastmod_user' => $user, ':mod_date' => $date];
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
                    . "lastmod_user=:lastmod_user,"
                    . "mod_date=:mod_date,"
                    . "lastmod_stamp = now()"
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
        
        // pr($_POST);
        
        unset($_POST);

        // update the user on the next record --  BROKEN
        /* switch ($post['submit']) {
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
            case 'Skipped':
                $code = 's';
                break;
        }
          */
 
        // run through a gambit of updating the database
        $this->update_db($post, $id, $table);

       
           $url = "Location: " . URL . "desc_fix/fetch/" . $next . "/" . $table . $this->param_url_string . '/';
            header($url);
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
                //<a href="http://127.0.0.1/MVC/desc_fix/show_only_status/704/flagged_desc/approved">approved</a>
                if ($k == 'status') {
                    $statusstring .= '<a href="' . $url . 'status/' . $v . '">' . $v . '</a>: ';
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
            $switchstring = str_replace('status/' . $this->param_url_array['status'], '', $this->paramstring);
            if (in_array($this->param_url_array['status'], $this->possible_statuses, TRUE)) {
                // $displaystatus = 'Only show status: ' . $this->param_url_array['status'];
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
            $dynamic_url = str_replace('//', '/', $this->classurl . $id . '/' . $table . '/' . $switchstring . '/status/' . $v);
            $out .= ' <a href="' . URL . $dynamic_url . '" ' . $highlight . ' >' . $v . '</a> | ';
        }
        if ($status_exists) {
            $highlight = '';
        } else {
            $highlight = 'style="background-color:yellow; padding:5px; display:inline-block;"';
        }

        $out .= ' <a href="' . URL . $this->classurl . '/' . $id . '/' . $table . '/" ' . $highlight . ' >all</a>';
        $out .= '</div></div>';

        if ($this->previously_modified_flag) {
            $out .= '<div style="width:100%; background-color:green; color: white; padding:3px;float:left;">Has been modified previously</div>';
        }

        return $out;
    }

    public function batch_process() {
        echo 'go again';

        $listparam['table'] = array(':table' => 'desc_batch_test');
        $listparam['columns'] = array(':t1' => 'id');
        $listparam['pdo_style'] = 'FETCH_COLUMN';
        $listsql = 'select :t1 from :table;';
        //$listsql = 'select id from users where delete_date = 0;' ;
        $list = new \libs\nate\CRUD\read_db($this->db, $listsql, $listparam);

        $array = array();
        // the list will be broken into chuckes of 10,000 records per chunk....after each chunk the batch counts re-start
        $array = array_chunk($list->result, 10000);
        $count = count($array);

        $cleanup = new \libs\nate\cleanup();
        $difference = new \libs\nate\diff\difference();
        $warn = new \libs\nate\resources\S1_WARN();

        $dbh = new $this->db;
        for($i = 0; $i < $count; $i++) {
            $chunk = array();
            $washchunk = array();
            $fetchparam['table'] = $listparam['table'];
            $fetchparam['columns'] = array(':f1' => 'id', ':f2' => 'name',':f3' => 'description',':f4' => 'short' );
            $swap = array();
                foreach($array[$i] as $k => $v){
                    $swap[$k]['id'] = $v;
                }
            $fetchparam['fields'] = $array[$i];
            
            $fetchsql = 'select :f1, :f2, :f3, :f4 from :table where :f1 IN($array)';
            $chunkobj = new \libs\nate\CRUD\insert_update_db($this->db, $fetchsql, $fetchparam);
            $chunk = $chunkobj->result;
            $chunkcount = count($chunk);
            $washchunk = array();
                for($j = 0; $j < $chunkcount; $j++){

                    $id = $chunk[$j]['id'];
                    $washchunk[$id]['$id'] = $id;
                    $washchunk[$id]['flag_description'] = $cleanup->washall($chunk[$j]['description']); 
                    $washchunk[$id]['flag_short'] = $cleanup->washall($chunk[$j]['short']); 
                    $washchunk[$id]['flag_name'] = $cleanup->washall($chunk[$j]['name']); 
                    $washchunk[$id]['warn_desc'] = $warn->washall($chunk[$j]['description']);
                    
                    // echo $id . strlen($washchunk[$id]['warn_desc']) . '<br/>';
                    $washchunk[$id]['warn_short'] = $warn->washall($chunk[$j]['short']);
                    similar_text(str_replace (" ", "",$washchunk[$id]['flag_description']), str_replace(" ", "",$chunk[$j]['description']),$percent);
                    $washchunk[$id]['desc_sim'] = number_format($percent, 2);
                    similar_text(str_replace (" ", "",$washchunk[$id]['flag_short']), str_replace(" ", "",$chunk[$j]['short']),$percent);
                    $washchunk[$id]['short_sim'] = number_format($percent, 2);
                    
                    $shortie = false;
                    
                    if(strpos($washchunk[$id]['warn_desc'], 'id="shortie"') > 0 || strpos($washchunk[$id]['warn_short'], 'id="shortie"') > 0 ){
                        $shortie = true;
                    }
                    
                    // logic check for status determination
                    
                    if( (($washchunk[$id]['short_sim'] < 95 && $washchunk[$id]['short_sim'] != 0 ) ||  
                        ($washchunk[$id]['desc_sim'] < 95 && $washchunk[$id]['desc_sim'] != 0 )) ){$status = "check";}
                    elseif (($washchunk[$id]['short_sim'] == 0 && $washchunk[$id]['desc_sim'] == 0)) { $status  = "no-change"; } else { $status = "auto-fixed";};
                    if(strlen($washchunk[$id]['warn_desc']) > 0 || strlen($washchunk[$id]['warn_short']) > 0 ) { 
                        $status = "warn_" . $status;
                    }
                    
                    if($shortie) {
                        $status = "shortie_" . $status;
                    }
                    
                    $washchunk[$id]['status'] = $status;
                }
                

            $updateparam['table'] = $listparam['table'];
            $updateparam['columns'] = array(
                ':f4' => 'id',
                ':f5' => 'flag_description',':f6' => 'flag_short',
                ':f7' => 'flag_name',':f8' => 'warn_desc',':f9' => 'warn_short',
                ':f10' => 'desc_sim',':f11' => 'short_sim',
                ':f12' => 'status');
            $updatesql = 'insert into :table('
                    . 'id,flag_description,flag_short,flag_name,'
                    . 'warn_desc,warn_short,desc_sim,short_sim,status) '
                    . 'values($array) '
                    . 'ON DUPLICATE KEY UPDATE '
                    . ':f5=VALUES(:f5),:f6=VALUES(:f6), '
                    . ':f7=VALUES(:f7), :f8=VALUES(:f8),:f9=VALUES(:f9), '
                    . ':f10=VALUES(:f10), :f11=VALUES(:f11), :f12=VALUES(:f12);';
            $updateparam['fields'] = $washchunk;
            // how many records are processed in a batch?
            $updateparam['batch'] = 50;
            $updateparam['display'] = 'percent';
            // how many batches will be processed before an on-screen update occures (costs 1 second each update)
            $updateparam['display_multiple_of'] = 5;
            $batchupdate = new \libs\nate\CRUD\batch_in_up_db($this->db, $updatesql, $updateparam);
            }
        }




        /*


          $param['table'] = array(':table' => 'users');
          $param['columns'] = array(':cola' => 'password', ':colb' => 'login');
          $sql = 'insert into :table(:cola,:colb) values($array) ON DUPLICATE KEY UPDATE id=VALUES(:cola),:colb = VALUES(:colb);';
          $param['fields'] = array(
          array(':password' => md5('test'), ':login' => 'again'.rand()),
          array(':password' => md5('test'), ':login' => 'again'.rand()),
          array(':password' => md5('test'), ':login' => 'again'.rand()),
          array(':password' => md5('test'), ':login' => 'again'.rand()));
          $param['batch'] = 1;
          $param['display'] = 'dot';
          $param['display_multiple_of'] = 1;


          $a = new \libs\nate\CRUD\batch_in_up_db($this->db, $sql, $param);
         *
         * 
         */

        /*
         * inherits read_db
         * inherits update_insert_db
         * relies read_db's $param['fields']= array() to extract a list of key's to progress through the batch
         * required: $param['batch'] to be an integer to update flush periods
         * optional: $param['display'] if 'total' it will generate a running total list with flush
         * optional: $param['display'] if 'dot' it will simply create incremental periods with flush
         * optional: $param['display'] if 'percent' it will output a percentage with flush
         * optional: $param['display'] if null it may time-out depending on length of process.
         */

    public function get($var) {
        return $this->{$var};
    }

}
