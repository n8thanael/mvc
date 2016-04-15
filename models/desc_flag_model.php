<?php

/*
 *   process_table_flaging() primary method recieves table parameters to flag and output to a table all flagged names, descriptions and short descriptions.
 */

class desc_flag_Model extends model {

    // fetchrecord is pushed the output of fetch() then the controller pulls this information back through public function get();
    private $fetchrecord_raw = array();
    private $current = '';
    private $fetchrecord = array(
        "id" => "",
        "style_id" => "",
        "brand" => "",
        "style" => "",
        "flag_name" => "",
        "flag_description" => "",
        "flag_short" => "",
        "dept" => "",
        "class" => "",
        "flag" => "",
        "picture_id" => "",
        "price" => "",
        "url" => "",
        "status" => "");
    private $fetchlist = array();

    function __construct() {
        parent::__construct();
    }

    /*
     * retrives the database information for a given ID
     * sets the private property/array: $fetchrecord[] with that id's info from web database 
     */

    public function fetch_all($id,$table) {
        $dbh = $this->db->prepare(
                "SELECT * FROM ".$table." WHERE id = :id;"
        );
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute(array(':id' => $id));
        $this->fetchrecord_raw = $dbh->fetchAll();
        $this->fetchrecord['id'] = $this->fetchrecord_raw[0]["id"];
        $this->fetchrecord['style_id'] = $this->fetchrecord_raw[0]["style_id"];
        $this->fetchrecord['brand'] = $this->fetchrecord_raw[0]["brand"];
        $this->fetchrecord['style'] = $this->fetchrecord_raw[0]["style"];
        $this->fetchrecord['flag_name'] = $this->fetchrecord_raw[0]["name"];
        $this->fetchrecord['flag_description'] = $this->fetchrecord_raw[0]["description"];
        $this->fetchrecord['flag_short'] = $this->fetchrecord_raw[0]["short"];
        $this->fetchrecord['dept'] = $this->fetchrecord_raw[0]["dept"];
        $this->fetchrecord['class'] = $this->fetchrecord_raw[0]["class"];
        $this->fetchrecord['OF19'] = $this->fetchrecord_raw[0]["OF19"];
        $this->fetchrecord['picture_id'] = $this->fetchrecord_raw[0]["picture_id"];
        $this->fetchrecord['price'] = $this->fetchrecord_raw[0]["price"];
        $this->fetchrecord['store_id'] = $this->fetchrecord_raw[0]["store_id"];
        $this->fetchrecord['inv_num'] = $this->fetchrecord_raw[0]["inv_num"];
        $this->fetchrecord['url'] = 'http://www.woodburyoutfitters.com/shop/-' . $this->fetchrecord_raw[0]["style_id"];
        return $this->fetchrecord;
    }

    /*
     * this works within $this->insert_into_flag_db
     * It fetches raw records and strips their tags
     * it then fills the new DB with the tag-less results as a base-line for corrections
     * it is merged to the array within $this->insert_into_flag_db
     */

    public function prep_orig_db() {
        $array = array();
        $array['name'] = htmlentities(strip_tags($this->fetchrecord_raw[0]['name']));
        $array['description'] = htmlentities(strip_tags($this->fetchrecord_raw[0]['description']));
        $array['short'] = htmlentities(strip_tags($this->fetchrecord_raw[0]['short']));
        return $array;
    }

    /*
     * Does a basic insert of the array given into the new web_flagged_desc table
     * input array must be formatted similar to 
     */

    private function insert_into_flag_db($array, $outputtable) {
        $array['web_id'] = $array['id'];
        unset($array['id']);
        $date = date('Y-m-d H:i:s');
        $additional_values = ['flag' => 'found', 'status' => 'check', 'start_date' => $date];
        $array = array_merge($array, $additional_values);
        $array = array_merge($array, $this->prep_orig_db());
        $fields = array_keys($array);
        $values = array_values($array);
        $fieldlist = implode(',', $fields);
        $qmarks = str_repeat("?,", count($fields) - 1);
        $sql = "INSERT into " . $outputtable . "($fieldlist) values(${qmarks}?)";
        $dbh = $this->db->prepare($sql);
        $dbh->execute($values);
        return $array;
    }

    /*
     * the grabs a specific #id from the database
     * pulls only name, description, short
     * designed to prep that record to be checked by check_fetched
     */

    private function fetch_to_check($id,$table) {
        $array = array();
        $dbh = $this->db->prepare(
                "SELECT name,description,short FROM ".$table." WHERE id = :id;"
        );
        $output = array();
        $dbh->execute(array(':id' => $id));
        //////////////////////////////////////////print_r($dbh->errorInfo());
        $output = $dbh->fetchAll();
        $array['flag_name'] = $output[0]['name'];
        $array['flag_description'] = $output[0]['description'];
        $array['flag_short'] = $output[0]['short'];
        return $array;
    }

