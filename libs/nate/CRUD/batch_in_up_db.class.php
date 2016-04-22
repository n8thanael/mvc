<?php

namespace libs\nate\CRUD;

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

class batch_in_up_db extends insert_update_db {

    private $current;
    private $batch;
    private $chunksoffields;
    private $display;
    private $display_multiple_of;

    // sets up in-class parameters and routes to correct methods
    function __construct($dbh, $sql = false, $param = false, $debug = false) {
        read_db::__construct($dbh, $sql, $param, $debug);
        if ($this->multi_dimensional_trigger) {

            (isset($param['batch'])) && (is_int($param['batch'])) ?
                            $this->batch = $param['batch'] : $this->batch = false;

            if (isset($param['display']) && ($param['display'] ==  'echo' || 'dot' || 'pre' || 'percent' )
                    && is_int($param['display_multiple_of'])) {
                $this->display_multiple_of = $param['display_multiple_of'];
                $this->display = $param['display'];
            } else {
                $this->display_multiple_of = 50;
                $this->display = 'percent';
            }

            // this class may have been called without "batch" - if so...
            // ...load the parent__construct and give it 
            if (!$this->batch) {
                parent::__construct($dbh, $sql, $param, $debug);
            } else {
                // check to see if the query is formmatted, correclty prior to proceeding
                // else error & die;
                if ((stripos(strtoupper($this->sql), 'INSERT') === 0) || (
                        stripos(strtoupper($this->sql), 'ON DUPLICATE KEY UPDATE') > 0 ) || (
                        stripos(strtoupper($this->sql), 'VALUES($ARRAY)') > 0 )) {
                    $this->str_replace_columns_tables();
                    // modified version of prepare_sql_values_for_insert_update() in insert_update_db
                    $this->batch_sql_values_for_insert_update();
                    echo '<p>so far so good.</p>';
                } else {
                    if ($this->debug) {
                        $this->echo_in_pre($this->dbh->errorInfo());
                        echo 'Sql Query: <u>"' . $this->sql . '"</u> Query must have a specific syntax.  Please read class documentation.';
                        $this->error = true;
                        die;
                    }
                }
            }
        } else {
            if ($this->debug) {
                $this->echo_in_pre($this->dbh->errorInfo());
                echo 'There has been an class inheritance error.';
                $this->error = true;
                die;
            }
        }
    }

    private function batch_sql_values_for_insert_update() {
        // due to batch loop we need to store the original sql statement
        $origsql = $this->sql;

        // batch processing measurements for loop setup and flush display
        $total = count($this->fields);
        $batchlimit = $this->batch;
        $batches = intval($total / $batchlimit);
        $remainder = $total % $this->batch;
        $remainder > 0 ? $batches++ : null;
        $currentnum = 0;
        $currentbatch = 1;
        $currentflush = 1;
        $flush_count = 0;
        
        // we may need to let the user know they have an incorrect batch value
        if ($batches == 0 && $this->debug) {
            echo 'Ran as a single batch, since \$param[batch] > count(\$param[$fields])';
            $batches = 1;
        }

        // separate the fields by batchlimit into chunks
        $this->chunksoffields = array_chunk($this->fields, $batchlimit);

        // process by batch sizes
        for ($k = 0; $k < $batches; $k++) {

            // setup values string & field array
            $sqlvalues = "VALUES";
            $this->sqlfields = array();

            // looping through this batch
            $j = count($this->chunksoffields[$k]);
            for ($i = 0; $i < $j; $i++) {

                // easy counter for how many keys are in this batch.
                $l = 0;
                $sqlvalues .= '(';

                // loops for every value inside this chunk
                foreach ($this->chunksoffields[$k][$i] as $v) {
                    array_push($this->sqlfields, $v);
                    // increment key counter.
                    $l++;
                };
                // build the string that will replace VALUES($ARRAY) from $parram['sql']
                $sqlvalues .= substr(str_repeat('?,', $l), 0, -1);
                $sqlvalues .= '),';
                
                // increase value for flush-display
                $currentnum++;
            }
            $sqlvalues = substr($sqlvalues, 0, -1);

            
            // looks for string values($array) to replace it with $sqlvalues
            $regex = '/VALUES\(\$[Aa]{1}rray\)|[Vv]{1}alues\(\$[Aa]{1}rray\)/';
            $this->sql = preg_replace($regex, $sqlvalues, $origsql);

            // inherited from parent: insert_update_db;
            $this->sql_query();

            // display flush counter for long processes if desired
            set_time_limit(0);
            $flush_display = array(
                'batchnum' => $k,
                'batchlimit' => $batchlimit,
                '$displayamt' => $this->display_multiple_of,
                'total' => $total,
                'batches' => $batches,
                'currentnum' => $currentnum,
                'currentbatch' => $currentbatch,
                'currentflush' => $currentflush
            );
            
            // it's possible to modify the number of times the flush will process
            if ($currentbatch % $this->display_multiple_of == 0) {
                $this->output_flush_progress($flush_display);
                //increase value for flush-display
                $currentflush++;
            }
            //increase value for flush-display
            $currentbatch++;
        }
    }

    // is set by the user to specify which display they'd like to use.  Default: percent;
    private function output_flush_echo($input) {
        echo '<span style="display:block; float:left; width:100%; padding:5px;">'
        . ' ' . $input['currentnum'] . ' of <i> ' . $input['total'] . '</i></span>';
    }
    
    private function output_flush_dot($input) {
        echo str_repeat('.',intval($input['currentflush']));
    }
    
    private function output_flush_percent($input) {
        echo intval(($input['currentnum']/$input['total']) * 100) . '%<br>';
    }
    
    private function output_flush_pre($input) {
        echo '<pre>';
        print_r($input);
        echo '</pre>';
    }

    private function output_flush_progress($flush_display) {
        switch ($this->display) {
            case 'pre':
                $this->output_flush_pre($flush_display);
                break;
            case 'echo':
                $this->output_flush_echo($flush_display);
                break;
            case 'dot':
                $this->output_flush_dot($flush_display);
                break;
            case 'percent':
                $this->output_flush_percent($flush_display);
                break;
        }
        if (@ob_get_contents()) {
            @ob_end_flush();
        }
        flush();
        sleep(1);
    }

}
