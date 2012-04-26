<?php
    require_once "../../../../../config.php";
    
    // libraries
    $d = DIRECTORY_SEPARATOR;
    $lib_dir = realpath(dirname( __FILE__ ) . $d . ".." . $d . ".." . $d . ".." . $d) . $d . 'lib' . $d . 'gismo' . $d . 'server_side' . $d;
    require_once $lib_dir . "GISMOdata_manager.php";
    
    // trace start
    echo "GISMO - export data (start)!<br />";
    
    $gdm = new GISMOdata_manager(true);
    
    // purge
    $purge_check = $gdm->purge_data();
    if ($purge_check === true) {
        echo "Gismo data has been purged successfully!<br />";        
    } else {
        echo $purge_check . "<br />";    
    }
    
    // sync
    $sync_check = $gdm->sync_data();
    if ($sync_check === true) {
        echo "Gismo data has been syncronized successfully!<br />";    
    } else {
        echo $sync_check . "<br />";     
    }
    
    // trace end
    echo "GISMO - export data (end)!<br />";
    
    // ok     
    return true;    
?>
