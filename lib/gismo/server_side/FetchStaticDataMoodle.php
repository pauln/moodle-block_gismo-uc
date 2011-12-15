<?php
// this class is used to fetch static data
class FetchStaticDataMoodle {
    // course data
    protected $id;
    protected $timecreated;
    protected $fullname;

    protected $course;

    // analysis start date / time
    protected $start_date;
    protected $start_time;

    // analysis end date / time
    protected $end_date;
    protected $end_time;

    // useful fields
    protected $users_ids;

    // Json fields
    protected $users;
    protected $assignments;
    protected $quizzes;
    protected $resources;
    protected $books;
    protected $forums;

    // constructor
    public function __construct($id) {
        $this->id = $id;
    }

    // getter
    public function __get($name) {
        return (property_exists($this, $name)) ? $this->$name : null;
    }

    // init
    public function init() {
        // check variable
        $check = true;
        // fetch data
        $check &= $this->FetchInfo();
        $check &= $this->FetchAssignments();
        $check &= $this->FetchUsers();
        $check &= $this->FetchQuizzes();
        $check &= $this->FetchResources('resource');
        $check &= $this->FetchResources('book');
        $check &= $this->FetchResources('forum');
        // start date / time
        $check &= $this->FetchStartDateAndTime();
        // return result
        return $check;
    }

    // fetch course info
    protected function FetchInfo() {
        // check variable
        $check = true;
        // fetch course
        $record = get_record("course", "id", $this->id);
        // save data
        if ($record !== FALSE) {
            $this->timecreated = $record->timecreated;
            $this->coursestart = $record->startdate;
            $this->fullname = $record->fullname;
            $this->course = $record;
        } else {
            $check = false;
        }
        // return result
        return $check;
    }

    // fetch assignments
    protected function FetchAssignments() {
        // default variables
        $check = false;
        $this->assignments = "[]";
        // fetch assignments
        $assignments = get_all_instances_in_course("assignment", $this->course, null, true);
        // $assignments = get_records_select("assignment", "course = " . $this->id, "name", "id,name,timeavailable,timedue,course,grade");
        // save data
        if ($assignments !== FALSE) {
            $json_assignments = array();
            $check = true;
            if (is_array($assignments) AND count($assignments) > 0) {
                foreach ($assignments as $assignment) {
                    $json_assignments[] = array("id" => $assignment->id,
                                                "name" => $assignment->name,
                                                "timeavailable" => $assignment->timeavailable,
                                                "gradeOver" => $assignment->grade,
                                                "timedue" => $assignment->timedue,
                                                "visible" => $assignment->visible);
                }
                $this->assignments = json_encode($json_assignments);
            }
        }
        // return result
        return $check;
    }

    // fetch users
    protected function FetchUsers() {
        // default variables
        $check = false;
        $this->users = "[]";
        // fetch students
        $users = get_course_students($this->id, "lastname");
        // save data
        if ($users !== FALSE) {
            $json_users = array();
            $check = true;
            if (is_array($users) AND count($users) > 0) {
                foreach ($users as $user) {
                    $json_users[] = array("id" => $user->id,
                                          "name" => ucfirst($user->lastname)." ".ucfirst($user->firstname),
                                          "visible" => "1");
                }
                $this->users = json_encode($json_users);
                $this->users_ids = array_keys($users);
            }
        }
        // return result
        return $check;
    }

    // fetch quizzes
    protected function FetchQuizzes() {
        // default variables
        $check = false;
        $this->quizzes = "[]";
        // fetch quizzes
        $quizzes = get_all_instances_in_course("quiz", $this->course, null, true);
        //$quizzes = get_records("quiz", "course", $this->id, "name");
        // save data
        if ($quizzes !== FALSE) {
            $json_quizzes = array();
            $check = true;
            if (is_array($quizzes) AND count($quizzes) > 0) {
                foreach ($quizzes as $quiz) {
                    $json_quizzes[] = array("id" => $quiz->id,
                                            "name" => $quiz->name,
                                            "timeopen_qui" => $quiz->timeopen,
                                            "timeclose_qui" => $quiz->timeclose,
                                            "visible" => $quiz->visible);
                }
                $this->quizzes = json_encode($json_quizzes);
            }
        }
        // return result
        return $check;
    }

