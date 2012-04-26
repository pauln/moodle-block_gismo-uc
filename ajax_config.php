<?php
    // mode (josn)
    $error_mode = "json";
    
    // libraries & acl
    require_once "common.php";
    
    // query
    $q = (isset($_REQUEST["q"])) ? $_REQUEST["q"] : "";
    
    // decide what to do
    switch ($q) {
        case "save":
            $result = array("status" => "false");
            if (isset($_REQUEST["config_data"]) AND is_array($_REQUEST["config_data"]) AND count($_REQUEST["config_data"]) > 0) {
                // serialize and encode config data
                $config_data = base64_encode(serialize((object) $_REQUEST["config_data"]));
                // update config
                $check = set_field("block_instance", "configdata", $config_data, "id", $srv_data->block_instance_id);
                if ($check !== false) {
                    $result["status"] = "true";    
                }    
            }
            break;
        default:
            break;    
    }
    
    // send response
    echo json_encode($result);   
?>
