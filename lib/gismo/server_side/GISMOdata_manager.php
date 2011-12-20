<?php
class GISMOdata_manager {
    // constants
    const run_inf = "02:00:00";
    const run_sup = "04:00:00";
    const hours_from_last_run = 12;
    const devel_mode = false;
    const limit_records = 200000;
    
    // fields
    protected $now_time;
    protected $now_hms;
    protected $manual;
    
    
    // constructor
    public function __construct($manual = false) {
        $this->now_time = time();
        $this->now_hms = date("H:i:s", $this->now_time);
        $this->manual = $manual;    
    }
    
    // sync data
    // This method ensures that data is syncronized only if:
    // 1) Now time is between 'run_inf' and 'run_sup'
    // 2) Data hasn't been syncronized in the last 'hours_from_last_run' hours
    public function sync_data() {
        global $CFG;
        // Adjust some php variables to the execution of this script
        if ($this->manual !== true) {
            @ini_set("max_execution_time","7200");
        }
        if (function_exists("raise_memory_limit")) {
            raise_memory_limit("1024M");
        }
        // DEBUG: MEMORY USAGE
        if (self::devel_mode) {
            echo "<br>MEMORY USAGE BEFORE: " . number_format(memory_get_usage(), 0, ".", "'");
        }
        
        // result
        $result = true;
        
        // table prefix
        $p = $CFG->prefix;
        
        // last export time and max log id
        $last_export_time = get_record("gismo_config", "name", "last_export_time");
        $last_export_max_log_id = get_record("gismo_config", "name", "last_export_max_log_id");
        if ($last_export_time === FALSE OR $last_export_max_log_id === FALSE) {
            return $this->return_error("Cannot extract last export time / last export max log id.", __FILE__, __FUNCTION__, __LINE__);     
        }
        
        // max log id (value to be set after export)
        $max_log_id = get_records("log", null, null, "id DESC", "id", 0, 1);
        if (!(is_array($max_log_id) AND count($max_log_id) === 1)) {
            return $this->return_error("Cannot extract max log id.", __FILE__, __FUNCTION__, __LINE__);    
        }
        $max_log_id = intval(array_pop($max_log_id)->id);
        
        // sync ???                    
        if (self::devel_mode === true OR
            ($this->now_time - intval($last_export_time->value) > self::hours_from_last_run * 3600 AND 
            (($this->now_hms >= self::run_inf AND $this->now_hms <= self::run_sup) OR $this->manual === true))) {
            // lock gismo tables
            // TODO
            
            /*
             * RESET IF DEVEL MODE
             */ 
            if (self::devel_mode === true) {
                // reset
                $this->devel_mode_reset();
                // update values
                $last_export_time->value = 0; 
                $last_export_max_log_id->value = 0;
            }
            
            /*
             * SYNC DATA
             */
       
            // set the filter (get newer data only)
            $filter = $p . "log.id > " . intval($last_export_max_log_id->value) . " AND " . $p . "log.id <= " . $max_log_id;
            if (!empty($CFG->loglifetime)) {    // !!! REMEBER: 0 is considered empty
                $filter = $filter . " AND " . $p . "log.time >= " . ($this->now_time - ($CFG->loglifetime * 86400));
            }

            /*
             * SYNC gismo_student_login table (GISMO Students Actions)
             */
            $offset = 0;
            $loop = true;

            $qry = "SELECT id, DATE( FROM_UNIXTIME( time ) ) AS date_val, time, COUNT( id ) AS count, userid, course ".
                    " FROM " . $p . "log WHERE $filter GROUP BY course, userid, date_val ".
                    " ORDER BY course, userid, date_val LIMIT %u OFFSET %u";

            // loop
            while ($loop === true) {
                $logins = get_records_sql(sprintf($qry, self::limit_records, $offset));

                // DEBUG: MEMORY USAGE
                if (self::devel_mode) {
                    echo "<br>MEMORY USAGE (MIDDLE GISMO STUDENTS LOGIN): " . number_format(memory_get_usage(), 0, ".", "'");
                }

                // add entries
                if (is_array($logins) AND count($logins) > 0) {
                    if (count($logins) < self::limit_records) {
                        $loop = false;
                    }
                    foreach($logins as $key => $login) {
                        $gsll_entry = new stdClass();
                        $gsll_entry->course_log = $login->course;
                        $gsll_entry->userid_log = $login->userid;
                        $gsll_entry->count_log = $login->count;
                        $gsll_entry->date_log = $login->date_val;
                        $gsll_entry->time_log = $login->time;
                        if (insert_record("gismo_student_login", $gsll_entry, true, "id_log") === FALSE) {
                            return $this->return_error("Cannot add entry in gismo_student_login table.", __FILE__, __FUNCTION__, __LINE__);
                        }
                        unset($gsll_entry, $logins[$key]);
                    }
                    unset($logins);
                } else {
                    $loop = false;
                }

                // increment offset
                $offset += self::limit_records;
            }

            // DEBUG: MEMORY USAGE
            if (self::devel_mode) {
                echo "<br>MEMORY USAGE (AFTER GISMO STUDENTS LOGIN): " . number_format(memory_get_usage(), 0, ".", "'");
            }

            /*
             * SYNC gismo_res_access table (GISMO Resources Access Overview)
             */

            $offset = 0;
            $loop = true;

            // retrieve accesses on resources
            $qry = "SELECT ".$p."log.id, DATE(FROM_UNIXTIME(".$p."log.time)) AS date_val, " . $p . "log.time AS time_rac, ".
                     $p."log.course AS course, ".$p."log.userid AS userid, ".
                     $p."course_modules.id AS res_id, COUNT(".$p."course_modules.instance) AS count FROM ".$p."log, ".
                     $p."course_modules WHERE ".$p."course_modules.id = ".$p."log.cmid AND ".
                        "((".$p."log.action = 'view' AND ".$p."log.module IN ('resource', 'book', 'glossary')) OR ".$p."log.action = 'view discussion' AND ".$p."log.module = 'forum') ".
                        "AND $filter GROUP BY course, res_id, date_val, userid LIMIT %u OFFSET %u";

            // loop
            while ($loop === true) {
                $actions = get_records_sql(sprintf($qry, self::limit_records, $offset));

                // DEBUG: MEMORY USAGE
                if (self::devel_mode) {
                    echo "<br>MEMORY USAGE (MIDDLE ACCESSES ON RESOURCES): " . number_format(memory_get_usage(), 0, ".", "'");
                }

                // add entries
                if (is_array($actions) AND count($actions) > 0) {
                    if (count($actions) < self::limit_records) {
                        $loop = false;
                    }
                    foreach($actions as $key => $action) {
                        $gra_entry = new stdClass();
                        $gra_entry->course_rac = $action->course;
                        $gra_entry->idresource_rac = $action->res_id;
                        $gra_entry->userid_rac = $action->userid;
                        $gra_entry->date_rac = $action->date_val;
                        $gra_entry->time_rac = $action->time_rac;
                        $gra_entry->count_rac = $action->count;
                        if (insert_record("gismo_res_access", $gra_entry, true, "id_rac") === FALSE) {
                            return $this->return_error("Cannot add entry in gismo_res_access table.", __FILE__, __FUNCTION__, __LINE__);
                        }
                        unset($gra_entry, $actions[$key]);
                    }
                    unset($actions);
                } else {
                    $loop = false;
                }

                // increment offset
                $offset += self::limit_records;
            }

            // DEBUG: MEMORY USAGE
            if (self::devel_mode) {
                echo "<br>MEMORY USAGE (AFTER ACCESSES ON RESOURCES): " . number_format(memory_get_usage(), 0, ".", "'");
            }

            /*
             * SYNC gismo_student_res_access table (GISMO Students Access On Resources)
             */

            $offset = 0;
            $loop = true;

            // retrieve users accesses on resources, keep trace of resource id (COUNT MUST BE DONE IN JS)
            $qry = "SELECT ".$p."log.id, DATE(FROM_UNIXTIME(".$p."log.time)) AS date_val, ".$p."log.time AS time_sra, COUNT(".$p.
                   "log.userid) AS count, ".$p."log.course AS course, ".$p."log.userid AS userid, ".$p.
                   "course_modules.id AS resid FROM ".$p."log, ".$p."course_modules WHERE ".$p.
                   "course_modules.id = ".$p."log.cmid AND ".
                   "((".$p."log.action = 'view' AND ".$p."log.module IN ('resource', 'book', 'glossary')) OR ".$p."log.action = 'view discussion' AND ".$p."log.module = 'forum') ".
                   "AND $filter GROUP BY course, userid, date_val, resid LIMIT %u OFFSET %u";

            // loop
            while ($loop === true) {
                $resource_access = get_records_sql(sprintf($qry, self::limit_records, $offset));

                // DEBUG: MEMORY USAGE
                if (self::devel_mode) {
                    echo "<br>MEMORY USAGE (MIDDLE STUDENTS RES ACCESS): " . number_format(memory_get_usage(), 0, ".", "'");
                }

                // add entries
                if (is_array($resource_access) AND count($resource_access) > 0) {
                    if (count($resource_access) < self::limit_records) {
                        $loop = false;
                    }
                    foreach($resource_access as $key => $res_acc) {
                        $gsra_entry = new stdClass();
                        $gsra_entry->userid_sra = $res_acc->userid;
                        $gsra_entry->resid_sra = $res_acc->resid;
                        $gsra_entry->course_sra = $res_acc->course;
                        $gsra_entry->count_sra = $res_acc->count;
                        $gsra_entry->date_sra = $res_acc->date_val;
                        $gsra_entry->time_sra = $res_acc->time_sra;
                        if (insert_record("gismo_student_res_access", $gsra_entry, true, "id_sra") === FALSE) {
                            return $this->return_error("Cannot add entry in gismo_student_res_access table.", __FILE__, __FUNCTION__, __LINE__);
                        }
                        unset($gsra_entry, $resource_access[$key]);
                    }
                    unset($resource_access);
                } else {
                    $loop = false;
                }

                // increment offset
                $offset += self::limit_records;
            }

            // DEBUG: MEMORY USAGE
            if (self::devel_mode) {
                echo "<br>MEMORY USAGE (AFTER STUDENTS RES ACCESS): " . number_format(memory_get_usage(), 0, ".", "'");
                echo "<br>----------<br>";
            }
                //}
            //}
            
            // update export time value and max log id
            $last_export_time->value = $this->now_time;
            if (update_record("gismo_config", $last_export_time) === FALSE) {
                return $this->return_error("Cannot update last export time value.", __FILE__, __FUNCTION__, __LINE__);    
            }
            $last_export_max_log_id->value = $max_log_id;
            if (update_record("gismo_config", $last_export_max_log_id) === FALSE) {
                return $this->return_error("Cannot update last export max log id value.", __FILE__, __FUNCTION__, __LINE__);    
            }            
            
            // unlock gismo tables
            // TODO        
        } else {
            $result = "It's not time to sync data now!";    
        }
        
        // DEBUG: MEMORY USAGE
        if (self::devel_mode) { 
            echo "<br>MEMORY USAGE AFTER: " . number_format(memory_get_usage(), 0, ".", "'") . "<br>";
        }
        
        // return result
        return $result;    
    }
    