    /* sends the description array to group of classes structured under the check parent class
     * if any matches are made, it will return a modified array that changed the original description
     * if there are no matches, it will return false;
     */ 

    private function check_fetched($array) {
        $check = new libs\nate\check();
        $array = $check->flagall($array);
        if ($array !== false) {
            return $array;
        } else {
            // echo "No Match<br>";
        }
    }

    /*
     * This method recieves an input file name array[0] and an output file name array[1]
     * success / fail output is handled within the class
     */

    public function prep_file($array) {
        $prepped = new libs\nate\prep($array);
    }

    /*
     * Simple query looks for a result on another table
     * returns true or false if it finds the matching id.
     */

    private function check_picture_match($id, $table, $pictable) {
        $dbh = $this->db->prepare(
                "select w2.picture_id as picture from " . $table . " as t1 LEFT JOIN " . $pictable . " as t2 on t1.style_id = t2.style_id WHERE t1.id = $id"
        );
        $dbh->execute();
        $array = $dbh->fetch();
        if ($array[0] != NULL) {
            return true;
        } else {
            return false;
        }
    }

    /* primary method
     * is called to pull an ID list
     * runs the entire list with fetch_to_check, which gets an array
     * check_fetched sents array off to a library of classes
     * classes return highlighted text showing a regex method found something
     * if regex finds nothing, the classes return false
     */

    public function process_table_flagging($param) {
        $table = $param[0];
        $outputtable = $param[1];
        $error = false;
        if (count($param) == 2) {
            $dbh = $this->db->prepare("SELECT id FROM " . $table . " where picture_id is NOT NULL ORDER BY id asc;");
        } elseif (count($param) == 4) {
            $limita = $param[2];
            $limitb = $param[3];
            $dbh = $this->db->prepare("SELECT id FROM " . $table . " where picture_id is NOT NULL ORDER BY id asc limit " . $limita . "," . $limitb . ";");
        } elseif (count($param) == 5) {
            $pictable = $param[4];
            $dbh = $this->db->prepare("SELECT id FROM " . $table . " ORDER BY id asc limit " . $limita . "," . $limitb . ";");
        } else {
            echo "<p> Function requires a list of parameters after the URL: <Br/>"
            . "fetch_idlist/table/outputtable [ /limita/limitb/pictable ]:optional <Br/></p>";
            $error = true;
        }
        if (!$error) {
            $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $dbh->execute();
            $idlist = $dbh->fetchAll();

            function myFlush() {
                echo(str_repeat(' ', 256));
                if (@ob_get_contents()) {
                    @ob_end_flush();
                }
                flush();
            }

            function outputProgress($j, $i,$current) {
                if ($j == 100) {
                    echo "<span style='display:block; float:left; width:100%; padding:5px;'>" . $i . "  |  <i>".$current."</i></span>";
                    $current = '';
                    myFlush();
                    sleep(1);
                }
                    return $current;
            }

            $i = 0;
            $j = 0;
            foreach ($idlist as $ids) {
                set_time_limit(0);
                $this->current .= "," . $idlist[$i]['id'];
                $this->current = outputProgress($j, $i, $this->current);
                $skip = false;

                // possible to check another table for a picture match if picture id's are somewhere else...
                /*
                if (isset($pictable)) {
                    if (!$this->check_picture_match($idlist[$i]['id'], $table, $pictable)) {
                        $skip = true;
                    }
                }
                 * 
                 */

                if (!$skip) {
                    $checkthis = $this->fetch_to_check($idlist[$i]['id'],$table);
                    $checked_array = $this->check_fetched($checkthis);
                    if (is_array($checked_array)) {
                        $append_array = $this->fetch_all($idlist[$i]['id'],$table);
                        $new_write_ready_array = array_replace($append_array, $checked_array);
                        $new_write_ready_array = $this->insert_into_flag_db($new_write_ready_array, $outputtable);
                        //echo $new_write_ready_array['name'] . '<br>';
                    }
                }
                $i++;
                if ($j == 100) {
                    $j = 1;
                } else {
                    $j++;
                }
            };
        } // end of if(!error)
    }

    public function processbatch($record) {


        $i = 0;
        while ($id <= $stopat) {

            $now = $dif - ($stopat - $id);
            $array = array($id, $table, $stopat);
            $this->process($array);

            $id++;
        }
    }

    public function get($var) {
        return $this->{$var};
    }

}
