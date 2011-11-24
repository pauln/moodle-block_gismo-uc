<?php
    // libraries & acl
    require_once "common.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="style/gismo.css" type="text/css" media="screen" charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="lib/third_parties/client_side/jqplot.0.9.7/jquery.jqplot.min.css" />
        <script type="text/javascript" src="lib/third_parties/client_side/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jquery-ui-1.8.6/js/jquery-ui-1.8.6.custom.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/jquery.jqplot.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/plugins/jqplot.barRenderer.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/plugins/jqplot.canvasTextRenderer.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/plugins/jqplot.categoryAxisRenderer.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/plugins/jqplot.dateAxisRenderer.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/plugins/jqplot.highlighter.min.js"></script>
        <script type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/plugins/jqplot.pointLabels.min.js"></script>

    </head>
    <body>
        <div>
            <?php echo (isset($_POST["datatodisplay"])) ? stripcslashes($_POST["datatodisplay"]) : ""; ?>
        </div>
        <script>
            // dialog
            $(document).ready(function() {
                <?php 
                    if (isset($_POST["mode"])) {
                        switch ($_POST["mode"]) {
                            case "1":
                ?>
                window.print();
                <?php
                                break;
                            case "0":
                            default:
                                break;
                        }
                    } 
                ?>                
            });
        </script>   
    </body>
</html>
