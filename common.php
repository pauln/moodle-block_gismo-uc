<?php
    /*
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    */
    
    // error mode
    $error_mode = (isset($error_mode) AND in_array($error_mode, array("json", "moodle"))) ? $error_mode : "moodle";
            
    // define constants
    define('ROOT', (realpath(dirname( __FILE__ )) . DIRECTORY_SEPARATOR));
    define('LIB_DIR', ROOT . "lib" . DIRECTORY_SEPARATOR);
    
    // include moodle config file
    require_once realpath(ROOT . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config.php");

    if(!function_exists("json_encode")) {
        require_once('json_encode.php');
    }
    
    // query filter between pages
    $query = (isset($_REQUEST['q'])) ? addslashes($_REQUEST['q']) : '';
    
    // LIBRARIES MANAGEMENT
    // Please use this section to set server side and cliend side libraries to be included
    
    // server side: please note that '.php' extension will be automatically added                                             
    $server_side_libraries = array("gismo"          => array("FetchStaticDataMoodle", "GISMOutil"),
                                   "third_parties"  => array());     
    
    // client side: please note that '.js' extension will be automatically added   
    $client_side_libraries = array("gismo"          => array("gismo", "top_menu", "left_menu", "time_line", "gismo_util"),
                                   "third_parties"  => array("jquery/jquery-1.7.1.min",
                                                             "jquery-ui-1.8.6/js/jquery-ui-1.8.6.custom.min",
                                                             "jqplot.0.9.7/jquery.jqplot.min",
                                                             "jqplot.0.9.7/plugins/jqplot.barRenderer.min",
                                                             "jqplot.0.9.7/plugins/jqplot.canvasAxisLabelRenderer.min",
                                                             "jqplot.0.9.7/plugins/jqplot.canvasAxisTickRenderer.min",
                                                             "jqplot.0.9.7/plugins/jqplot.canvasTextRenderer.min",
                                                             "jqplot.0.9.7/plugins/jqplot.categoryAxisRenderer.min",
                                                             // "jqplot.0.9.7/plugins/jqplot.cursor.min",
                                                             "jqplot.0.9.7/plugins/jqplot.dateAxisRenderer.min",
                                                             // "jqplot.0.9.7/plugins/jqplot.dragable.min",
                                                             "jqplot.0.9.7/plugins/jqplot.highlighter.min",
                                                             // "jqplot.0.9.7/plugins/jqplot.logAxisRenderer.min",
                                                             // "jqplot.0.9.7/plugins/jqplot.mekkoAxisRenderer.min",
                                                             // "jqplot.0.9.7/plugins/jqplot.mekkoRenderer.min",
                                                             // "jqplot.0.9.7/plugins/jqplot.ohlcRenderer.min",
                                                             // "jqplot.0.9.7/plugins/jqplot.pieRenderer.min",
                                                             "jqplot.0.9.7/plugins/jqplot.pointLabels.min"
                                                             // "jqplot.0.9.7/plugins/jqplot.trendline.min"
                                                             ));
                                                             
    
    
    // include server-side libraries libraries
    if (is_array($server_side_libraries) AND count($server_side_libraries) > 0) {
        foreach ($server_side_libraries as $key => $server_side_libs) {
            if (is_array($server_side_libs) AND count($server_side_libs) > 0) {
                foreach ($server_side_libs as $server_side_lib) {
                    $lib_full_path = LIB_DIR . $key . DIRECTORY_SEPARATOR . "server_side" . DIRECTORY_SEPARATOR .  $server_side_lib . ".php";
                    if (is_file($lib_full_path) AND is_readable($lib_full_path)) {
                        require_once $lib_full_path;    
                    }    
                }
            }          
        }
    }
    
    // check input data
    if (!isset($_REQUEST["srv_data"])) {
        GISMOutil::gismo_error('err_srv_data_not_set', $error_mode);
        exit;    
    }
    $srv_data_encoded = $_REQUEST["srv_data"];
    $srv_data = (object) unserialize(base64_decode(urldecode($srv_data_encoded)));
    
    // course id
    if (!property_exists($srv_data, "course_id")) {
        GISMOutil::gismo_error('err_course_not_set', $error_mode);
        exit;       
    }
    
    // block instance id
    if (!property_exists($srv_data, "block_instance_id")) {
        GISMOutil::gismo_error('err_block_instance_id_not_set', $error_mode);
        exit;        
    }
    
    // extract the course    
    if (! $course = get_record("course", "id", intval($srv_data->course_id))) {
        GISMOutil::gismo_error('err_course_not_set', $error_mode);
        exit;
    }
    
    // check authorization
    if (!has_capability('block/gismo:view', get_context_instance(CONTEXT_BLOCK, intval($srv_data->block_instance_id)))) {
        GISMOutil::gismo_error('err_access_denied', $error_mode);
        exit;
    }
    
    // get gismo settings
    $gismo_settings = get_field("block_instance", "configdata", "id", intval($srv_data->block_instance_id));
    if (is_null($gismo_settings) OR $gismo_settings === "") {
        $gismo_settings = get_object_vars(GISMOutil::get_default_options());   
    } else {
        $gismo_settings = get_object_vars(unserialize(base64_decode($gismo_settings)));
        if (is_array($gismo_settings) AND count($gismo_settings) > 0) {
            foreach ($gismo_settings as $key => $value) {
                if (is_numeric($value)) {
                    if (strval(intval($value)) === strval($value)) {
                        $gismo_settings[$key] = intval($value);
                    } else if (strval(floatval($value)) === strval($value)) {
                        $gismo_settings[$key] = floatval($value);    
                    }
                }       
            }
        }
        // include_hidden_items
        if (!array_key_exists("include_hidden_items", $gismo_settings)) {
            $gismo_settings["include_hidden_items"] = 1;
        }
    }
    $gismo_config = json_encode($gismo_settings); 
?>