    // purge data
    // This method removes old data according to the moodle log life time
    public function purge_data() {
        global $CFG;

        // result
        $result = true;
        
        // delete old logs
        if (!empty($CFG->loglifetime)) {    // !!! REMEBER: 0 is considered empty
            // log life time
            $loglifetime = $this->now_time - ($CFG->loglifetime * 86400);
            
            // purge queries
            $queries = array("gismo_student_login" => "time_log < " . $loglifetime,
                             "gismo_res_access" => "time_rac < " . $loglifetime,
                             "gismo_student_res_access" => "time_sra < " . $loglifetime);
            
            // execute queries
            if (is_array($queries) AND count($queries) > 0) {
                foreach ($queries as $table => $select) {
                    $check = delete_records_select($table, $select);
                    if ($check === FALSE) {
                        return $this->return_error("Error while purging old logs.", __FILE__, __FUNCTION__, __LINE__);
                    }    
                }
            }
        } else {
            $result = "Nothing to be purged, logs never expire!";    
        }
        
        // ok
        return $result;    
    }
    
    // devel method
    // This methods does as follows:
    // 1) Reset config parameters that have to do with sync
    // 2) Empty gismo tables (data)
    public function devel_mode_reset() {
        global $CFG;
        
        // delete data
        delete_records("gismo_res_access");
        delete_records("gismo_student_login");
        delete_records("gismo_student_res_access");
        
        // reset last export time
        $last_export_time = get_record("gismo_config", "name", "last_export_time");
        $last_export_time->value = 0;
        update_record("gismo_config", $last_export_time);
        
        // reset export max log id
        $last_export_max_log_id = get_record("gismo_config", "name", "last_export_max_log_id");
        $last_export_max_log_id->value = 0;
        update_record("gismo_config", $last_export_max_log_id);
        
        // ok
        return true;    
    }
    
    // this method return a error message
    protected function return_error($msg, $file, $function, $line) {
        return "Error: " . strtolower($msg) . sprintf(" [ File: '%s',  Function: '%s',  Line: '%s' ]", $file, $function, $line);   
    }
}
?>
