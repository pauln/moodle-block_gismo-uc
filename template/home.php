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
                <h1 align="center">Welcome to GISMO</h1>
                <table id="charts_list" cellspacing="0" cellpadding="5" align="center">
                    <tr>
                        <td>Students: accesses by students</td>
                        <td><img width="200" height="100" src="images/help/students_accesses_by_students_tn.png" /></td>
                    </tr>
                    <tr>
                        <td><img width="200" height="100" src="images/help/students_accesses_overview_tn.png" /></td>
                        <td>Students: accesses overview</td>
                    </tr>
                    <tr>
                        <td>Students: accesses overview on resources</td>
                        <td><img width="200" height="100" src="images/help/students_accesses_overview_on_resources_tn.png" /></td>
                    </tr>
                    <tr>
                        <td><img width="200" height="100" src="images/help/resources_students_overview_tn.png" /></td>
                        <td>Resources: students overview</td> 
                    </tr>
                    <tr>
                        <td>Resources: accesses overview</td>
                        <td><img width="200" height="100" src="images/help/resources_accesses_overview_tn.png" /></td>
                    </tr>
                    <tr>
                        <td><img width="200" height="100" src="images/help/activities_assignments_tn.png" /></td>
                        <td>Activities: assignments overview</td>
                    </tr>
                </table>
            </div>           
        </div>    
    </div>
</div>