<?php
require_once('JSON.php');
$GISMO_JSON = new Services_JSON();
function json_encode($arr) {
    global $GISMO_JSON;
    return $GISMO_JSON->encode($arr);
}

?>
