<?php
require_once('JSON.php');
function json_encode($arr) {
    $json = new Services_JSON();
    return $json->encode($arr);
}

?>