    // fetch resources
    protected function FetchResources($type) {
        // default variables
        $check = false;
        $type_plural = $type."s";
        $this->$type_plural = "[]";
        // fetch resources
        $resources = get_all_instances_in_course($type, $this->course, null, true);
        //$resources = get_records("resource", "course", $this->id, "name");
        // save data
        $json_resources = array();
        if ($resources !== FALSE) {
            $check = true;
            if (is_array($resources) AND count($resources) > 0) {
                foreach ($resources as $resource) {
                    $json_resources[] = array("id" => $resource->coursemodule,
                                              "name" => $resource->name,
                                              "visible" => $resource->visible);
                }
            }
        }
        if (sizeof($json_resources) > 0) {
            $this->$type_plural = json_encode($json_resources);
        }
        // return result
        return $check;
    }

    // fetch start date and time
    protected function FetchStartDateAndTime() {
        // check variable
        $check = true;
        // select min date / time & max date / time for each log table
        // default
        $this->end_time = time();
        $this->end_date = date("Y-m-d", $this->end_time);
        $this->start_time = (empty($CFG->loglifetime)) ? $this->coursestart : ($this->end_time - ($CFG->loglifetime * 86400));
        $this->start_date = date("Y-m-d", $this->start_time);
        // values according to logs
        if (is_array($this->users_ids) AND count($this->users_ids) > 0) {
            // students ids for query
            $qry_students_ids = implode(", ", $this->users_ids);
            // modes
            $modes = array("start" => "ASC", "end" => "DESC");
            if (is_array($modes) AND count($modes) > 0) {
                foreach ($modes as $context => $order_criteria) {
                    $dates = array();
                    $times = array();
                    $d = array();
                    $d["rac"] = get_records_select("gismo_res_access", "userid_rac IN(" . $qry_students_ids . ") AND course_rac = " . $this->id, "time_rac " . $order_criteria, "id_rac, time_rac, date_rac", 0, 1);
                    $d["log"] = get_records_select("gismo_student_login", "userid_log IN(" . $qry_students_ids . ") AND course_log = " . $this->id, "time_log " . $order_criteria, "id_log, time_log, date_log", 0, 1);
                    $d["sra"] = get_records_select("gismo_student_res_access", "userid_sra IN(" . $qry_students_ids . ") AND course_sra = " . $this->id, "time_sra " . $order_criteria, "id_sra, time_sra, date_sra", 0, 1);
                    if ($d["rac"] === FALSE AND $d["log"] === FALSE AND $d["sra"] === FALSE) {
                        $check = false;
                    } else {
                        foreach ($d as $key => $e) {
                            if (is_array($e) AND count($e) > 0) {
                                $entry = array_pop($e);
                                $datef = "date_" . $key;
                                $timef = "time_" . $key;
                                $dates[] = $entry->$datef;
                                $times[] = $entry->$timef;
                            }
                        }
                        // save fields
                        $datef = $context . "_date";
                        $timef = $context . "_time";
                        // save
                        $this->$datef = ($context === "start") ? min($dates) : max($dates);
                        $this->$timef = ($context === "start") ? min($times) : max($times);
                    }
                }
            }
            // start date & time => to the first day of the month
            $this->start_time = GISMOutil::this_month_first_day_time($this->start_time);
            $this->start_date = date("Y-m-d", $this->start_time);
            // end date & time => to the first day of the next month
            $this->end_time = GISMOutil::next_month_first_day_time($this->end_time);
            $this->end_date = date("Y-m-d", $this->end_time);
        }
        // return result
        return $check;
    }

    public function checkData() {
        if ($this->users !== "[]" AND
            !($this->resources === "[]" AND $this->books === "[]" AND $this->forums === "[]" AND $this->assignments === "[]" AND $this->quizzes === "[]")) {
            return true;
        } else {
            return false;
        }
    }
}
?>
