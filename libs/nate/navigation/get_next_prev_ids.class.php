<?php

namespace libs\nate\navigation;

/*
 * receives database information and an object to connet with
 * runs a query and figures out what is the next previous id's based on a short 'andwhereclasuse'
 * returns a string of HTML that links to the next/prev links
 * does not verify what is sent to search in the DB
 * REFACTOR!!!! -->  sql may be possible until the strings are prepared as array sets like they should be
 * if there is an SQL error because of the $andwhereclause (if it returns 0) it will simply cut that clause and re-run the sql query
 */

class get_next_prev_ids {

    private $prevnext;  // simple html string

    public function __construct($id, $table, $andwhereclause, $classurl, $extraurlsort, $dbh) {
        $nextprevarray = $this->get_next_prev_ids($table, $id, $andwhereclause, $dbh);
        $this->prevnext = $this->render_prev_next($table, $id, $classurl, $nextprevarray, $extraurlsort);

        echo '<pre>';
        print_r(array($id, $table, $andwhereclause, $classurl, $extraurlsort, $dbh));
        echo '</pre>';
    }

    public function get() {
        return $this->prevnext;
    }

    public function get_next_prev_ids($table, $id, $andwhereclause, $dbh) {
        $out = array();
        $nextprevarray = array();

        // select the next and previous id's - reguardless of flag
        $sql = 'SELECT * FROM (select max(id) as id '
                . ' FROM ' . $table . ' WHERE id < ' . $id . ' ' . $andwhereclause . ' ORDER BY id DESC LIMIT 1) as A '
                . 'UNION (select min(id) as id FROM ' . $table . ' WHERE id > ' . $id . ' ' . $andwhereclause . ' ORDER BY id ASC LIMIT 1);';
        $dbhobj = $dbh->prepare($sql);
        $dbhobj->setFetchMode(\PDO::FETCH_ASSOC);
        $dbhobj->execute();
        $temp = $dbhobj->fetchAll();
        
        // Is there an errror in the first query?
        // Is the result of the query NULL?
        // ....fine - cut the $andwhereclause out of the query
        echo $sql;
        echo count($temp[0]);
        $errorarray = $dbhobj->errorInfo();
        print_r($errorarray);
        print_r($temp);
        if (($temp[0]['id'] == NULL && count($temp) == 1) || $errorarray[1] !== NULL) {
            $sql = 'SELECT * FROM (select max(id) as id '
                    . ' FROM ' . $table . ' WHERE id < ' . $id . ' ORDER BY id DESC LIMIT 1) as A '
                    . 'UNION (select min(id) as id FROM ' . $table . ' WHERE id > ' . $id . ' ORDER BY id ASC LIMIT 1);';
            $dbhobj = $dbh->prepare($sql);
            $dbhobj->setFetchMode(\PDO::FETCH_ASSOC);
            $dbhobj->execute();
            $temp = $dbhobj->fetchAll();
        }

        if (isset($temp[0]) || isset($temp[1])) {

            if ($temp[0]['id'] != NULL) {
                $nextprevarray['prev'] = $temp[0]['id'];
            }
            if (isset($temp[1])) {
                if ($temp[1]['id'] != NULL) {
                    $nextprevarray['next'] = $temp[1]['id'];
                }
            }
        }

        return $nextprevarray;
    }

    // recieves an item number and generates nav bar PREV | NEXT if there are more items.
    public function render_prev_next($table, $id, $classurl, $nextprevarray, $extraurlsort) {
        if (substr($extraurlsort, 1) != '/') {
            $extraurlsort = '/' . $extraurlsort;
        }
        $out = '';
        if (isset($nextprevarray['prev'])) {
            $out .= '<a href="' . URL . $classurl . $nextprevarray['prev'] . '/' . $table . $extraurlsort . '">prev</a> |';
        }
        if (isset($nextprevarray['next'])) {
            $out .= ' <a href="' . URL . $classurl . $nextprevarray['next'] . '/' . $table . $extraurlsort . '">next</a>';
        }
        return $out;
    }

}
