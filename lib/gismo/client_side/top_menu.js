function top_menu(g) {
    // gismo instance
    this.gismo = g;
    
    this.init = function () {
        var timeout    = 500;
        var menu_timer = 0;
        var ddmenuitem = 0;

        function menu_open() {
            jsddm_canceltimer();
            menu_close();
            ddmenuitem = $(this).find('ul').css('visibility', 'visible');
            $(this).children('a').addClass('menu_open');
            $(this).children('a').children('img').attr('src', 'images/menu_icon_selected.gif');
        }

        function menu_close(a) {  
            if (ddmenuitem) {
                ddmenuitem.css('visibility', 'hidden');    
            }
            $('#panelMenu > li').children('a').removeClass('menu_open');
            $('#panelMenu > li').children('a').children('img').attr('src', 'images/menu_icon.gif');
        }

        function menu_close_scheduler() {
            menu_timer = window.setTimeout(menu_close, timeout);
        }

        function jsddm_canceltimer() {  
            if (menu_timer) {
                window.clearTimeout(menu_timer);
                menu_timer = null;
            }
        }

        $(document).ready(function() {  
            $('#panelMenu > li').bind('mouseover', menu_open);
            $('#panelMenu > li').bind('mouseout',  menu_close_scheduler);
        });

        document.onclick = menu_close;    
    }
}