jQuery(document).ready(function () {
    jQuery(".app_sideBar_contentArea").on("click", function () {
        jQuery("#mail_contentArea_noMailSelected").hide();
    });
    jQuery("#topBar_btnmail").on("click", function () {
        jQuery("#divNenDen2").css("display", "block");
        jQuery("#mailApp").css("display", "block");
        jQuery("#mailApp").animate({
            "opacity": "1",
            "margin-top": "100px"
        }, 400);
    });
    jQuery("#app_mail_btnquit").on("click", function () {
        jQuery("#mailApp").animate({
            "opacity": "0",
            "margin-top": "60px"
        }, 400, function () {
            jQuery(this).css("display", "none");
        });
    });
    jQuery(".app_navBar a").on("click", function () {
        jQuery(".app_navBar a").css({
            "background-color": "rgb(74,81,96)",
            "color": "rgb(138,146,165)"
        });
        jQuery(this).css({
            "background-color": "#16a085",
            "color": "#eeeeee"
        });
    });
});