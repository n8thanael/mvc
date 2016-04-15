<?php

class showitem_Model extends model {

    // fetchrecord is pushed the output of fetch() then the controller pulls this information back through public function get();
    private $fetchrecord = array();
    private $comparerecord = array();
    private $fetchdesc = "";
    private $comparedesc = "";
    private $fetchminmax = array();
    private $itemdata = array();
    private $itemlist = array();
    private $item_nav = 0;
    private $compare_nav = 0;

    function __construct() {
        parent::__construct();
    }

    /*
     *  Fetches the original record from the default DB: item
     *  If there is a second parameter passed, it can also call a item to compare for display.
     */

    public function fetch($record) {

        $id = $record[0];
        


        if (count($record) == 2) {
            $table = $record[1];
            $dbh = $this->db->prepare(
                    "SELECT * FROM " . $table . " WHERE id = :id;"
            );


            $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $dbh->execute(array(
                ':id' => $id));
            // print_r($dbh->errorInfo());
            $this->comparerecord = $dbh->fetchAll();
            $this->comparedesc = $this->comparerecord[0]["description"];
            $this->item_nav = $this->compare_item_nav($record[0]);
        } else {
            $this->item_nav = $this->render_item_nav($record[0]);
        }



        $dbh = $this->db->prepare(
                "SELECT * FROM item WHERE id = :id;"
        );
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute(array(
            ':id' => $id));
        $this->fetchrecord = $dbh->fetchAll();
        $this->fetchdesc = $this->fetchrecord[0]["description"];
    }

    /*
     * fuction collects 2 paramaters from URL
     * 1: item code
     * 2: current or new table to create / modify
     * will look for the id within table: item
     * will run all of $cleanup() within libs\nate
     * will save modified record to current/new table
     */

    public function process($record) {
        $idcheck = array();
        if (count($record) == 3) {
            $this->fetch($record);

            $id = $record[0];
            $table = $record[1];
            $stopat = $record[2];

            $dbh = $this->db->prepare("describe " . $table);
            $dbh->execute();
            $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $tablecheck = $dbh->fetchAll();

            if (count($tablecheck) == 0) {
                $dbh = $this->db->prepare("CREATE table " . $table . " SELECT * from item limit 0;");
                $dbh->execute();
                //   print_r($dbh->errorInfo());
            }


            // does the table already have this ID?
            $dbh = $this->db->prepare(
                    'SELECT id from ' . $table . ' where id = :id;'
            );
            $dbh->execute(array(':id' => $this->fetchrecord[0]["id"]));
            $dbh->setFetchMode(PDO::FETCH_ASSOC);
            $idcheck = $dbh->fetchAll();


            // if id doesn't have the id...do an insert,
            if (empty($idcheck)) {
                $dbh = $this->db->prepare(
                        'INSERT into ' . $table . ' (id, sku, description) values(:id, :sku, :desc);'
                );
                // if id does have the id...do an update,
            } else {
                $dbh = $this->db->prepare(
                        'UPDATE ' . $table . ' SET sku = :sku, description = :desc where id = :id;'
                );
            }

            $cleanup = new libs\nate\cleanup();

            $dbh->execute(array(
                ':id' => $this->fetchrecord[0]["id"],
                ':sku' => $this->fetchrecord[0]["sku"],
                ':desc' => $cleanup->washall($this->fetchrecord[0]["description"])));

            return "done";
        } else {
            echo "params don't match:";
            var_dump($record);
        }
    }

    public function processbatch($record) {
        $id = $record[0];
        $table = $record[1];
        $stopat = $record[2];
        $dif = $stopat - $id;

        function myFlush() {
            echo(str_repeat(' ', 256));
            if (@ob_get_contents()) {
                @ob_end_flush();
            }
            flush();
        }

        function outputProgress($current, $total, $i) {
            if ($i == 100) {
                echo "<span style='display:block; float:left; width:100%; padding:5px;'>" . round($current / $total * 100) . "% | " . $current . " </span>";
                myFlush();
                sleep(1);
            }
        }

        $i = 0;
        while ($id <= $stopat) {
            set_time_limit(0);
            $now = $dif - ($stopat - $id);
            $array = array($id, $table, $stopat);
            $this->process($array);
            outputProgress($now, $dif, $i);
            $id++;
            if ($i == 100) {
                $i = 1;
            } else {
                $i++;
            }
        }
    }

    public function list_100($page) {
        if(empty($page[0])) {$page[0] = 0;};
        $pagelimitlow = intval($page[0]) * 100;
        $pagelimithi = $pagelimitlow + 100;
        $dbh = $this->db->prepare(
                "select I.id, I.sku, I.description, T.count from item as I join(select count(*) as count from item) as T limit {$pagelimitlow}, {$pagelimithi};"
        );
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $this->itemlist = $this->render_item_list($dbh->fetchAll());
        // print_r($dbh->errorInfo());
        // return $this->itemlist;
    }

    private function render_item_list($array) {
        $string = '<ul>';
        $i = 0;
        foreach ($array as $v) {
            $string .= '<li><i>';
            $string .= $array[$i]['id'] . '</i> | <a href="' . URL . 'showitem/fetch/' . $array[$i]['id'] . '/run_1">';
            $string .= $array[$i]['sku'] . '</a> | ';
            $string .= substr(strip_tags($array[$i]['description']), 0, 100) . '...';
            $string .= '</li>';
            $i++;
        }
        $string .= '</ul>';
        return $string;
    }

    public function prepfiles($names) {
        $prepfile = new libs\nate\resources\P1_PREP($names);
    }

    // recieves an item number and generates nav bar PREV | NEXT if there are more items.
    public function render_item_nav($number) {
        $number = intval($number);
        $out = '';
        $dbh = $this->db->prepare(
                "SELECT MIN(id) as min,MAX(id) as max from item"
        );
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $this->fetchminmax = $dbh->fetchAll();
        $min = intval($this->fetchminmax[0]["min"]);
        $max = intval($this->fetchminmax[0]["max"]);
        if ($min < $number) {
            $out = '<a href="' . URL . 'showitem/fetch/' . ($number - 1) . '">prev</a> |';
        }
        if ($max > $number) {
            $out .= ' <a href="' . URL . 'showitem/fetch/' . ($number + 1) . '">next</a>';
        }
        return $out;
    }

    // recieves an item number and generates nav bar PREV | NEXT if there are more items.
    public function compare_item_nav($number) {
        $number = intval($number);
        $out = '';
        $dbh = $this->db->prepare(
                "SELECT MIN(id) as min,MAX(id) as max from item"
        );
        $dbh->setFetchMode(PDO::FETCH_ASSOC);
        $dbh->execute();
        $this->fetchminmax = $dbh->fetchAll();
        $min = intval($this->fetchminmax[0]["min"]);
        $max = intval($this->fetchminmax[0]["max"]);
        if ($min < $number) {
            $out = '<a href="' . URL . 'showitem/fetch/' . ($number - 1) . '/run_1">prev</a> |';
        }
        if ($max > $number) {
            $out .= ' <a href="' . URL . 'showitem/fetch/' . ($number + 1) . '/run_1">next</a>';
        }
        return $out;
    }

    public function get($var) {
        return $this->{$var};
    }

}
