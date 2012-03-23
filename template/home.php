<div id="content">
    <div id="left_menu">
        <div id="lm_header" class="ct_header">
            <!-- Users / Resources / Assignments / Quizzes menu -->
            <img class="image_link" id="close_control" src="images/close.png" alt="Hide menu" title="Hide menu" style="float: right; margin: 0; padding: 0;" onclick="javascript:g.lm.hide();" />
            <img class="image_link" id="left_menu_info" src="images/left_menu_info.gif" alt="Show details" title="Show details" style="float: right; margin-right: 15px;"  onclick="javascript:g.lm.show_info();" />
        </div>
        <div id="lm_content"><!-- Users / Resources / Assignments / Quizzes lists --></div>    
    </div>
    <div id="chart">
        <div id="ch_header" class="ct_header">
            <img class="image_link" id="open_control" src="images/open.png" alt="Show menu" title="Show menu" style="float: left; margin: 0; padding: 0; margin-right: 5px; display: none;" onclick="javascript:g.lm.show();" />
            <div id="course_name">
                <!--
                <a href="javascript:void(0);" onclick="javascript:g.print(); $(this).blur();"><img src="images/print.png" alt="Print chart"></a>
                <a href="javascript:void(0);" onclick="javascript:g.save(); $(this).blur();"><img src="images/disk.png" alt="Save chart"></a>
                -->
            </div>
            <div id="title"><!-- Chart title --></div>
        </div>
        <div id="ch_content">
            <div id="error_message">
                <div id="title"></div>
                <p id="message"></p>
            </div>
            <div id="processing">
                <div id="p_img"><img src="images/processing.gif" alt="Processing data" /></div>
                <p id="p_message">Processing data, please wait!</p>
            </div>
            <div id="plot">
                <!-- Chart -->
            </div>
            <div id="welcome_page">
                <h1 align="center">Welcome to LearnTrak</h1>
                <table id="charts_list" cellspacing="0" cellpadding="5" align="center" width="840">
                    <tr>
                        <td class="home-image"><img width="200" height="100" src="images/home/students-accesses-overview_thumb.png" /></td>
                        <td>LearnTrak is a graphical interactive student monitoring and tracking system tool that extracts tracking data from this Learn course, and generates useful graphical representations that can be explored by course instructors to examine various aspects of student engagement. The LearnTrak block is only visible to the instructors of the course.</td>
                        <td class="home-image"><img width="200" height="100" src="images/home/activities-assignments-quizzes_thumb.png" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><p style="margin-top:0;">As a teacher in your Learn course you may wonder what use your students are making of the site: Are they regularly accessing the course? Are they viewing or downloading course materials? Are they participating in activities? Are there students who are over-achieving or under-achieving? This is where LearnTrak can help you.</p><p>LearnTrak generates graphical representations of activity within Learn courses. There are three main categories of visualizations: Students, Resources and Activities.</p></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="home-image" valign="bottom"><img width="200" height="100" src="images/home/students-accesses_thumb.png" /></td>
                        <td class="help" style="width:auto;">
                            <h2>LearnTrak Help Topics</h2>
                            <ul class="contents" style="margin-bottom:0;">
                                <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#overview">Overview</a></li>
                                <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#options">Options</a></li>
                                <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#students-accesses">Students</a>
                                    <ul>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#students-accesses">Accesses by students</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#students-accesses-overview">Accesses overview</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#students-accesses-overview-resources">Accesses overview of resources, books, forums, glossaries or wikis</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#student-details-resources">Student details on resources, books, forums, glossaries or wikis</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#groups">Groups and Groupings of students</a></li>
                                    </ul>
                                </li>
                                <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#resources-students-overview">Resources</a>
                                    <ul>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#resources-students-overview">Students overview</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#resources-accesses-overview">Accesses overview of resources and books</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#resources-details-students">Resource details on students</a></li>
                                    </ul>
                                </li>
                                <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#activities-assignments-quizzes">Activities</a>
                                    <ul>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#activities-assignments-quizzes">Assignments/Quizzes overview</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#activities-forums-accesses-overview">Forums, glossaries or wikis student overview</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#activities-forums-student-overview">Forums, glossaries or wikis accesses overview</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#activities-forums-details-students">Forums, glossaries or wikis details on students</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#activities-summary-student-overview">Summary students overview</a></li>
                                        <li><a href="?q=help&srv_data=<?php echo $srv_data_encoded;?>#activities-summary-accesses-overview">Summary accesses overview</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </td>
                        <td class="home-image" valign="bottom"><img width="200" height="100" src="images/home/resources-access-overview_thumb.png" /></td>
                    </tr>
                </table>
            </div>           
        </div>    
    </div>
</div>