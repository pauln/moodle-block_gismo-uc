<?php
    // libraries & acl
    require_once "common.php";
?>
<div id="inner" class="help">
    <h1>LearnTrak Help</h1>
    <h2>Contents</h2>
    <ul class="contents">
        <li><a href="#overview">Overview</a></li>
        <li><a href="#options">Options</a></li>
        <li><a href="#students-accesses">Students</a>
            <ul>
                <li><a href="#students-accesses">Accesses by students</a></li>
                <li><a href="#students-accesses-overview">Accesses overview</a></li>
                <li><a href="#students-accesses-overview-resources">Accesses overview of resources, books, forums, glossaries or wikis</a></li>
                <li><a href="#student-details-resources">Student details on resources, books, forums, glossaries or wikis</a></li>
            </ul>
        </li>
        <li><a href="#resources-students-overview">Resources</a>
            <ul>
                <li><a href="#resources-students-overview">Students overview</a></li>
                <li><a href="#resources-accesses-overview">Accesses overview of resources and books</a></li>
                <li><a href="#resources-details-students">Resource details on students</a></li>
            </ul>
        </li>
        <li><a href="#activities-assignments-quizzes">Activities</a>
            <ul>
                <li><a href="#activities-assignments-quizzes">Assignments/Quizzes overview</a></li>
                <li><a href="#activities-forums-accesses-overview">Forums, glossaries or wikis student overview</a></li>
                <li><a href="#activities-forums-student-overview">Forums, glossaries or wikis accesses overview</a></li>
                <li><a href="#activities-forums-details-students">Forums, glossaries or wikis details on students</a></li>
                <li><a href="#activities-summary-student-overview">Summary students overview</a></li>
                <li><a href="#activities-summary-accesses-overview">Summary accesses overview</a></li>
            </ul>
        </li>
    </ul>
    
    <h2 id="overview">Overview &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/overview.png" width="500" height="330">
    <p>
        There are four areas which make up the LearnTrak user interface:
    </p>
    <ul>
        <li>The dropdown menu navigation bar</li>
        <li>The List Panel: A list of students, resources, or activities in your Learn course will be shown in this panel. From each list the instructor can select or deselect data to visualize, for items chosen using the tick-boxes.</li>
        <li>The Graph Panel: Visualisations are shown in this panel.</li>
        <li>Time Slider: Using this slider the instructor can restrict the graph to display data from a specific range of dates.</li>
    </ul>
    <p>
        By clicking on items from the dropdown menus under the category headings along the menu bar at the top, graphical representations can be viewed with a focus on the student, the resources or the activities within a Learn site.
    </p>
    <p>
        The list of items viewed can be made more specific by using the tick boxes in the list panel. The dates of the information displayed can be made more specific by using the time slider along the bottom of the screen, which can be moved from either end.
    </p>
    <hr class="category" />

    <h2 id="options">Options &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/options.png" width="447" height="375">
    <p>
        There are options available which allow you to customise the way you see LearnTrak, such as including items which have been hidden in your course, or changing the colours in which the graphs are displayed.
    </p>
    <hr class="category" />
    
    <h2 id="students-accesses">Students: Accesses by students &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/students-accesses.png" width="500" height="330">
    <p>
        A simple matrix formed by students' names (on  the Y-axis) and the dates of the course (on the X-axis) is used to represent accesses to the course. A mark on the graph represents a particular student accessing the course at least once on a given date. 
    </p>
    <hr class="section" />
    
    <h2 id="students-accesses-overview">Students: Accesses overview &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/students-accesses-overview.png" width="500" height="330">
    <p>
        A histogram showing the total number of accesses to the course made by students on each date.
    </p>
    <p>
        Coupled with the previous graph, this provides an overview of accesses made by students to the course with a clear identification of patterns and trends. You can also find information about the attendance of a specific student by using the tick boxes in the list panel.
    </p>
    <hr class="section" />
    
    <h2 id="students-accesses-overview-resources">Students: Accesses overview on resources, books, forums, glossaries or wikis &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/students-accesses-overview-resources.png" width="500" height="330">
    <p>
        This graph represents the total number of accesses made by students (on the X-axis) to all the resources (using "Link to a file or web site") in the course (Y-axis).
    </p>
    <p>
        Similar graphs are available for any books, forums, glossaries or wikis which you have in your Learn site. These can be viewed by choosing that item from the "Students" dropdown menu.
    </p>
    <p>
        By clicking on the "eye icon" in the left menu, you can see the details for a specific student. This will be displayed as per the example below.
    </p>
    <hr class="section" />
    
    <h2 id="student-details-resources">Students: Student details on resources, books, forums, glossaries or wikis &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/student-details-resources.png" width="500" height="330">
    <p>
        This graph shows an overview of a particular student's accesses to the course's resources. Dates are represented on the X-axis; resources are represented on the Y-axis.
    </p>
    <hr class="category" />
    
    <h2 id="resources-students-overview">Resources: Students overview &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/resources-students-overview.png" width="500" height="330">
    <p>
        Details of which resources were accessed by each student and when they were accessed are provided in this chart. Student names are shown on the Y-axis, with resource names on the X-axis. A mark is shown if the student has accessed this resource, with the colour of the mark determined by the number of times he/she accessed this resource. The colours range from green to red, or light to dark, depending on the option you have chosen. The actual number can be seen by placing the cursor on the mark. This also shows the maximum activity by a student. 
    </p>
    <hr class="section" />
    
    <h2 id="resources-accesses-overview">Resources: Accesses overview of resources and books &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/resources-accesses-overview.png" width="500" height="330">
    <p>
        This graph depicts the total number of accesses made by all students to each resource of the course (X-axis). Each bar of the histogram represents a particular resource.
    </p>
    <p>
        A similar graph is available for any books which you have in your Learn site. This can be viewed by choosing that item from the "Resources" dropdown menu.
    </p>
    <p>
        By clicking on the "eye icon" in the left menu, you can see the details for a specific resource. This will be displayed as in the example below.
    </p>
    <hr class="section" />
    
    <h2 id="resources-details-students">Resources: Resource details on students &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/resources-details-students.png" width="500" height="330">
    <p>
        This chart provides an overview of students' accesses to this particular resource. Dates are shown on the X-axis; students are shown on the Y-axis.
    </p>
    <hr class="category" />
    
    <h2 id="activities-assignments-quizzes">Activities: Assignments/Quizzes overview &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/activities-assignments-quizzes.png" width="500" height="330">
    <p>
        Students' grades on assignments (or quizzes) are presented in this chart. The Y-axis shows the students, the X-axis shows the assignments (or quizzes in the graphs dedicated to quizzes) in the Learn site, and marks denote students' graded submissions. A mark which only shows an outline depicts a submission which has not been graded, while a coloured mark reports the grade: a lower grade is depicted in red (or a light colour), a high grade is depicted in green (or a dark colour). The actual grade can be seen by placing the cursor on the mark.
    </p>
    <hr class="section" />
    
    <h2 id="activities-forums-accesses-overview">Activities: Forums, glossaries or wikis student overview &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/activities-forums-student-overview.png" width="500" height="330">
    <p>
        This chart is intended to visually indicate the engagement by students with forums. The Y-axis shows the students, while the X-axis shows the forums in the Learn site. Coloured marks denote the number of times a student has viewed or written posts in a forum: a lower number is depicted in red (or a light colour), a high number is depicted in green (or a dark colour). The actual number can be seen by placing the cursor on the mark. This also shows the maximum activity by a student.
    </p>
    <p>
        A similar graph is available for glossaries or wikis which you have in your Learn site. This can be viewed by choosing that item from the "Activities" dropdown menu.
    </p>
    <hr class="section" />
    
    <h2 id="activities-forums-student-overview">Activities: Forums, glossaries or wikis accesses overview &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/activities-forums-accesses-overview.png" width="500" height="330">
    <p>
        This graph represents the total number of accesses made by students to each forum in the course (X-axis). Each bar of the histogram represents a particular forum in the course.
    </p>
    <p>
        A similar graph is available for any glossaries or wikis which you have in your Learn site. This can be viewed by choosing that item from the "Activities" dropdown menu.
    </p>
    <p>
        By clicking on the "eye icon" in the left menu, you can see the details for a specific forum. This will be displayed as in the example below.
    </p>
    <hr class="section" />
    
    <h2 id="activities-forums-details-students">Activities: Forums, glossaries or wikis details on students &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/activities-forums-details-students.png" width="500" height="330">
    <p>
        This chart shows an overview of a particular student's accesses to the course's forums. Dates are shown on the X-axis; forums are shown on the Y-axis. 
    </p>
    <hr class="section" />
    
    <h2 id="activities-summary-student-overview">Activities: Summary students overview &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/activities-summary-student-overview.png" width="500" height="330">
    <p>
        All students' engagement with all forums, glossaries and wikis is aggregated into this one chart. The Y-axis shows the students, the X-axis shows all the forums, glossaries and wikis in the Learn site. Coloured marks denote the number of times a student has engaged with a forum, glossary or wiki: a lower number is depicted in red (or a light colour), a high number is depicted in green (or a dark colour). The actual number can be seen by placing the cursor on the mark. This also shows the maximum activity by a student.
    </p>
    <hr class="section" />
    
    <h2 id="activities-summary-accesses-overview">Activities: Summary accesses overview &nbsp; <a href="#">[Back to top]</a></h2>
    <img src="images/help/activities-summary-accesses-overview.png" width="500" height="330">
    <p>
        This histogram shows the total number of accesses to the forums, glossaries and wikis made by students in the Learn site.  Each bar of the histogram represents a particular forum, glossary or wiki within the course.
    </p>
</div>