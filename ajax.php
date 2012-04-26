<?php
    // mode (josn)
    $error_mode = "json";
    
    // libraries & acl
    require_once "common.php";
    
    // check input data
    if (!isset($_REQUEST["q"]) OR
        !isset($_REQUEST["from"]) OR
        !isset($_REQUEST["to"])) {
        GISMOutil::gismo_error('err_missing_parameters', $error_mode);
        exit;
    } else {
        $query = addslashes($_REQUEST["q"]);
        $course_id = intval($srv_data->course_id);
        $from = intval($_REQUEST["from"]);            
        $to = intval($_REQUEST["to"]);    
    }
    
    // time filter
    $time_filter = sprintf("BETWEEN %u AND %u", $from, $to);
    
    // get course
    $course = get_record("course", "id", $course_id);
    if ($course === FALSE) {
        GISMOutil::gismo_error('err_course_not_set', $error_mode);
        exit;
    }
    
    // get users
    $users = get_course_students($course->id);
    if ($users === FALSE) {
        GISMOutil::gismo_error('err_missing_course_students', $error_mode);
        exit;
    }
    $users_ids_qry = (is_array($users) AND count($users) > 0) ? implode(", ", array_keys($users)) : "0";
    
    
    // result
    $result = new stdClass();
    $result->name = "";
    $result->data = array();
    
    // extract data
    switch ($query) {
        case "student-accesses":
        case "student-accesses-overview":
            // chart title
            switch($query) {
                case "student-accesses-overview":
                    $result->name = get_string("student_accesses_overview_chart_title", "block_gismo");
                    break;
                case "student-accesses":
                default:
                    $result->name = get_string("student_accesses_chart_title", "block_gismo");
                    break;
            }
            // chart data
            $student_resource_access = get_records_select("gismo_student_login", sprintf("course_log = %u AND time_log %s AND userid_log IN(%s)", $course_id, $time_filter, $users_ids_qry), "time_log ASC");
            // build result 
            if ($student_resource_access !== false) {
                // evaluate start date and end date
                // 1. get min date and max date
                // 2. from min date to first of the month
                //    from max date to last of the month
                // 3. evaluate difference in days between the two dates
                if (is_array($student_resource_access) AND count($student_resource_access) > 0) {
                    // 1. min and max date
                    $keys = array_keys($student_resource_access);
                    $min_date = $student_resource_access[$keys[0]]->date_log;
                    $max_date = $student_resource_access[$keys[count($student_resource_access)-1]]->date_log;
                    // adjust values
                    $mid = explode("-", $min_date);
                    $mad = explode("-", $max_date);
                    $min_date = date("Y-m-d", mktime(0, 0 ,0 , $mid[1], 1, $mid[0]));
                    $min_datetime = date("Y-m-d H:i:s", mktime(0, 0 ,0 , $mid[1], 1, $mid[0]));
                    $max_date = date("Y-m-d", mktime(0, 0 ,0 , $mad[1] + 1, 0, $mad[0]));
                    $max_datetime = date("Y-m-d H:i:s", mktime(0, 0 ,0 , $mad[1] + 1, 0, $mad[0]));
                    // diff
                    $days = intval(GISMOutil::days_between_dates($max_datetime, $min_datetime));               
                    // save results
                    $extra_info = new stdClass();
                    $extra_info->min_date = $min_date;
                    $extra_info->max_date = $max_date;
                    $extra_info->num_days = $days;
                    $result->extra_info = $extra_info;                    
                }
                $result->data = $student_resource_access;    
            }
            break;
        case "student-resources-access":
            $subtype = (isset($_REQUEST["subtype"])) ? $_REQUEST["subtype"] : "";
            switch ($subtype) {
                case "users-details":
                    // check student id
                    if (isset($_REQUEST["id"])) {
                        // chart title
                        $result->name = "Students: student details on resources <a href='javascript:void(0);' onclick='javascript:g.analyse(\"student-resources-access\");'><img src=\"images/back.png\" alt=\"Close details\" title=\"Close details\" /></a>";
                        // get data
                        $student_resource_access = get_records_select("gismo_student_res_access", sprintf("course_sra = %u AND time_sra %s AND userid_sra = %u", $course_id, $time_filter, intval($_REQUEST["id"])), "time_sra ASC");
                        // build result 
                        if ($student_resource_access !== false) {
                            // evaluate start date and end date
                            // 1. get min date and max date
                            // 2. from min date to first of the month
                            //    from max date to last of the month
                            // 3. evaluate difference in days between the two dates
                            if (is_array($student_resource_access) AND count($student_resource_access) > 0) {
                                // 1. min and max date
                                $keys = array_keys($student_resource_access);
                                $min_date = $student_resource_access[$keys[0]]->date_sra;
                                $max_date = $student_resource_access[$keys[count($student_resource_access)-1]]->date_sra;
                                // adjust values
                                $mid = explode("-", $min_date);
                                $mad = explode("-", $max_date);
                                $min_date = date("Y-m-d", mktime(0, 0 ,0 , $mid[1], 1, $mid[0]));
                                $min_datetime = date("Y-m-d H:i:s", mktime(0, 0 ,0 , $mid[1], 1, $mid[0]));
                                $max_date = date("Y-m-d", mktime(0, 0 ,0 , $mad[1] + 1, 0, $mad[0]));
                                $max_datetime = date("Y-m-d H:i:s", mktime(0, 0 ,0 , $mad[1] + 1, 0, $mad[0]));
                                // diff
                                $days = intval(GISMOutil::days_between_dates($max_datetime, $min_datetime));               
                                // save results
                                $extra_info = new stdClass();
                                $extra_info->min_date = $min_date;
                                $extra_info->max_date = $max_date;
                                $extra_info->num_days = $days;
                                $result->extra_info = $extra_info;                    
                            }
                            $result->data = $student_resource_access;    
                        }
                    }
                    break;
                default:
                    // chart title
                    $result->name = get_string("student_resources_overview_chart_title", "block_gismo");
                    // get data
                    $student_resource_access = get_records_select("gismo_student_res_access", sprintf("course_sra = %u AND time_sra %s AND userid_sra IN(%s)", $course_id, $time_filter, $users_ids_qry), "time_sra ASC");
                    // build result 
                    if ($student_resource_access !== false) {
                        $result->data = $student_resource_access;    
                    }
                    break;
            }
            break;
        case "resources-students-overview":
            // chart title
            $result->name = get_string("resources_students_overview_chart_title", "block_gismo");
            // chart data
            $resource_accesses = get_records_select("gismo_res_access", sprintf("course_rac = %u AND time_rac %s AND userid_rac IN(%s)", $course_id, $time_filter, $users_ids_qry), "time_rac ASC");
            // extra info (get max value)
            $query = "SELECT id_rac, SUM(count_rac) AS count FROM " . $CFG->prefix . "gismo_res_access WHERE course_rac = " . intval($course_id) .
                     " GROUP BY userid_rac, idresource_rac ORDER BY count DESC LIMIT 1 OFFSET 0"; 
                     // TODO add course_start & course_end filters 
            $ei = get_records_sql($query);
            $extra_info = new stdClass();
            $extra_info->max_value = 100;   // TODO: error management
            if ($ei !== false AND is_array($ei) AND count($ei) > 0) {
                $extra_info->max_value = array_pop($ei)->count;        
            }
            // result
            if ($resource_accesses !== false) {
                $result->extra_info = $extra_info;
                $result->data = $resource_accesses;    
            }
            break;        
        case "resources-access":
            $subtype = (isset($_REQUEST["subtype"])) ? $_REQUEST["subtype"] : "";
            switch ($subtype) {
                case "resources-details":
                    // check resource id
                    if (isset($_REQUEST["id"])) {
                        // chart title
                        $result->name = "Resources: Resource details on students <a href='javascript:void(0);' onclick='javascript:g.analyse(\"resources-access\");'><img src=\"images/back.png\" alt=\"Close details\" title=\"Close details\" /></a>";
                        // chart data
                        $resource_accesses = get_records_select("gismo_res_access", sprintf("course_rac = %u AND time_rac %s AND idresource_rac = %u", $course_id, $time_filter, intval($_REQUEST["id"])), "time_rac ASC");
                        // result
                        if ($resource_accesses !== false) {
                            // evaluate start date and end date
                            // 1. get min date and max date
                            // 2. from min date to first of the month
                            //    from max date to last of the month
                            // 3. evaluate difference in days between the two dates
                            if (is_array($resource_accesses) AND count($resource_accesses) > 0) {
                                // 1. min and max date
                                $keys = array_keys($resource_accesses);
                                $min_date = $resource_accesses[$keys[0]]->date_rac;
                                $max_date = $resource_accesses[$keys[count($resource_accesses)-1]]->date_rac;
                                // adjust values
                                $mid = explode("-", $min_date);
                                $mad = explode("-", $max_date);
                                $min_date = date("Y-m-d", mktime(0, 0 ,0 , $mid[1], 1, $mid[0]));
                                $min_datetime = date("Y-m-d H:i:s", mktime(0, 0 ,0 , $mid[1], 1, $mid[0]));
                                $max_date = date("Y-m-d", mktime(0, 0 ,0 , $mad[1] + 1, 0, $mad[0]));
                                $max_datetime = date("Y-m-d H:i:s", mktime(0, 0 ,0 , $mad[1] + 1, 0, $mad[0]));
                                // diff
                                $days = intval(GISMOutil::days_between_dates($max_datetime, $min_datetime));               
                                // save results
                                $extra_info = new stdClass();
                                $extra_info->min_date = $min_date;
                                $extra_info->max_date = $max_date;
                                $extra_info->num_days = $days;
                                $result->extra_info = $extra_info;                    
                            }
                            $result->data = $resource_accesses;    
                        }
                    }
                    break;
                default:
                    // chart title
                    $result->name = get_string("resources_access_overview_chart_title", "block_gismo");
                    // chart data
                    $resource_accesses = get_records_select("gismo_res_access", sprintf("course_rac = %u AND time_rac %s AND userid_rac IN(%s)", $course_id, $time_filter, $users_ids_qry), "time_rac ASC");
                    // result
                    if ($resource_accesses !== false) {
                        $result->data = $resource_accesses;    
                    }  
                    break;
            }
            break;
        case "assignments":
            // chart title
            $result->name = get_string("assignments_chart_title", "block_gismo");
            // chart data
            $fields = "asub.id AS asub_id, " . // stupid moodle get_records_sql function set array key with the first selected field (use a unique key to avoid data loss)
                      "asub.userid AS userid, " .
                      "asub.grade AS user_grade, " .
                      "asub.timemodified AS submission_time, " .
                      "asub.timemarked AS test_timemarked, " .
                      "a.id AS test_id, " . 
                      "a.grade AS test_max_grade";  
            $qry = "SELECT " . $fields . " FROM " . $CFG->prefix . "assignment AS a INNER JOIN " .
                   $CFG->prefix . "assignment_submissions AS asub ON a.id = asub.assignment WHERE a.course = " . 
                   $course_id . " AND asub.timemodified " . $time_filter;
            $assignments = get_records_sql($qry);
            // build result 
            if ($assignments !== false AND 
                is_array($assignments) AND count($assignments) > 0 AND
                is_array($users) AND count($users) > 0) { 
                foreach ($assignments as $assignment) {
                    if (array_key_exists($assignment->userid, $users)) {
                        $item = array("test_id" => $assignment->test_id,
                                      "test_max_grade" => $assignment->test_max_grade,
                                      "userid" => $assignment->userid,
                                      "user_grade" => $assignment->user_grade,              // -1 if it hasn't been corrected
                                      "submission_time" => $assignment->submission_time,
                                      "test_timemarked" => $assignment->test_timemarked);   // 0 if it hasn't been corrected
                        array_push($result->data, $item);
                    }          
                }    
            }
            break;
        case "quizzes":
            // chart title
            $result->name = get_string("quizzes_chart_title", "block_gismo");
            // chart data
            $fields = "qgrd.id AS qgrd_id, " .  // stupid moodle get_records_sql function set array key with the first selected field (use a unique key to avoid data loss)
                      "qgrd.userid AS userid, " .
                      "qgrd.grade AS user_grade, " .
                      "qgrd.timemodified AS submission_time, " .
                      "q.id AS test_id, " . 
                      "q.grade AS test_max_grade";  
            $qry = "SELECT " . $fields . " FROM " . $CFG->prefix . "quiz AS q INNER JOIN " .
                   $CFG->prefix . "quiz_grades AS qgrd ON q.id = qgrd.quiz WHERE q.course = " . 
                   $course_id . " AND qgrd.timemodified " . $time_filter;
            $quizzes = get_records_sql($qry);
            // build result 
            if ($quizzes !== false AND 
                is_array($quizzes) AND count($quizzes) > 0 AND
                is_array($users) AND count($users) > 0) { 
                foreach ($quizzes as $quiz) {
                    if (array_key_exists($quiz->userid, $users)) {
                        $item = array("test_id" => $quiz->test_id,
                                      "test_max_grade" => $quiz->test_max_grade,
                                      "userid" => $quiz->userid,
                                      "user_grade" => $quiz->user_grade,
                                      "submission_time" => $quiz->submission_time);
                        array_push($result->data, $item);
                    }          
                }    
            }
            break;
        default:
            break;        
    }
    
    // echo json encoded result
    echo json_encode($result);
?>