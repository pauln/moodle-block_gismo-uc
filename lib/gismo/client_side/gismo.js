function gismo(config, srv_data, static_data, course_start_time, current_time) {
    // html elements ids
    // header
    this.header_id = 'header';
    // content
    this.content_id = 'content';
    this.left_menu_id = 'left_menu';
    this.lm_header_id = 'lm_header';
    this.lm_content_id = 'lm_content';
    this.chart_id = 'chart';
    this.plot_id = 'plot';
    this.error_message_id = 'error_message';
    this.processing_id = 'processing';
    this.ch_header_id = 'ch_header';
    this.ch_content_id = 'ch_content';
    // footer
    this.footer_id = 'footer';
    this.date_slider_id = 'date_slider';
    this.from_date_id = 'from_date';
    this.to_date_id = 'to_date';
    
    // fields
    this.srv_data = srv_data;
    this.static_data = static_data;
    this.course_start_time = course_start_time;
    this.current_time = current_time;
    this.current_analysis = {
        type: null,
        options: null,
        name: null, 
        data: null,
        extra_info: null,
        prepared_data: null,
        plot: null,
        status: false
    };
    this.menu = { 'student-resources-access': ['users', 'resources'],
                  'student-resources-access:users-details': ['resources'],
                  'student-books-access': ['users', 'books'],
                  'student-books-access:users-details': ['books'],
                  'student-forums-access': ['users', 'forums'],
                  'student-forums-access:users-details': ['forums'],
                  'student-glossaries-access': ['users', 'glossaries'],
                  'student-glossaries-access:users-details': ['glossaries'],
                  'student-wikis-access': ['users', 'wikis'],
                  'student-wikis-access:users-details': ['wikis'],
                  'student-accesses': ['users'],
                  'student-accesses-overview': ['users'],
                  'resources-students-overview': ['users', 'resources'],
                  'resources-access': ['users', 'resources'],
                  'resources-access:resources-details': ['users'],
                  'books-students-overview': ['users', 'books'],
                  'books-access': ['users', 'books'],
                  'forums-students-overview': ['users', 'forums'],
                  'forums-access': ['users', 'forums'],
                  'glossaries-students-overview': ['users', 'glossaries'],
                  'glossaries-access': ['users', 'glossaries'],
                  'wikis-students-overview': ['users', 'wikis'],
                  'wikis-access': ['users', 'wikis'],
                  'activitysummary-students-overview': ['users', 'forums', 'glossaries', 'wikis'],
                  'activitysummary-access': ['users', 'forums', 'glossaries', 'wikis'],
                  'assignments': ['users', 'assignments'], 
                  'quizzes': ['users', 'quizzes']};
    this.menu_default = {   'student-resources-access': 0,
                            'student-resources-access:users-details': 0,
                            'student-books-access': 0,
                            'student-books-access:users-details': 0,
                            'student-forums-access': 0,
                            'student-forums-access:users-details': 0,
                            'student-glossaries-access': 0,
                            'student-glossaries-access:users-details': 0,
                            'student-wikis-access': 0,
                            'student-wikis-access:users-details': 0,
                            'student-accesses': 0,
                            'student-accesses-overview': 0,
                            'resources-students-overview': 1,
                            'resources-access': 1,
                            'resources-access:resources-details': 0,
                            'books-students-overview': 1,
                            'books-access': 1,
                            'books-access:books-details': 0,
                            'forums-students-overview': 1,
                            'forums-access': 1,
                            'forums-access:forums-details': 0,
                            'glossaries-students-overview': 1,
                            'glossaries-access': 1,
                            'glossaries-access:glossaries-details': 0,
                            'wikis-students-overview': 1,
                            'wikis-access': 1,
                            'wikis-access:wikis-details': 0,
                            'activitysummary-students-overview': 0,
                            'activitysummary-access': 0,
                            'assignments': 0, 
                            'quizzes': 0};
    this.menu_details = { 'student-resources-access': ['users'],
                          'student-resources-access:users-details': [],
                          'student-books-access': ['users'],
                          'student-books-access:users-details': [],
                          'student-forums-access': ['users'],
                          'student-forums-access:users-details': [],
                          'student-glossaries-access': ['users'],
                          'student-glossaries-access:users-details': [],
                          'student-wikis-access': ['users'],
                          'student-wikis-access:users-details': [],
                          'student-accesses': [],
                          'student-accesses-overview': [],
                          'resources-students-overview': [],
                          'resources-access': ['resources'],
                          'resources-access:resources-details': [],
                          'books-students-overview': [],
                          'books-access': ['books'],
                          'forums-students-overview': [],
                          'forums-access': ['forums'],
                          'glossaries-students-overview': [],
                          'glossaries-access': ['glossaries'],
                          'wikis-students-overview': [],
                          'wikis-access': ['wikis'],
                          'activitysummary-students-overview': [],
                          'activitysummary-access': ['forums', 'glossaries', 'wikis'],
                          'assignments': [], 
                          'quizzes': []};
    
    // resize management
    this.resize_scheduled = false;
    this.last_resize = 0;
    
    // config
    this.cfg = config;
    
    // util
    this.util = new gismo_util(this);
    
    // composite (instances)
    this.tm = new top_menu(this);
    this.lm = new left_menu(this);
    this.cht = null;
    this.tl = new time_line(this);
    
    // init method
    this.init = function () {
        // init top menu
        this.tm.init();
        // init left menu
        this.lm.init();
        // init time line
        this.tl.init();
        // other stuff
        $("#" + this.plot_id).hide();
        $("#" + this.error_message_id).hide();
        $("#" + this.processing_id).hide();
        // set course
        $("#" + this.ch_header_id + " #course_name").html(this.util.intelligent_substring(this.static_data["course_full_name"], true, 100, 5));
    }
    
    // this method return type & subtype combined (used to decide how to prepare date / create chart)
    this.get_full_type = function () {
        var full_type = null;
        if (this.current_analysis.type != null) {
            full_type = this.current_analysis.type;
            if (this.current_analysis.options != null && 
                this.current_analysis.options.subtype != undefined && 
                this.current_analysis.options.subtype != null) {
                full_type = full_type + ":" + this.current_analysis.options.subtype;    
            }    
        }
        return full_type;   
    }
    
    this.days_between = function (iso_date1, iso_date2) {   // date yyyy-mm-dd
        // milliseconds in a day
        var day_ms = 1000 * 60 * 60 * 24;
        var date1 = iso_date1.split("-");
        var date2 = iso_date2.split("-");
        
        // Convert both dates to milliseconds
        var date1_ms = (new Date(date1[0], date1[1], date1[2], 0, 0, 0, 0)).getTime();
        var date2_ms = (new Date(date2[0], date2[1], date2[2], 0, 0, 0, 0)).getTime();

        // Calculate the difference in milliseconds
        var difference_ms = Math.abs(date1_ms - date2_ms);
        
        // Convert back to days and return
        return Math.round(difference_ms / day_ms);

    }
    
    this.show_error = function (message, title) {
        var t = (title == undefined) ? "An error has occurred" : title;
        // hide welcome page
        $("#welcome_page").hide();
        // hide current plot
        $("#" + this.plot_id).hide();
        // hide processing
        $("#" + this.processing_id).hide();
        // empty chart
        $("#" + this.plot_id).empty();
        $("#" + this.plot_id).html("");
        // set error message
        $("#" + this.error_message_id + " #title").html(t);
        $("#" + this.error_message_id + " #message").html(message);
        // show message
        $("#" + this.error_message_id).show();
    }
    
    this.show_processing = function () {
        // hide welcome page
        $("#welcome_page").hide();
        // hide current plot
        $("#" + this.error_message_id).hide();
        // empty chart
        $("#" + this.plot_id).empty();
        $("#" + this.plot_id).html("");
        $("#" + this.plot_id).height(0);
        // show processing
        $("#" + this.processing_id).show();
    }
    
    // analyse method
    this.analyse = function (type, options) {
        // variables
        var response;
        
        // options
        var opt = ""
        if (options != undefined) {
            for (var k in options) {
                opt += "&" + k + "=" + options[k];        
            }
        }
        
        // show processing
        this.show_processing();
        
        // extract data from server
        $.ajax({
            url: 'ajax.php',
            async: false, 
            type: 'POST',
            data: 'q=' + type + opt + '&srv_data=' + this.srv_data + '&from=' + this.tl.get_from(true) + '&to=' + this.tl.get_to(true) + '&token=' + Math.random(), 
            dataType: 'json',
            success: 
                function(json) {
                    response = json;
                },
            error:
                function(error) {
                    response = {error: '1', message: 'Cannot extract data from server!'};    
                }
        });
        
        // check response for errors
        if (response['error'] != undefined && response['error'] == '1') {
            if (response['message'] != undefined) {
                this.show_error(response['message']);
            } else {
                this.show_error('Unknown error!');    
            }    
        } else {
            // save data
            this.current_analysis.type = type;
            this.current_analysis.options = (options != undefined) ? options : null;
            this.current_analysis.name = response.name;
            this.current_analysis.data = response.data;
            this.current_analysis.extra_info = response.extra_info;
            
            // show / hide menus
            this.lm.set_menu(true);
            
            // show / hide details controls
            this.lm.init_lm_content_details();
            
            // draw chart
            this.create_chart();    
        }
    }
    
    // get color
    this.get_color = function (secondary_channels_colors) {
        var tmp;
        // color
        switch (this.cfg.chart_base_color) {
            case 1:
                tmp = "#ff" + secondary_channels_colors + secondary_channels_colors; 
                break;
            case 2:
                tmp = "#" + secondary_channels_colors + "ff" + secondary_channels_colors; 
                break;
            case 3:
            default:
                tmp = "#" + secondary_channels_colors + secondary_channels_colors + "ff"; 
                break;
        }
        // return color
        return tmp;        
    }

    // get series colors (for matrix)
    this.get_series_colors = function (num_series, base_color) {
        var colors = new Array();
        var tmp;
        if(this.cfg.chart_base_color==4) {
            return this.get_series_colors_g2r();
        }
        for (var k=0; k<this.cfg.matrix_num_series_limit; k++) {
            // build non base channel value
            if (k > 0) {
                tmp = (256 - Math.floor((parseFloat(k) / parseFloat((this.cfg.matrix_num_series_limit - 1))) * 256)).toString(16);
                while (tmp.length < 2) {
                    tmp = "0" + tmp;
                }
            } else {
                tmp = "00";
            }
            // store color
            colors[k] = this.get_color(tmp);
        }
        // return colors
        return colors;
    }

    // get color - green to red
    this.get_color_g2r = function (green, red) {
        // return color
        return "#" + red + green + "00";
    }

    // get series colors (for matrix) - green to red
    this.get_series_colors_g2r = function () {
        var colors = new Array();
        var tmp, red, green, rtmp;
        for (var k=0; k<this.cfg.matrix_num_series_limit; k++) {
            // build non base channel value
            if (k > 0) {
                tmp = (256 - Math.floor((parseFloat(k) / parseFloat((this.cfg.matrix_num_series_limit - 1))) * 256));
                rtmp = tmp;
                if (rtmp > 192) {
                    rtmp = Math.min(204+(256-rtmp), 255);
                    tmp = 256;
                } else {
                    if (rtmp > 64) rtmp=255;
                    if (rtmp <=64 && rtmp > 0) rtmp += 128;
                }
                red = rtmp.toString(16);
                while (red.length < 2) {
                    red = "0" + red;
                }
                tmp = 256 - tmp;
                if(tmp>=256) tmp=255;
                //if(tmp<=64) tmp=0;
                green = tmp.toString(16);
                while (green.length < 2) {
                    green = "0" + green;
                }
            } else {
                red = "ff";
                green = "00";
            }
            // store color
            colors[k] = this.get_color_g2r(green, red);
        }
        // return colors
        return colors;
    }
    
    // prepare data
    this.prepare_data = function () {
        // get selected items
        var selected_items = this.lm.get_selected_items();
        var prepared_data = new Array();
        var lines = new Array();
        var genseries = new Array();
        var xticks = new Array();
        var xticks_pos = new Array();
        var yticks = new Array();
        var yticks_pos = new Array();
        var item = null, num_serie = 0, count, key, tmp, k, colors, index, used_lines, used_genseries;

        // build chart
        switch (this.get_full_type()) {
            case 'student-accesses':
                if (this.static_data["users"].length > 0 && this.util.get_assoc_array_length(this.current_analysis.data) > 0) {
                    // yticks
                    count = 0;
                    for (item in this.static_data["users"]) {
                        if (jQuery.inArray((this.static_data["users"][item].id).toString(), selected_items["users"]) != -1) {
                            count++;
                        }    
                    }
                    for (item in this.static_data["users"]) {
                        if (jQuery.inArray((this.static_data["users"][item].id).toString(), selected_items["users"]) != -1) {
                            yticks.unshift(this.util.intelligent_substring(this.static_data["users"][item].name, false));
                            yticks_pos[this.static_data["users"][item].id] = count;
                            count--;
                        }    
                    }
                    // build line
                    for (item in this.current_analysis.data) {                        
                        if (jQuery.inArray((this.current_analysis.data[item].userid_log).toString(), selected_items["users"]) != -1) {
                            lines.push(new Array(this.current_analysis.data[item].date_log, yticks_pos[this.current_analysis.data[item].userid_log], this.current_analysis.data[item].count_log));      
                        }
                    }
                    // set prepared data (at least on resource must have been selected)
                    if (lines.length > 0 && yticks.length > 0) {
                        prepared_data["lines"] = lines;
                        prepared_data["yticks"] = yticks;
                        prepared_data["min_date"] = this.current_analysis.extra_info.min_date;
                        prepared_data["max_date"] = this.current_analysis.extra_info.max_date;
                        prepared_data["xticks_num"] = this.current_analysis.extra_info.num_days;
                        prepared_data["xticks_min_len"] = 5;
                        prepared_data["yticks_num"] = yticks.length;
                        prepared_data["yticks_min_len"] = 13;
                        prepared_data["x_label"] = "Timeline";
                        prepared_data["y_label"] = "Accesses";
                    }       
                } 
                break;
            case 'student-accesses-overview':
                if (this.static_data["users"].length > 0 && this.util.get_assoc_array_length(this.current_analysis.data) > 0) {
                    var date = null;
                    tmp = new Array();
                    // build line
                    for (item in this.current_analysis.data) {                        
                        if (jQuery.inArray((this.current_analysis.data[item].userid_log).toString(), selected_items["users"]) != -1) {
                            // sum user contribute if date already in the list, add new entry otherwise
                            date = this.current_analysis.data[item].date_log;
                            count = this.current_analysis.data[item].count_log;
                            if (tmp[date] == undefined) {
                                tmp[date] = new Array(date, parseInt(count));
                            } else {
                                tmp[date][1] += parseInt(count);
                            }                            
                        }
                    }
                    // assoc to normal array
                    if (this.util.get_assoc_array_length(tmp) > 0) {
                        for (item in tmp) {
                            lines.push(tmp[item]);
                        }        
                    }
                    // set prepared data (at least on resource must have been selected)
                    if (lines.length > 0) {
                        prepared_data["lines"] = lines;
                        prepared_data["min_date"] = this.current_analysis.extra_info.min_date;
                        prepared_data["max_date"] = this.current_analysis.extra_info.max_date;
                        prepared_data["xticks_num"] = this.current_analysis.extra_info.num_days;
                        prepared_data["xticks_min_len"] = 5;
                        prepared_data["x_label"] = "Timeline";
                        prepared_data["y_label"] = "Accesses";
                    }      
                } 
                break;
            case 'student-resources-access':
            case 'student-books-access':
            case 'student-forums-access':
            case 'student-glossaries-access':
            case 'student-wikis-access':
                var this_type = this.get_full_type().split('-')[1];
                if (this.static_data["users"].length > 0 && this.util.get_assoc_array_length(this.current_analysis.data) > 0) {
                    // init (set value 0 for each course student that is selected in the left menu)
                    for (item in this.static_data["users"]) {
                        if (jQuery.inArray((this.static_data["users"][item].id).toString(), selected_items["users"]) != -1) {
                            lines.push(0);
                            xticks.push(this.util.intelligent_substring(this.static_data["users"][item].name, false));
                            yticks.push((this.static_data["users"][item].id).toString());
                        }
                    }
                    // sum contributes for each resource that is selected in the left menu
                    for (item in this.current_analysis.data) {
                        if (jQuery.inArray(this.current_analysis.data[item].resid_sra, selected_items[this_type]) != -1) {
                            if (jQuery.inArray(this.current_analysis.data[item].userid_sra, yticks) != -1) {
                                index = jQuery.inArray(this.current_analysis.data[item].userid_sra, yticks);
                                lines[index] += parseInt(this.current_analysis.data[item].count_sra);    
                            }        
                        }    
                    }
                    // set prepared data (at least on resource must have been selected)
                    if (lines.length > 0 && xticks.length > 0) {
                        prepared_data["lines"] = lines;
                        prepared_data["xticks"] = xticks;
                        prepared_data["xticks_num"] = xticks.length;
                        prepared_data["xticks_min_len"] = 15;
                        prepared_data["x_label"] = "Students";
                        prepared_data["y_label"] = "Accesses on "+this_type;
                    }
                }   
                break;
            case 'student-resources-access:users-details':
            case 'student-books-access:users-details':
            case 'student-forums-access:users-details':
            case 'student-glossaries-access:users-details':
            case 'student-wikis-access:users-details':
                var this_type = this.get_full_type().split('-')[1];
                if (this.static_data[this_type].length > 0 && this.util.get_assoc_array_length(this.current_analysis.data) > 0) {
                    // yticks
                    count = 1;
                    for (item in this.static_data[this_type]) {
                        if (jQuery.inArray((this.static_data[this_type][item].id).toString(), selected_items[this_type]) != -1) {
                            yticks.push(this.util.intelligent_substring(this.static_data[this_type][item].name, true));
                            yticks_pos[this.static_data[this_type][item].id] = count;
                            count++;
                        }    
                    }
                    // build line
                    for (item in this.current_analysis.data) {                        
                        if (jQuery.inArray((this.current_analysis.data[item].resid_sra).toString(), selected_items[this_type]) != -1) {
                            lines.push(new Array(this.current_analysis.data[item].date_sra, yticks_pos[this.current_analysis.data[item].resid_sra], this.current_analysis.data[item].count_sra));        
                        }
                    }
                    // set prepared data (at least on resource must have been selected)
                    if (lines.length > 0 && yticks.length > 0) {
                        prepared_data["lines"] = lines;
                        prepared_data["yticks"] = yticks;
                        prepared_data["min_date"] = this.current_analysis.extra_info.min_date;
                        prepared_data["max_date"] = this.current_analysis.extra_info.max_date;
                        prepared_data["xticks_num"] = this.current_analysis.extra_info.num_days;
                        prepared_data["xticks_min_len"] = 5;
                        prepared_data["yticks_num"] = yticks.length;
                        prepared_data["yticks_min_len"] = 13;
                        prepared_data["x_label"] = "Timeline";
                        prepared_data["y_label"] = (this_type=='books')?"Books":(this_type=='forums')?"Forums":(this_type=='glossaries')?"Glossaries":(this_type=='wikis')?"Wikis":"Resources";
                    }       
                }
                break;
            case 'resources-students-overview':
            case 'books-students-overview':
            case 'forums-students-overview':
            case 'glossaries-students-overview':
            case 'wikis-students-overview':
            case 'activitysummary-students-overview':
                var this_type = this.get_full_type().split('-')[0];
                if (this.static_data["users"].length > 0 && this.static_data[this_type].length > 0 && this.util.get_assoc_array_length(this.current_analysis.data) > 0) {
                    var userid, resid, val;
                    var max;
                    var real_type, real_types = new Array();
                    // xticks / yticks
                    count = 1;
                    for (item in this.static_data[this_type]) {
                        real_type = this.static_data[this_type][item].type;
                        real_types[this.static_data[this_type][item].id] = real_type;
                        if (jQuery.inArray((this.static_data[this_type][item].id).toString(), selected_items[real_type]) != -1) {
                            xticks.push(this.util.intelligent_substring(this.static_data[this_type][item].name, true));
                            xticks_pos[this.static_data[this_type][item].id] = count;
                            count++;
                        }
                    }
                    count = 0;
                    for (item in this.static_data["users"]) {
                        if (jQuery.inArray((this.static_data["users"][item].id).toString(), selected_items["users"]) != -1) {
                            count++;
                        }
                    }
                    for (item in this.static_data["users"]) {
                        if (jQuery.inArray((this.static_data["users"][item].id).toString(), selected_items["users"]) != -1) {
                            yticks.unshift(this.util.intelligent_substring(this.static_data["users"][item].name, false));
                            yticks_pos[this.static_data["users"][item].id] = count;
                            count--;
                        }
                    }
                    // aggregate data (keep only selected users / resources)
                    var aggregated_data = new Array();
                    for (item in this.current_analysis.data) {
                        userid = this.current_analysis.data[item].userid_rac;
                        resid = this.current_analysis.data[item].idresource_rac;
                        real_type = real_types[resid];
                        val = parseInt(this.current_analysis.data[item].count_rac);
                        key = userid + "_" + resid;
                        if (jQuery.inArray((userid).toString(), selected_items["users"]) != -1 &&
                            jQuery.inArray((resid).toString(), selected_items[real_type]) != -1) {
                            if (aggregated_data[key] == undefined) {
                                aggregated_data[key] = new Array();
                                aggregated_data[key].push(val);
                            } else {
                                aggregated_data[key] = parseInt(aggregated_data[key]) + val;
                            }
                        }
                    }
                    // max = Math.max.apply(Math, this.util.array_values(aggregated_data));
                    max = this.current_analysis.extra_info.max_value;   // MAX MUST BE ALWAYS THE SAME (MUST NOT DEPEND ON TIME RANGE)
                    // generate series
                    colors = this.get_series_colors();
                    for (item in aggregated_data) {
                        // evaluate serie
                        num_serie = Math.round(parseFloat(aggregated_data[item])/parseFloat(max)*(this.cfg.matrix_num_series_limit - 2)) + 1;
                        // userid & resid
                        tmp = item.split("_");
                        userid = parseInt(tmp[0]);
                        resid = parseInt(tmp[1]);
                        // lines
                        if (lines[num_serie] == undefined) {
                            lines[num_serie] = new Array();
                            genseries[num_serie] = { color: colors[num_serie], markerOptions:{style: "filledSquare" }};
                        }
                        lines[num_serie].push(new Array(xticks_pos[resid],
                                                       yticks_pos[userid],
                                                       aggregated_data[item],
                                                       max));
                    }
                    // keep only used lines
                    used_lines = new Array();
                    used_genseries = new Array();
                    for (k=0; k<this.cfg.matrix_num_series_limit;k++) {
                        if (lines[k] != undefined && genseries[k] != undefined) {
                            used_lines.push(lines[k]);
                            used_genseries.push(genseries[k]);
                        }
                    }
                    // set prepared data (at least on resource must have been selected)
                    if (used_lines.length > 0 && xticks.length > 0) {
                        prepared_data["lines"] = used_lines;
                        prepared_data["genseries"] = used_genseries;
                        prepared_data["xticks"] = xticks;
                        prepared_data["yticks"] = yticks;
                        prepared_data["xticks_num"] = xticks.length;
                        prepared_data["xticks_min_len"] = 18;
                        prepared_data["yticks_num"] = yticks.length;
                        prepared_data["yticks_min_len"] = 18;
                        prepared_data["x_label"] = (this_type=='books')?"Books":(this_type=='forums')?"Forums":(this_type=='glossaries')?"Glossaries":(this_type=='wikis')?"Wikis":(this_type=='activitysummary')?"Activities":"Resources";
                        prepared_data["y_label"] = "Students";
                    }
                }
                break;
            case 'resources-access':
            case 'books-access':
            case 'forums-access':
            case 'glossaries-access':
            case 'wikis-access':
            case 'activitysummary-access':
                var this_type = this.get_full_type().split('-')[0];
                if (this.static_data[this_type].length > 0 && this.util.get_assoc_array_length(this.current_analysis.data) > 0) {
                    // init (set value 0 for each course resource that is selected in the left menu)
                    for (item in this.static_data[this_type]) {
                        var real_type = this.static_data[this_type][item].type;
                        if (jQuery.inArray((this.static_data[this_type][item].id).toString(), selected_items[real_type]) != -1) {
                            lines.push(0);
                            xticks.push(this.util.intelligent_substring(this.static_data[this_type][item].name, true));
                            yticks.push((this.static_data[this_type][item].id).toString());
                        }
                    }
                    // sum contributes for each user that is selected in the left menu
                    for (item in this.current_analysis.data) {
                        if (jQuery.inArray(this.current_analysis.data[item].userid_rac, selected_items["users"]) != -1) {
                            if (jQuery.inArray(this.current_analysis.data[item].idresource_rac, yticks) != -1) {
                                index = jQuery.inArray(this.current_analysis.data[item].idresource_rac, yticks);
                                lines[index] += parseInt(this.current_analysis.data[item].count_rac);    
                            }        
                        }    
                    }
                    // set prepared data (at least on resource must have been selected)
                    if (lines.length > 0 && xticks.length > 0) {
                        prepared_data["lines"] = lines;
                        prepared_data["xticks"] = xticks;
                        prepared_data["xticks_num"] = xticks.length;
                        prepared_data["xticks_min_len"] = 15;
                        prepared_data["x_label"] = (this_type=='books')?"Books":(this_type=='forums')?"Forums":(this_type=='glossaries')?"Glossaries":(this_type=='wikis')?"Wikis":(this_type=='activitysummary')?"Activities":"Resources";
                        prepared_data["y_label"] = "Accesses";
                    }
                }
                break;
            case 'resources-access:resources-details':
            case 'books-access:books-details':
            case 'forums-access:forums-details':
            case 'glossaries-access:glossaries-details':
            case 'wikis-access:wikis-details':
            case 'activitysummary-access:forums-details':
            case 'activitysummary-access:glossaries-details':
            case 'activitysummary-access:wikis-details':
                if (this.static_data["users"].length > 0 && this.util.get_assoc_array_length(this.current_analysis.data) > 0) {
                    // yticks
                    count = 1;
                    for (item in this.static_data["users"]) {
                        if (jQuery.inArray((this.static_data["users"][item].id).toString(), selected_items["users"]) != -1) {
                            yticks.push(this.util.intelligent_substring(this.static_data["users"][item].name, false));
                            yticks_pos[this.static_data["users"][item].id] = count;
                            count++;
                        }    
                    }
                    // build line
                    for (item in this.current_analysis.data) {                        
                        if (jQuery.inArray((this.current_analysis.data[item].userid_rac).toString(), selected_items["users"]) != -1) {
                            lines.push(new Array(this.current_analysis.data[item].date_rac, yticks_pos[this.current_analysis.data[item].userid_rac], this.current_analysis.data[item].count_rac));      
                        }
                    }
                    // set prepared data (at least on resource must have been selected)
                    if (lines.length > 0 && yticks.length > 0) {
                        prepared_data["lines"] = lines;
                        prepared_data["yticks"] = yticks;
                        prepared_data["min_date"] = this.current_analysis.extra_info.min_date;
                        prepared_data["max_date"] = this.current_analysis.extra_info.max_date;
                        prepared_data["xticks_num"] = this.current_analysis.extra_info.num_days;
                        prepared_data["xticks_min_len"] = 5;
                        prepared_data["yticks_num"] = yticks.length;
                        prepared_data["yticks_min_len"] = 13;
                        prepared_data["x_label"] = "Timeline";
                        prepared_data["y_label"] = "Students";
                    }       
                }
                break;
            case 'assignments':
                if (this.static_data["users"].length > 0 && this.static_data["assignments"].length > 0 && this.util.get_assoc_array_length(this.current_analysis.data) > 0) {
                    // xticks / yticks
                    count = 1;
                    for (item in this.static_data["users"]) {
                        if (jQuery.inArray((this.static_data["users"][item].id).toString(), selected_items["users"]) != -1) {
                            xticks.push(this.util.intelligent_substring(this.static_data["users"][item].name, false));
                            xticks_pos[this.static_data["users"][item].id] = count;
                            count++;
                        }    
                    }
                    count = 0;
                    for (item in this.static_data["assignments"]) {
                        if (jQuery.inArray((this.static_data["assignments"][item].id).toString(), selected_items["assignments"]) != -1) {
                            count++;
                        }       
                    }
                    for (item in this.static_data["assignments"]) {
                        if (jQuery.inArray((this.static_data["assignments"][item].id).toString(), selected_items["assignments"]) != -1) {
                            yticks.unshift(this.util.intelligent_substring(this.static_data["assignments"][item].name, true));
                            yticks_pos[this.static_data["assignments"][item].id] = count;
                            count--;
                        }       
                    }
                    // generate series only for selected users / assignments
                    colors = this.get_series_colors();
                    for (item in this.current_analysis.data) {                        
                        if (jQuery.inArray((this.current_analysis.data[item].userid).toString(), selected_items["users"]) != -1 &&
                            jQuery.inArray((this.current_analysis.data[item].test_id).toString(), selected_items["assignments"]) != -1) {
                            
                            // evaluate serie
                            num_serie = 0;
                            if ((parseInt(this.current_analysis.data[item].user_grade) != -1) && (parseInt(this.current_analysis.data[item].test_max_grade) != -2)) {
                                num_serie = Math.round(parseFloat(this.current_analysis.data[item].user_grade)/parseFloat(this.current_analysis.data[item].test_max_grade)*(this.cfg.matrix_num_series_limit - 2)) + 1;
                            }
                            
                            // lines
                            if (lines[num_serie] == undefined) {
                                lines[num_serie] = new Array();
                                genseries[num_serie] = { color: colors[num_serie], markerOptions:{style: (num_serie != 0) ? "filledSquare" : "square" }};
                            }
                            lines[num_serie].push(new Array(xticks_pos[this.current_analysis.data[item].userid], 
                                                           yticks_pos[this.current_analysis.data[item].test_id],
                                                           (parseInt(this.current_analysis.data[item].user_grade) == -1) ? "NA" : this.current_analysis.data[item].user_grade,
                                                           this.current_analysis.data[item].test_max_grade));       
                        }    
                    }
                    // keep only used lines
                    used_lines = new Array();
                    used_genseries = new Array();
                    for (k=0; k<this.cfg.matrix_num_series_limit;k++) {
                        if (lines[k] != undefined && genseries[k] != undefined) {
                            used_lines.push(lines[k]);
                            used_genseries.push(genseries[k]);   
                        }
                    }
                    
                    // set prepared data (at least on resource must have been selected)
                    if (used_lines.length > 0 && xticks.length > 0) {
                        prepared_data["lines"] = used_lines;
                        prepared_data["genseries"] = used_genseries;
                        prepared_data["xticks"] = xticks;
                        prepared_data["yticks"] = yticks;
                        prepared_data["xticks_num"] = xticks.length;
                        prepared_data["xticks_min_len"] = 18;
                        prepared_data["yticks_num"] = yticks.length;
                        prepared_data["yticks_min_len"] = 18;
                        prepared_data["x_label"] = "Students";
                        prepared_data["y_label"] = "Assignments";
                    }       
                }
                break;
            case 'quizzes':
                if (this.static_data["users"].length > 0 && this.static_data["quizzes"].length > 0 && this.util.get_assoc_array_length(this.current_analysis.data) > 0) {
                    // xticks / yticks
                    count = 1;
                    for (item in this.static_data["users"]) {
                        if (jQuery.inArray((this.static_data["users"][item].id).toString(), selected_items["users"]) != -1) {
                            xticks.push(this.util.intelligent_substring(this.static_data["users"][item].name, false));
                            xticks_pos[this.static_data["users"][item].id] = count;
                            count++;
                        }    
                    }
                    count = 1;
                    for (item in this.static_data["quizzes"]) {
                        if (jQuery.inArray((this.static_data["quizzes"][item].id).toString(), selected_items["quizzes"]) != -1) {
                            yticks.push(this.util.intelligent_substring(this.static_data["quizzes"][item].name, true));
                            yticks_pos[this.static_data["quizzes"][item].id] = count;
                            count++;
                        }       
                    }
                    // generate series only for selected users / quizzes
                    colors = this.get_series_colors();
                    for (item in this.current_analysis.data) {                        
                        if (jQuery.inArray((this.current_analysis.data[item].userid).toString(), selected_items["users"]) != -1 &&
                            jQuery.inArray((this.current_analysis.data[item].test_id).toString(), selected_items["quizzes"]) != -1) {
                            
                            // evaluate serie
                            num_serie = 0;
                            if ((parseInt(this.current_analysis.data[item].user_grade) != -1)) { 
                                num_serie = Math.round(parseFloat(this.current_analysis.data[item].user_grade)/parseFloat(this.current_analysis.data[item].test_max_grade)*(this.cfg.matrix_num_series_limit - 2)) + 1;
                            }
                            
                            // lines
                            if (lines[num_serie] == undefined) {
                                lines[num_serie] = new Array();
                                genseries[num_serie] = { color: colors[num_serie], markerOptions:{style: (num_serie != 0) ? "filledSquare" : "square" }};
                            }
                            lines[num_serie].push(new Array(xticks_pos[this.current_analysis.data[item].userid], 
                                                           yticks_pos[this.current_analysis.data[item].test_id],
                                                           (parseInt(this.current_analysis.data[item].user_grade) == -1) ? "NA" : this.current_analysis.data[item].user_grade,
                                                           this.current_analysis.data[item].test_max_grade));       
                        }    
                    }
                    // keep only used lines
                    used_lines = new Array();
                    used_genseries = new Array();
                    for (k=0; k<this.cfg.matrix_num_series_limit;k++) {
                        if (lines[k] != undefined && genseries[k] != undefined) {
                            used_lines.push(lines[k]);
                            used_genseries.push(genseries[k]);   
                        }
                    }
                    
                    // set prepared data (at least on resource must have been selected)
                    if (used_lines.length > 0 && xticks.length > 0) {
                        prepared_data["lines"] = used_lines;
                        prepared_data["genseries"] = used_genseries;
                        prepared_data["xticks"] = xticks;
                        prepared_data["yticks"] = yticks;
                        prepared_data["xticks_num"] = xticks.length;
                        prepared_data["xticks_min_len"] = 18;
                        prepared_data["yticks_num"] = yticks.length;
                        prepared_data["yticks_min_len"] = 18;
                        prepared_data["x_label"] = "Students";
                        prepared_data["y_label"] = "Quizzes";
                    }       
                }
                break;        
        }
        
        // save prepared data
        this.current_analysis.prepared_data = prepared_data;    
    }
                
    // this method sets the correct plot area width and height
    this.set_plot_dimensions = function () {
        if (this.current_analysis.status == true) {
            // width
            var visible_width = $("body").width() - $("#" + this.left_menu_id).width() - 40;
            var w = visible_width;
            if (this.current_analysis.prepared_data.xticks_num != undefined) {
                var plot_width = $("#" + this.plot_id).width();
                var required_width = this.current_analysis.prepared_data.xticks_min_len*this.current_analysis.prepared_data.xticks_num + 50;
                w = (required_width < visible_width) ? visible_width : required_width;
                $("#" + this.plot_id).width(w);    
            }
            $("#" + this.plot_id).width(w);
            // height
            var visible_height = $("body").height() - $("#" + this.header_id).height() - $("#" + this.ch_header_id).height() - $("#" + this.footer_id).height() - 40;
            // var visible_height = $("#" + this.chart_id).height() - $("#" + this.ch_header_id).height() - 40;
            var h = visible_height;
            if (this.current_analysis.prepared_data.yticks_num != undefined) {
                var plot_height = $("#" + this.plot_id).height();
                var required_height = this.current_analysis.prepared_data.yticks_min_len*this.current_analysis.prepared_data.yticks_num + 50;
                h = (required_height < visible_height) ? visible_height : required_height;    
            }
            $("#" + this.plot_id).height(h);
        }    
    }
    
    // this method retrieve number of pixel available for matrix entry
    this.get_matrix_entry_side_pixels = function () {
        var num_pixel = 12;
        if (this.current_analysis.status == true) {
            // evaluate number of pixels
            var w = parseInt(parseFloat($("#" + this.plot_id).width() - 200.0) / this.current_analysis.prepared_data.xticks.length) - 4.0;
            var h = parseInt(parseFloat($("#" + this.plot_id).height() - 200.0)/ this.current_analysis.prepared_data.yticks.length) - 4.0;
            num_pixel = (w < h) ? w : h;
            // check against minimum
            /*
            var min_w = parseFloat(this.current_analysis.prepared_data.xticks_min_len) - 4.0;
            var min_h = parseFloat(this.current_analysis.prepared_data.yticks_min_len) - 4.0;
            num_pixel = (num_pixel < min_w) ? min_w : num_pixel;
            num_pixel = (num_pixel < min_h) ? min_h : num_pixel;
            */    
        }
        return num_pixel;    
    }
    
    // create chart method
    this.create_chart = function () {
        // prepare data
        this.prepare_data();
        var data = this.current_analysis.prepared_data;
        // empty chart
        $("#" + this.plot_id).empty();
        $("#" + this.plot_id).html("");
        // set title
        $("#" + this.ch_header_id + " #title").html(this.current_analysis.name);
        // check data
        if (data == undefined || data == null || !(this.util.get_assoc_array_length(data) > 0)) {
            // update status
            this.current_analysis.status = false;
            // show error
            this.show_error("Cannot proceed with the analysis beacuse there isn't any data to work on!", "Missing data");       
        } else {
            // update status
            this.current_analysis.status = true;
            // set plot dimensions
            this.set_plot_dimensions();
            // hide welcome page
            $("#welcome_page").hide();
            // hide message
            $("#" + this.error_message_id).hide();
            // hide processing
            $("#" + this.processing_id).hide();
            // show current plot
            $("#" + this.plot_id).show();
            // build chart
            switch (this.get_full_type()) {
                case 'student-accesses':
                case 'student-resources-access:users-details':
                case 'student-books-access:users-details':
                case 'student-forums-access:users-details':
                case 'student-glossaries-access:users-details':
                case 'student-wikis-access:users-details':
                case 'resources-access:resources-details':
                case 'books-access:books-details':
                case 'forums-access:forums-details':
                case 'glossaries-access:glossaries-details':
                case 'wikis-access:wikis-details':
                case 'activitysummary-access:forums-details':
                case 'activitysummary-access:glossaries-details':
                case 'activitysummary-access:wikis-details':
                    this.current_analysis.plot = $.jqplot(this.plot_id, [data.lines], {
                        axes:{
                            xaxis:{
                                renderer:$.jqplot.DateAxisRenderer, 
                                min: data.min_date,
                                max: data.max_date,
                                label: data.x_label,
                                labelRenderer: $.jqplot.CanvasAxisLabelRenderer, 
                                tickInterval: '1 month',
                                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                                tickOptions: {
                                    formatString:'%#d %b %Y'
                                    /*, angle: -90 */
                                },
                                autoscale: false
                            },
                            yaxis: {
                                autoscale: true,
                                labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                                ticks: data.yticks,
                                label: data.y_label,
                                renderer: $.jqplot.CategoryAxisRenderer,
                                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                                tickOptions: {
                                    formatString: '<nobr>%s</nobr>'
                                }
                            }
                        },
                        seriesDefaults: {
                            pointLabels: { show: false },
                            color: this.get_color("00"),
                            showLine: false,  
                            markerOptions:{style:'filledSquare', size:3, shadow: false }
                        },
                        highlighter: {
                            tooltipAxes: 'xy',
                            tooltipFade: false, 
                            yvalues: 2, 
                            useAxesFormatters: true, 
                            sizeAdjust: 4.5, 
                            formatString:'<table class="jqplot-highlighter"><tr><td>%s, <span class="hidden">%s</span>%s accesses</td></tr></table>'
                        }
                    });
                    break;
                case 'student-accesses-overview':
                    this.current_analysis.plot = $.jqplot(this.plot_id, [data.lines], {
                        axes:{
                            xaxis:{
                                renderer:$.jqplot.DateAxisRenderer, 
                                min: data.min_date,
                                max: data.max_date,
                                label: data.x_label,
                                labelRenderer: $.jqplot.CanvasAxisLabelRenderer, 
                                tickInterval: '1 month',
                                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                                tickOptions: {
                                    formatString:'%#d %b %Y'
                                    /* , angle: -90 */
                                },
                                autoscale: false
                            },
                            yaxis: {
                              min: 0,
                              autoscale: true,
                              label: data.y_label,
                              labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                              tickOptions: {
                                formatString: '%d'
                              }
                            }
                        },
                        seriesDefaults: {
                            renderer:$.jqplot.BarRenderer,
                            rendererOptions:{
                                barPadding: 0,
                                barMargin: 3,
                                barWidth: 2
                            },
                            showMarker:false,
                            /*
                            pointLabels: {
                                hideZeros: true,
                                ypadding: 2,
                                labelsFromSeries: true
                            },
                            */
                            pointLabels: { show: false },
                            color: this.get_color("00"),
                            shadow: false
                        },
                        /*
                        highlighter: { show: false }
                        */
                        highlighter: {} 
                    });
                    break;
                case 'student-resources-access':
                case 'student-books-access':
                case 'student-forums-access':
                case 'student-glossaries-access':
                case 'student-wikis-access':
                    this.current_analysis.plot = $.jqplot(this.plot_id, [data.lines], {
                      seriesDefaults: {
                        renderer:$.jqplot.BarRenderer,
                        rendererOptions:{
                            barPadding: 0,
                            barMargin: 3
                            /* barWidth: 8 */
                        },
                        showMarker:false,
                        pointLabels: {
                            hideZeros: true,
                            ypadding: 2,
                            labelsFromSeries: true
                        },
                        color: this.get_color("00"),
                        shadow: false
                      },
                      axes: {
                        xaxis: {
                          renderer: $.jqplot.CategoryAxisRenderer,
                          label: data.x_label,
                          labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                          tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                          ticks: data.xticks,
                          tickOptions: {
                            angle: -90
                          }
                        },
                        yaxis: {
                          min: 0,
                          autoscale: true,
                          label: data.y_label,
                          labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                          tickOptions: {
                            formatString: '%d'
                          }
                        }
                      },
                      highlighter: { show: false }
                    });
                    break;
                case 'resources-access':
                case 'books-access':
                case 'forums-access':
                case 'glossaries-access':
                case 'wikis-access':
                case 'activitysummary-access':
                    this.current_analysis.plot = $.jqplot(this.plot_id, [data.lines], {
                      seriesDefaults: {
                        renderer:$.jqplot.BarRenderer,
                        rendererOptions:{
                            barPadding: 0,
                            barMargin: 3
                            /* barWidth: 8 */
                        },
                        showMarker:false,
                        pointLabels: {
                            hideZeros: true,
                            ypadding: 2,
                            labelsFromSeries: true
                        },
                        color: this.get_color("00"),
                        shadow: false
                      },
                      axes: {
                        xaxis: {
                          renderer: $.jqplot.CategoryAxisRenderer,
                          label: data.x_label,
                          labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                          tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                          ticks: data.xticks,
                          tickOptions: {
                            angle: -90
                          }
                        },
                        yaxis: {
                          min: 0,
                          autoscale: true,
                          label: data.y_label,
                          labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                          tickOptions: {
                            formatString: '%d'
                          }
                        }
                      },
                      highlighter: { show: false }
                    });
                    break;
                case 'resources-students-overview':
                case 'books-students-overview':
                case 'forums-students-overview':
                case 'glossaries-students-overview':
                case 'wikis-students-overview':
                case 'activitysummary-students-overview':
                case 'assignments':
                case 'quizzes':
                    var msize = this.get_matrix_entry_side_pixels();
                    var formatString;
                    // highlight templates
                    switch (this.get_full_type()) {
                        case "resources-students-overview":
                        case "books-students-overview":
                        case "forums-students-overview":
                        case "glossaries-students-overview":
                        case "wikis-students-overview":
                        case "activitysummary-students-overview":
                            formatString = '<table class="jqplot-highlighter"><tr style="display:none; visibility:hidden;"><td>hidden:</td><td>%s</td></tr><tr><td>%s</td><td>(max is %s)</td></tr></table>';
                            break;
                        default:
                            formatString = '<table class="jqplot-highlighter"><tr style="display:none; visibility:hidden;"><td>hidden:</td><td>%s</td></tr><tr><td>Grade: </td><td>%s / %s</td></tr></table>';
                            break;
                    }
                    this.current_analysis.plot = $.jqplot(this.plot_id, data.lines, {
                        seriesDefaults: { pointLabels: { show: false }, showLine: false, markerOptions:{ size: msize, shadow: false }/*, gridPadding: {top:5, right:5, bottom:5, left:5}*/ },
                        series: data.genseries,
                        axes: {
                            xaxis: {
                                label: data.x_label,
                                autoscale: false,
                                labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                                ticks: data.xticks,
                                tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
                                tickOptions: {
                                    angle: -90
                                },
                                renderer: $.jqplot.CategoryAxisRenderer
                            },
                            yaxis: {
                                label: data.y_label,
                                autoscale: false,
                                labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                                ticks: data.yticks,
                                renderer: $.jqplot.CategoryAxisRenderer,
                                tickRenderer: $.jqplot.CanvasAxisTickRenderer
                            }           
                        },
                        highlighter: {
                            tooltipAxes: 'y',
                            tooltipFade: false, 
                            yvalues: 3, 
                            useAxesFormatters: true, 
                            sizeAdjust: 4.5, 
                            formatString: formatString
                        },
                        cursor: {show: false}
                    });
                    break;        
            }
        }   
    }
    
    // update chart method
    this.update_chart = function () {
        this.create_chart();          
    }
    
    // resize method
    this.resize = function () {
        // adjust left menu and chart height
        var content_height = $("body").height() - $("#" + this.header_id).height() - $("#" + this.footer_id).height();
        $("#" + this.left_menu_id).height(content_height);
        $("#" + this.lm_content_id).height($("#" + this.left_menu_id).height() - $("#" + this.lm_header_id).height());
        $("#" + this.chart_id).height(content_height);
        $("#" + this.ch_content_id).height($("#" + this.chart_id).height() - $("#" + this.ch_header_id).height());
        $("#" + this.plot_id).height($("#" + this.ch_content_id).height() - parseInt($("#" + this.plot_id).css("marginTop")) - parseInt($("#" + this.plot_id).css("marginBottom")));
        // timeline width
        $("#" + this.date_slider_id).width($("body").width() - $("#" + this.from_date_id).width() - $("#" + this.to_date_id).width() - 35);    
        // redraw chart
        if (this.current_analysis != undefined && this.current_analysis.plot != undefined && this.current_analysis.plot != null) {
            if (this.resize_scheduled == false) {
                // schedule resize
                this.resize_scheduled = true;
                var g = this;
                setTimeout(function () {
                    // set plot dimensions
                    g.set_plot_dimensions();
                    // replot
                    // g.current_analysis.plot.replot({clear: true, resetAxes: true});
                    g.update_chart();
                    // resize not scheduled anymore
                    g.resize_scheduled = false;
                    g.last_resize = (new Date()).getTime();    
                }, (this.last_resize + this.cfg.resize_delay < (new Date()).getTime()) ? 5 : this.cfg.resize_delay);
            }    
        }    
    }

    this.is_item_visible = function (item) {
        var visibility = false;
        if (this.cfg.include_hidden_items == "1" || (item["visible"] != undefined && item["visible"] == "1")) {
            visibility = true;
        }
        return visibility;
    }

    this.get_items_number = function (item) {
        var count = 0;
        if (this.static_data[item] != undefined && this.static_data[item] != null) {
            count = this.static_data[item].length;
            if (count > 0 && this.cfg.include_hidden_items == "0") {
                for (var k in this.static_data[item]) {
                    if (this.static_data[item][k]["visible"] != undefined && this.static_data[item][k]["visible"] == "0") {
                        count--;
                    }
                }
            }
        }
        return count;
    }
    
    // print / save method
    this.print_save = function (value) {
        if (this.current_analysis.status == true) {
            // content to be put in the new window / tab
            $("#print_form #datatodisplay").html($("#" + this.chart_id).html());
            // dialog
            $("#print_form #mode").val(value);
            // submit the form
            $("#print_form").submit();
        } else {
            var title = {0: "GISMO - Save chart", 1: "GISMO - Print chart"};
            var message = {0: "Nothing to save at the moment!", 1: "Nothing to print at the moment!"};
            this.util.show_modal_dialog(title[value], "<p>" + message[value] + "</p>");    
        }    
    }
    
    // print method
    this.print = function () {
        this.util.show_modal_dialog("Print function not available", "<p>This function hasn't been implemented yet. It will be available in a future release of GISMO.</p>");
        // return this.print_save(1);
    }

    // save method
    this.save = function () {
        this.util.show_modal_dialog("Save function not available", "<p>This function hasn't been implemented yet. It will be available in a future release of GISMO.</p>");
        // return this.print_save(0);    
    }
    
    // options
    this.options = function () {
        // self
        var g = this;
        // show options dialog
        var dialog = $("<div></div>").attr("id", "dialog");
        var form = $('<form></form>')
                    .attr({id: "gismo_options_form", name: "gismo_options_form" })
                    .append($('<fieldset></fieldset>')
                        .addClass("local_fieldset")
                        .append($("<legend></legend>").html("General settings"))
                        // show hidden items
                        .append($('<label></label>').attr({ "for": "include_hidden_items_yes" }).html("Include hidden items"))
                        .append($('<input type="radio" name="include_hidden_items" />').attr({ id: "include_hidden_items_yes", value: "1" }))
                        .append("Yes")
                        .append($('<input type="radio" name="include_hidden_items" />').attr({ id: "include_hidden_items_no", value: "0" }))
                        .append("No")
                        .append($('<br />'))
                    )
                    .append($('<fieldset></fieldset>')
                        .addClass("local_fieldset")
                        .append($("<legend></legend>").html("Chart settings"))
                        // base color
                        .append($('<label></label>').attr({ "for": "charts_base_color_red" }).html("Base color"))
                        .append($('<input type="radio" name="chart_base_color" />').attr({ id: "charts_base_color_red", value: "1" }))
                        .append("Red")
                        .append($('<input type="radio" name="chart_base_color" />').attr({ id: "charts_base_color_green", value: "2" }))
                        .append("Green")
                        .append($('<input type="radio" name="chart_base_color" />').attr({ id: "charts_base_color_blue", value: "3" }))
                        .append("Blue")
                        .append($('<br />'))
                        .append($('<label></label>'))
                        .append($('<input type="radio" name="chart_base_color" />').attr({ id: "charts_base_color_g2r", value: "4" }))
                        .append("Green to Red")
                        .append($('<br />'))
                        // Axes label max length
                        .append($('<label></label>').attr({ "for": "chart_axis_label_max_len" }).html("Axes label max length (characters)"))
                        .append($('<input type="text"></input>').attr({ id: "chart_axis_label_max_len", name: "chart_axis_label_max_len", maxlength: 2 }).addClass("small_field"))
                        .append($('<br />'))
                        // Axes label max offset
                        .append($('<label></label>').attr({ "for": "chart_axis_label_max_offset" }).html("Axes label max offset (characters)"))
                        .append($('<input type="text"></input>').attr({ id: "chart_axis_label_max_offset", name: "chart_axis_label_max_offset", maxlength: 2 }).addClass("small_field"))
                        .append($('<br />'))
                        // Matrix series max number
                        .append($('<label></label>').attr({ "for": "matrix_num_series_limit" }).html("Number of colors (matrix charts)"))
                        .append($('<input type="text"></text>').attr({ id: "matrix_num_series_limit", name: "matrix_num_series_limit", maxlength: 2 }).addClass("small_field"))
                        .append($('<br />'))
                    )
                    .append($('<fieldset></fieldset>')
                        .addClass("local_fieldset")
                        .append($("<legend></legend>").html("Other settings"))
                        // Window resize delay
                        .append($('<label></label>').attr({ "for": "resize_delay" }).html("Window resize delay (seconds)"))
                        .append($('<select></select>').attr({ id: "resize_delay", name: "resize_delay"})
                            .addClass("medium_field")
                            .append($('<option></option>').attr({value: parseInt(1.0 * 1000.0)}).html("1.0"))
                            .append($('<option></option>').attr({value: parseInt(1.5 * 1000.0)}).html("1.5"))
                            .append($('<option></option>').attr({value: parseInt(2.0 * 1000.0)}).html("2.0"))
                            .append($('<option></option>').attr({value: parseInt(2.5 * 1000.0)}).html("2.5"))
                            .append($('<option></option>').attr({value: parseInt(3.0 * 1000.0)}).html("3.0"))
                            .append($('<option></option>').attr({value: parseInt(3.5 * 1000.0)}).html("3.5"))
                            .append($('<option></option>').attr({value: parseInt(4.0 * 1000.0)}).html("4.0"))
                            .append($('<option></option>').attr({value: parseInt(4.5 * 1000.0)}).html("4.5"))
                            .append($('<option></option>').attr({value: parseInt(5.0 * 1000.0)}).html("5.0"))
                        )
                    );           
        dialog.html("<p>This section let you customize specific applications options.</p>" + $('<div></div>').append(form).html());
        dialog.attr("title", "Options");
        dialog.dialog({ 
            resizable: false, 
            modal: true, 
            draggable: false,
            width: 500,
            buttons: {
                'Cancel': function() {
                    // close dialog 
                    $(this).dialog('destroy'); 
                    $(this).remove();
                },
                'Save': function() {
                    var response = true;
                    // update instance config
                    g.cfg.include_hidden_items = parseInt($(this).find("input[name='include_hidden_items']:checked").val());
                    g.cfg.chart_base_color = parseInt($(this).find("input[name='chart_base_color']:checked").val());
                    g.cfg.chart_axis_label_max_len = parseInt($(this).find("#chart_axis_label_max_len").val());
                    g.cfg.chart_axis_label_max_offset = parseInt($(this).find("#chart_axis_label_max_offset").val());
                    g.cfg.matrix_num_series_limit = parseInt($(this).find("#matrix_num_series_limit").val());
                    g.cfg.resize_delay = parseInt($(this).find("#resize_delay").val());
                    // update settings
                    var config_data = "";
                    for (var k in g.cfg) {
                        config_data += "config_data[" + k + "]=" + g.cfg[k] + "&";        
                    }
                    $.ajax({
                        url: 'ajax_config.php',
                        async: false, 
                        type: 'POST',
                        data: 'q=save&' + config_data + 'srv_data=' + g.srv_data + '&token=' + Math.random(), 
                        dataType: 'json',
                        success: 
                            function(json) {
                                if (!(json["status"] != undefined && json["status"] == "true")) {
                                    response = {error: '1', message: 'Cannot save settings to the database!'};    
                                } else {
                                    response = true;
                                }
                            },
                        error:
                            function(error) {
                                response = {error: '1', message: 'Unknown error!'};     
                            }
                    });
                    // check response for errors
                    if (response['error'] != undefined && response['error'] == '1') {
                        if (response['message'] != undefined) {
                            g.show_error(response['message']);
                        } else {
                            g.show_error('Unknown error!');    
                        }    
                    } else {
                        // rebuild left menu
                        g.lm.init();
                        // replot the chart using new settings 
                        if (g.current_analysis.status == true) {
                            g.update_chart();
                        }
                        // set menu (visible lists icons)
                        g.lm.set_menu(false);
                    }
                    // close dialog 
                    $(this).dialog('destroy'); 
                    $(this).remove(); 
                }                            
            }
        });
        // set form values
        $("#dialog #include_hidden_items_yes").prop('checked', (g.cfg.include_hidden_items == 1));
        $("#dialog #include_hidden_items_no").prop('checked', (g.cfg.include_hidden_items == 0));
        $("#dialog #charts_base_color_red").prop('checked', (g.cfg.chart_base_color == 1));
        $("#dialog #charts_base_color_green").prop('checked', (g.cfg.chart_base_color == 2));
        $("#dialog #charts_base_color_blue").prop('checked', (g.cfg.chart_base_color == 3));
        $("#dialog #charts_base_color_g2r").prop('checked', (g.cfg.chart_base_color == 4));
        $("#dialog #chart_axis_label_max_len").val(g.cfg.chart_axis_label_max_len);
        $("#dialog #chart_axis_label_max_offset").val(g.cfg.chart_axis_label_max_offset);
        $("#dialog #matrix_num_series_limit").val(g.cfg.matrix_num_series_limit);
        $("#dialog #resize_delay").val(g.cfg.resize_delay);                                   
    }
    
    this.about = function () {
        // self
        var g = this;
        // show options dialog
        var dialog = $("<div></div>").attr("id", "dialog");
        var about = $('<div></div>')
                    .append($('<fieldset></fieldset>')
                        .addClass("local_fieldset")
                        .append($("<legend></legend>").html("Gismo"))
                        .append("Information about this Gismo release is reported below:")
                        .append($("<p></p>").append($("<ul></ul>")
                            .append($("<li></li>").append("Version: 2.0.2"))
                            .append($("<li></li>").append("Release date: 2010-11-30"))
                        )) 
                    )
                    .append($('<fieldset></fieldset>')
                        .addClass("local_fieldset")
                        .append($("<legend></legend>").html("Authors"))
                        .append("Please feel free to contact authors for questions or for reporting bugs at the following addresses:")
                        .append($("<p></p>").append($("<ul></ul>")
                            .append($("<li></li>").append("Mauro Nidola (mauro.nidola _AT_ usi.ch)"))
                            .append($("<li></li>").append("Riccardo Mazza (riccardo.mazza _AT_ usi.ch)"))
                            .append($("<li></li>").append("Christian Milani (christian.milani _AT_ usi.ch)"))
                        ))
                    )
        dialog.html(about.html());
        dialog.attr("title", "About Gismo");
        dialog.dialog({ 
            resizable: false, 
            modal: true, 
            draggable: false,
            width: 500,
            buttons: {
                'Close': function() {
                    // close dialog 
                    $(this).dialog('destroy'); 
                }                            
            }
        });   
    }
    
    // exit
    this.exit = function () {
        return this.util.show_exit_confirmation("GISMO - Exit", "Do you really want to exit Gismo?");
    }
}