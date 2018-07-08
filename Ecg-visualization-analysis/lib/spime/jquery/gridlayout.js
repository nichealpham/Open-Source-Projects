jQuery(document).ready(function () {
    jQuery(".sideBar").height(jQuery(window).height() - 52);
    //jQuery(".topBar").width(jQuery(".page").width() - 60);//
    jQuery(".viewer").width(jQuery(".page").width() - 60);
    jQuery("#divNenDen").width(jQuery(".topBar").width() - jQuery(".topBar_nameBox").width() - 16);
    jQuery("#personalBox").css({
        "height": jQuery(".sideBar").height(),
        "margin-left": jQuery(".topBar").width() + 30
    });
    jQuery("#divNenDen2").css({
        "height": jQuery(".sideBar").height(),
        "width": jQuery(".topBar").width()
    });
    when_nameBox_clicked();
    when_divNenDen2_clicked();
    fix_position_nameText_inside_timelineBox();
    var windowWidth = jQuery(window).width();
    if (windowWidth >= 1020) //laptop screen
    {
        var pageWd = windowWidth - 17; // -17px for the scroll
        var viewPort = pageWd - 60;
        jQuery(".viewport").width(viewPort);
        jQuery(".topBar_searchBox_laptop").width(pageWd - jQuery(".paralel").width() - 580);
        jQuery(".office").css("margin-top", jQuery(".sideBar").height() - jQuery(".sideBar_iconAnchor").height() - jQuery(".sideBar_iconAnchor2").height() - jQuery(".sideBar_iconAnchor3").height() - jQuery(".sideBar_iconAnchor4").height() - jQuery(".sideBar_iconAnchor5").height() - jQuery(".sideBar_iconAnchor6").height() - 344);
    }
    else    //tablet version
    {
        var pageWd = windowWidth  //Take the whole screen
        var viewPort = pageWd - 60;
        jQuery(".viewport").width(viewPort);        
    };
    function when_nameBox_clicked() {
        jQuery(".nameBox_anchor").on("click", function () {
            jQuery(this).css({
                "background-color": "#16a085",
                "color": "White"
            });
            jQuery("#divNenDen2").css("display", "block");
            jQuery("#personalBox").animate({
                "margin-left": jQuery(".topBar").width() - 300,
                "opacity": 1
            }, 400);
        });
    };
    function when_divNenDen2_clicked() {
        jQuery("#divNenDen2").on("click", function () {
            jQuery(".nameBox_anchor").css({
                "background-color": "transparent",
                "color": "eeeeee"
            });
            jQuery("#divNenDen2").css("display", "none");
            jQuery("#personalBox").animate({
                "margin-left": jQuery(".topBar").width() + 30,
                "opacity": 0
            }, 200);
        });       
    };
    function fix_position_nameText_inside_timelineBox() {
        var length = jQuery("#timelineBox_textName").val().length;
        if (length > 16) {
            jQuery("#timelineBox_textName").css("top", "-46px");
        };
    };
});