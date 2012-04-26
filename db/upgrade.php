<?php

// This file keeps track of upgrades to 
// the moodle_gismo
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_block_gismo_upgrade($oldversion=0) {
    global $CFG, $THEME, $db;

    $result = true;
	
    if ($result && $oldversion < 2010043000) {
        // remove block instance settings from gismo general settings table (gismo_config)
        $result = $result && delete_records("gismo_config", "name", "matrix_num_series_limit");
        $result = $result && delete_records("gismo_config", "name", "chart_base_color");
        $result = $result && delete_records("gismo_config", "name", "resize_delay");
        $result = $result && delete_records("gismo_config", "name", "chart_axis_label_max_len");
        $result = $result && delete_records("gismo_config", "name", "chart_axis_label_max_offset");	
    }

    if ($result && $oldversion < 2010113000) {
        // add new setting 'include hidden items' in the DB
        $result = true;
    }

    return $result;
}

?>