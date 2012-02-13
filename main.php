<?php
    // libraries & acl
    require_once "common.php";
    
    // fetch static data
    $gismo_static_data = new FetchStaticDataMoodle($course->id);
    $gismo_static_data->init();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?php echo get_string('page_title', 'block_gismo') . " " . $gismo_static_data->fullname;?></title>
        <!-- client side libraries START -->
        <?php
        if (is_array($client_side_libraries) AND count($client_side_libraries) > 0) {
            foreach ($client_side_libraries as $key => $client_side_libs) {
                if (is_array($client_side_libs) AND count($client_side_libs) > 0) {
                    foreach ($client_side_libs as $client_side_lib) {
                        $lib_full_path = LIB_DIR . $key . DIRECTORY_SEPARATOR . "client_side" . DIRECTORY_SEPARATOR .  $client_side_lib . ".js";
                        if (is_file($lib_full_path) AND is_readable($lib_full_path)) {
        ?>
        <script type="text/javascript" src="lib/<?php echo $key . "/client_side/" . $client_side_lib; ?>.js"></script>
        <?php                         
                        }    
                    }
                }          
            }
        }
        ?>
        <!--[if IE]><script language="javascript" type="text/javascript" src="lib/third_parties/client_side/jqplot.0.9.7/excanvas.js"></script><![endif]-->
        <!-- client side libraries END -->
        <link rel="stylesheet" href="style/gismo.css" type="text/css" media="screen" charset="utf-8" />
        <link rel="stylesheet" href="lib/third_parties/client_side/jquery-ui-1.8.6/css/ui-darkness/jquery-ui-1.8.6.custom.css" type="text/css" media="screen" charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="lib/third_parties/client_side/jqplot.0.9.7/jquery.jqplot.min.css" />
        <?php
            // static data + gismo instance not needed by help page 
            if (!in_array($query, array("help"))) {  
        ?>
        <script type="text/javascript">
            // <!--
            
            // static data
            var config = <?php echo $gismo_config; ?>;
            var srv_data = '<?php echo $srv_data_encoded; ?>';
            var static_data = new Array();
            static_data['users'] = <?php echo $gismo_static_data->users; ?>;
            static_data['quizzes'] = <?php echo $gismo_static_data->quizzes; ?>;
            static_data['resources'] = <?php echo $gismo_static_data->resources; ?>;
            static_data['books'] = <?php echo $gismo_static_data->books; ?>;
            static_data['forums'] = <?php echo $gismo_static_data->forums; ?>;
            static_data['glossaries'] = <?php echo $gismo_static_data->glossaries; ?>;
            static_data['wikis'] = <?php echo $gismo_static_data->wikis; ?>;
            // build summary array by concatenating previous three
            static_data['activitysummary'] = static_data['forums'].concat(static_data['glossaries']);
            static_data['activitysummary'].push.apply(static_data['activitysummary'], static_data['wikis']);
            static_data['assignments'] = <?php echo $gismo_static_data->assignments; ?>;
            static_data['course_full_name'] = '<?php echo str_replace("'", "\\'", $gismo_static_data->fullname); ?>';
            var course_start_time = <?php echo $gismo_static_data->start_time; ?>;
            var current_time = <?php echo $gismo_static_data->end_time; ?>;
            
            // gismo instance
            var g = new gismo(config, srv_data, static_data, course_start_time, current_time);
            
            // initialize application
            $(document).ready(function() {
                // init
                g.init();
                
                // window resize event
                $(window).resize(function () { g.resize(); });
                
                // force resize
                setTimeout(function() { g.resize(); }, 100);                
            });
            
            
            // -->
        </script>
        <?php 
            } 
        ?>
    </head>
    <body>
        <div id="dialog"></div>
        <div id="header">
            <form class="hidden" id="print_form" name="print_form" action="print.php" target="_blank" method="post">
                <textarea class="hidden" id="datatodisplay" name="datatodisplay"></textarea>
                <input type="hidden" id="mode" name="mode" />
            </form>
            <div id="menu">
                <ul id="panelMenu">
                <?php if (in_array($query, array("help"))) { ?>
                    <li><a href="?srv_data=<?php echo $srv_data_encoded;?>"><?php echo get_string('close', 'block_gismo'); ?></a></li>    
                <?php } else { ?>
                    <li><a href="javascript:void(0)"><?php echo get_string('file', 'block_gismo'); ?>&nbsp;&nbsp;<img src="images/menu_icon.gif" alt="" /></a>
                        <ul>
                            <li><a href="javascript:g.options();"><div><nobr><?php echo get_string('options', 'block_gismo'); ?></nobr></div></a></li>
                            <li><a href="javascript:g.exit();"><div><nobr><?php echo get_string('exit', 'block_gismo'); ?></nobr></div></a></li>
                        </ul>                        
                    </li>
                    <li><a href="javascript:void(0)"><?php echo get_string('students', 'block_gismo'); ?>&nbsp;&nbsp;<img src="images/menu_icon.gif" alt="" /></a>
                        <ul>
                             <li>
                                <a href="javascript:g.analyse('student-accesses')"><div><nobr><?php echo get_string('student_accesses', 'block_gismo'); ?></nobr></div></a>
                            </li>
                            <li>
                                <a href="javascript:g.analyse('student-accesses-overview')"><div><nobr><?php echo get_string('student_accesses_overview', 'block_gismo'); ?></nobr></div></a>
                            </li>
                            <?php if ($gismo_static_data->resources !== "[]") { ?>
                                <li>
                                    <a href="javascript:g.analyse('student-resources-access')"><div><nobr><?php echo get_string('student_resources_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if ($gismo_static_data->books !== "[]") { ?>
                                <li>
                                    <a href="javascript:g.analyse('student-books-access')"><div><nobr><?php echo get_string('student_books_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if ($gismo_static_data->forums !== "[]") { ?>
                                <li>
                                    <a href="javascript:g.analyse('student-forums-access')"><div><nobr><?php echo get_string('student_forums_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if ($gismo_static_data->glossaries !== "[]") { ?>
                                <li>
                                    <a href="javascript:g.analyse('student-glossaries-access')"><div><nobr><?php echo get_string('student_glossaries_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if ($gismo_static_data->wikis !== "[]") { ?>
                                <li>
                                    <a href="javascript:g.analyse('student-wikis-access')"><div><nobr><?php echo get_string('student_wikis_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                        </ul>               
                    </li>
                    <?php if ($gismo_static_data->resources !== "[]" || $gismo_static_data->books !== "[]") { ?>
                    <li><a href="javascript:void(0)"><?php echo get_string('resources', 'block_gismo'); ?>&nbsp;&nbsp;<img src="images/menu_icon.gif" alt="" /></a>
                        <ul>
                            <?php if ($gismo_static_data->resources !== "[]") { ?>
                                <li>
                                    <a href="javascript:g.analyse('resources-students-overview');"><div><nobr><?php echo get_string('resources_students_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                                <li>
                                    <a href="javascript:g.analyse('resources-access');"><div><nobr><?php echo get_string('resources_access_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if ($gismo_static_data->books !== "[]") { ?>
                                <li>
                                    <a href="javascript:g.analyse('books-students-overview');"><div><nobr><?php echo get_string('books_students_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                                <li>
                                    <a href="javascript:g.analyse('books-access');"><div><nobr><?php echo get_string('books_access_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                        </ul>               
                    </li>
                    <?php } ?>
                    <?php if (!($gismo_static_data->assignments === "[]" AND $gismo_static_data->quizzes === "[]" AND $gismo_static_data->forums === "[]" AND $gismo_static_data->glossaries === "[]" AND $gismo_static_data->wikis === "[]")) { $activitiesShown=0;?>
                    <li><a href="javascript:void(0)"><?php echo get_string('activities', 'block_gismo'); ?>&nbsp;&nbsp;<img src="images/menu_icon.gif" alt="" /></a>
                        <ul>
                            <?php if ($gismo_static_data->assignments !== "[]") { ?>
                                <li>
                                    <a href="javascript:g.analyse('assignments')"><div><nobr><?php echo get_string('assignments', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if ($gismo_static_data->quizzes !== "[]") { ?>
                                <li>
                                    <a href="javascript:g.analyse('quizzes')"><div><nobr><?php echo get_string('quizzes', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if (($gismo_static_data->assignments !== "[]" OR $gismo_static_data->quizzes !== "[]") AND ($gismo_static_data->forums !== "[]" OR $gismo_static_data->glossaries !== "[]" OR $gismo_static_data->wikis !== "[]")) { ?>
                                <li style="border-bottom:1px solid #999;margin-bottom:10px;"></li>
                            <?php } ?>
                            <?php if ($gismo_static_data->forums !== "[]") { $activitiesShown++;?>
                                <li>
                                    <a href="javascript:g.analyse('forums-students-overview');"><div><nobr><?php echo get_string('forums_students_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                                <li>
                                    <a href="javascript:g.analyse('forums-access');"><div><nobr><?php echo get_string('forums_access_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if ($gismo_static_data->glossaries !== "[]") { $activitiesShown++;?>
                                <li>
                                    <a href="javascript:g.analyse('glossaries-students-overview');"><div><nobr><?php echo get_string('glossaries_students_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                                <li>
                                    <a href="javascript:g.analyse('glossaries-access');"><div><nobr><?php echo get_string('glossaries_access_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if ($gismo_static_data->wikis !== "[]") { $activitiesShown++;?>
                                <li>
                                    <a href="javascript:g.analyse('wikis-students-overview');"><div><nobr><?php echo get_string('wikis_students_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                                <li>
                                    <a href="javascript:g.analyse('wikis-access');"><div><nobr><?php echo get_string('wikis_access_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                            <?php if ($activitiesShown > 1) { ?>
                                <li>
                                    <a href="javascript:g.analyse('activitysummary-students-overview');"><div><nobr><?php echo get_string('activitysummary_students_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                                <li>
                                    <a href="javascript:g.analyse('activitysummary-access');"><div><nobr><?php echo get_string('activitysummary_access_overview', 'block_gismo'); ?></nobr></div></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <li><a href="javascript:void(0)"><?php echo get_string('help', 'block_gismo'); ?>&nbsp;&nbsp;<img src="images/menu_icon.gif" alt="" /></a>
                        <ul>
                            <li>
                                <a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>"><div><nobr><?php echo get_string('help_page', 'block_gismo'); ?></nobr></div></a>
                            </li>                          
                        </ul>
                    </li>
                </ul>
            <?php } ?>
            </div>
            <a id="logo" href="http://gismo.sourceforge.net" target="_blank"><img src="images/logo.png" /></a>
        </div><br clear="all" />
        <div id="content">
            <?php
                // content and footer
                switch ($query) {    
                    case "help":
                        $content = "template/help.php";
                        $footer = false;
                        break;
                    default:
                        $content = "template/home.php";
                        $footer = "template/footer.html";
                        break;
                }
                require_once $content;
            ?>
        </div>
        <?php 
            if($footer) {
                require_once $footer; 
            } 
        ?>
    </body>
    <?php
        if (in_array($query, array("help"))) {
            // HACK     
    ?> 
    <script>
        $(document).ready(function() {  
            $('#panelMenu > li').bind('mouseover', function () {
                $(this).children('a').addClass('menu_open');    
            });
            $('#panelMenu > li').bind('mouseout',  function () {
                $(this).children('a').removeClass('menu_open');    
            });
        });
    </script>
    <?php
        }
    ?>  
</html>
