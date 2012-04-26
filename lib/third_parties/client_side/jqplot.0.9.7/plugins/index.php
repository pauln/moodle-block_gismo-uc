<?php
    echo "\$jqplot_plugins = array(";
    $dir = dirname(__FILE__);
    $files = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if (strpos($file, "min")) {
                $files[] = substr($file, 0, strlen($file) - 3);    
            }
        }
        closedir($handle);
    }
    sort($files);
    $count=0;
    foreach ($files as $file) {
        echo $count .  " => \"" . $file . "\",<br />";
        $count++; 
    }
    echo ");"
?>
