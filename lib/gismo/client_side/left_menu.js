function left_menu(g) {
    // gismo instance
    this.gismo = g;
    
    // fields
    this.visible_list = 'users';
    this.lists = {users: {img: 'users.png', tooltip: 'users'},
                   resources: {img: 'resources.png', tooltip: 'resources'}, 
                   quizzes: {img: 'quizzes.png', tooltip: 'quizzes'}, 
                   assignments: {img: 'assignments.png', tooltip: 'assignments'}};
    
    // init lm header method
    this.init_lm_header = function() {
        // local variables
        var element, image, item;
        var lm = this;
        // build header
        for (item in this.lists) {
            // add only if not empty
            if (this.gismo.static_data[item].length > 0) {
                // list link with icon
                element = $('<a></a>').attr('href', 'javascript:void(0);').addClass("list_selector");
                element.attr("id", item + "_menu")
                element.bind('click', {list: item, lm: this}, function (event) {
                    event.data.lm.show_list(event.data.list);
                    $(this).blur();
                });
                image = $('<img></img>').attr('src', 'images/' + this.lists[item]["img"]);
                image.attr('alt', 'Show ' + this.lists[item]["tooltip"] + ' list'); 
                image.attr('title', 'Show ' + this.lists[item]["tooltip"] + ' list'); 
                element.append(image);
                // add entry to the header
                $('#' + this.gismo.lm_header_id).append(element);    
            }
        }
    }
    
    // init lm content method
    this.init_lm_content = function() {
        // local variables
        var element, cb_item, cb_label, item;
        var lm = this;
        var count;
        // create lists
        for (item in this.lists) {
            count = this.gismo.get_items_number(item);
            // list
            element = $('<div></div>').attr('id', this.get_list_container_id(item));
            if (count > 0) {
                // add header with a checkbox to control items selection
                cb_item = $('<input></input>').attr("type", "checkbox");
                cb_item.attr("value", "0");
                cb_item.attr("name", item + "_cb_control");
                cb_item.attr("id", item + "_cb_control");
                cb_item.prop("checked", true);
                cb_item.addClass("cb_element");
                cb_item.bind("click", {list: item}, function(event) {
                    $('#' + event.data.list + '_list input:checkbox').prop('checked', $(this).prop('checked'));
                    if (lm.gismo.current_analysis.plot != null && lm.gismo.current_analysis.plot != undefined) {
                        lm.gismo.update_chart();
                    }
                });
                var lab = (item == 'users') ? "students" : item;    // WORKAROUND
                cb_label = $("<label></label>").html("<b>" + lab.toUpperCase() + " (" + count + " ITEMS)</b>");
                cb_label.addClass("cb_label");
                cb_label.prepend(cb_item);
                element.append($('<div></div>').addClass("cb_main").append(cb_label));
                // add items checkboxes
                for (var k=0; k<this.gismo.static_data[item].length; k++) {
                    if (this.gismo.is_item_visible(this.gismo.static_data[item][k])) {
                        cb_item = $('<input></input>').attr("type", "checkbox");
                        cb_item.attr("value", this.gismo.static_data[item][k].id);
                        cb_item.attr("name", item + "_cb[" + this.gismo.static_data[item][k].id + "]");
                        cb_item.attr("id", item + "_cb_" + this.gismo.static_data[item][k].id);
                        cb_item.prop("checked", true);
                        cb_item.addClass("cb_element");
                        cb_item.bind("click", {list: item}, function (event) {
                            // if shift key has been pressed then this is the only one selected
                            if (event.altKey) {
                                $('#' + event.data.list + '_list input:checkbox').attr('checked', false);
                                $(this).attr('checked', true);
                            }
                            if (lm.gismo.current_analysis.plot != null && lm.gismo.current_analysis.plot != undefined) {
                                lm.gismo.update_chart();
                            }
                        });
                        cb_label = $("<label style='float: left;'></label>")
                                        .html(this.gismo.static_data[item][k].name)
                                        .mouseover(function () {
                                            $(this).addClass("selected");
                                        })
                                        .mouseout(function () {
                                            $(this).removeClass("selected");
                                        });
                        cb_label.addClass("cb_label");
                        cb_label.prepend(cb_item);
                        element.append(
                            $("<div></div>").addClass("cb")
                            .append(cb_label)
                            .append(
                                $("<image style='float: left; margin-top: 3px; margin-left: 5px;'></image>")
                                .attr("id", item + "_" + this.gismo.static_data[item][k].id)
                                .attr({src: "images/eye.png", title: "Details"})
                                .addClass(item + "_details image_link float_right")
                                .mouseover(function () {
                                    $(this).parent().addClass("selected");
                                })
                                .mouseout(function () {
                                    $(this).parent().removeClass("selected");
                                })
                                .click(function () {
                                    var options = $(this).attr("id").split("_");
                                    g.analyse(g.current_analysis.type, {subtype: options[0] + "-details", id: options[1]});
                                })
                            )
                        );
                    }
                }
            } else {
                element.html("<p>There isn't any " + item + " in the course!</p>");
            }
            element.hide();
            $('#' + this.gismo.lm_content_id).append(element);
        }
        $('#' + this.gismo.lm_content_id).append($('<br style="clear: both;" />'));
        $('#' + this.gismo.lm_content_id).append($('<div></div>').css({"height": "10px"}))  
    }
    
    this.init_lm_content_details = function() {
        var full_type = this.gismo.get_full_type()
        // hide all details controls
        $(".users_details").hide();
        $(".resources_details").hide();
        $(".assignments_details").hide();
        $(".quizzes_details").hide();
        // show details
        if (this.gismo.menu_details[full_type] != undefined) {
            for (var k in this.gismo.menu_details[full_type]) {
                $("." + this.gismo.menu_details[full_type][k] + "_details").show();    
            }    
        }
    }

    // clean
    this.clean = function () {
        // clean header
        $('#' + this.gismo.lm_header_id + " .list_selector").remove();
        // clean content
        $('#' + this.gismo.lm_content_id).empty();
    }
    
    // init method
    this.init = function () {
        // clean
        this.clean();
        // init header (link icons)
        this.init_lm_header();
        // init content (build lists)
        this.init_lm_content();
        // show / hide items details
        this.init_lm_content_details();
        // show current list
        this.show_list(this.visible_list);
    }
    
    this.get_list_container_id = function (list) {
        return list + "_list";    
    }
    
    this.show_list = function (list) {
        // hide previous list
        $("#" + this.get_list_container_id(this.visible_list)).hide();
        // show new list
        $("#" + this.get_list_container_id(list)).show();
        // update current list
        this.visible_list = list;
    }
    
    this.get_selected_items = function () {
        var selected_items = new Array();
        for (var item in this.lists) {
            selected_items[item] = new Array();
            $("#" + this.get_list_container_id(item) + " input:checkbox:checked").each(function (index) {
                selected_items[item].push($(this).val());            
            });    
        }
        return selected_items;            
    }
    
    this.set_menu = function (fresh) {
        // enabled menus (def)
        var all = ['users', 'resources', 'assignments', 'quizzes'];
        var enabled = ['users', 'resources', 'assignments', 'quizzes'];
        var list = enabled[0];
        var type = this.gismo.get_full_type();
        // default
        if (this.gismo.menu[type] != undefined) {
            enabled = this.gismo.menu[type];
            list = enabled[this.gismo.menu_default[type]];
        }
        // current menu ??
        if (fresh == false) {
            if (jQuery.inArray(this.visible_list, enabled) != -1) {
                list = this.visible_list;
            }
        }
        // show / hide enabled menus
        for (var item in all) {
            if (jQuery.inArray(all[item], enabled) != -1) {
                // show
                $("#" + all[item] + "_menu").show();     
            } else {
                // hide
                $("#" + all[item] + "_menu").hide();
            }
        }
        // show first entry
        this.show_list(list);
    }
    
    this.show = function() {
        $('#open_control').hide(); 
        $('#close_control').show(); 
        $('#left_menu').show();
        $('#left_menu').toggleClass('closed_lm', 0); 
        $('#chart').toggleClass('expanded_ch', 0);
        if (this.gismo.get_full_type() != null) {
            this.gismo.update_chart();   
        }   
    }
    
    this.hide = function() {
        $('#open_control').show(); 
        $('#close_control').hide(); 
        $('#left_menu').hide();
        $('#left_menu').toggleClass('closed_lm', 0); 
        $('#chart').toggleClass('expanded_ch', 0);
        if (this.gismo.get_full_type() != null) {
            this.gismo.update_chart();   
        }
    }

    // info
    this.show_info = function() {
        var title = "GISMO - Lists";
        var message = "<p>To customize the chart you can select/unselect items from enabled menus.</p>";
        message += "<p>Instructions</p>";
        message += "<ul style='list-style-position: inside;'>";
        message += "<li>Main Checkbox: select/unselect all list items.</li>";
        message += "<li>Item Click: select/unselect the clicked item.</li>";
        message += "<li>Item Alt+Click: select only the clicked item</li>";
        message += "<li><img src='images/eye.png'> show item details</li>";
        message += "</ul>";
        this.gismo.util.show_modal_dialog(title, message);
    }
}
