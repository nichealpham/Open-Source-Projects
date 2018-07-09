var app = angular.module("app")
.controller("mainController", ["$scope", "$http", "$rootScope", "$window", "printService", 'FileSaver', 'Blob', '$location', '$interval', '$timeout', function ($scope, $http, $rootScope, $window, printService, FileSaver, Blob, $location, $interval, $timeout) {
  $scope.establish_server_connections = function() {
    $scope.local_server = {
      name: "Local server",
      link: "http://localhost:8000",
    };
    $scope.transaction_server = {
      name: "Cassandra express server",
      link: "http://103.15.51.249:1337",
    };
    $scope.socket = io.connect($scope.local_server.link, { 'force new connection': true } );
    $scope.heroku_socket = io.connect($scope.transaction_server.link, { 'force new connection': true } );
    $scope.socket.on("serialdevice_connected_sent_by_server", function(data) {
      jQuery("#page_loading").show();
      $scope.custom_timeout = $timeout(function() {
        jQuery("#page_loading").hide();
        var obj = {
          name: "USB Bluetooth Donggle",
          icon: "images/usb.png",
          detail_info: data,
        };
        $scope.connected_devices.push(obj);
        $scope.$apply(function() {
          $scope.should_display_connected_devices = $scope.show_if_length_larger_than_0($scope.connected_devices);
        });
        $timeout.cancel($scope.custom_timeout);
        $scope.custom_timeout = null;
      }, 2100);
    });
    $scope.socket.on("port_unplugged_successfully_sent_by_server", function(port) {
      var ind = $scope.connected_devices.indexOf(port);
      $scope.connected_devices.splice(ind, 1);
      $scope.$apply(function() {
        $scope.should_display_connected_devices = $scope.show_if_length_larger_than_0($scope.connected_devices);
      });
      $scope.close_message_box();
      $scope.loading_message = "Connecting serial port device. Please wait...";
      $scope.custom_timeout = $timeout(function() {
        $scope.$apply(function() {
          $scope.port_to_be_displayed_in_message_box = null;
        });
        $timeout.clear($scope.custom_timeout);
        $scope.custom_timeout = null;
      }, 800);
    });
    $scope.socket.on("port_closed_successfully_sent_by_server", function() {
      $scope.custom_timeout = $timeout(function() {
        $scope.open_message_box();
        jQuery("#page_loading").hide();
        $timeout.cancel($scope.custom_timeout);
        $scope.custom_timeout = null;
      }, 2100);
    });
  };
  $scope.configure_on_pageload = function() {
    jQuery("#message_box").hide();
    jQuery("#page_loading").hide();
    jQuery("#spime_message_box").hide();
    jQuery("#smallPopup_messagebox").tinyDraggable({ handle: '.header' });
    jQuery(".general_black_box_dragable").tinyDraggable({ handle: '.header' });
    jQuery("#mailApp").tinyDraggable({ handle: '.app_topBar' });
    jQuery("#settingApp").tinyDraggable({ handle: '.app_topBar' });
    var vw = jQuery(window).width();
    if (vw <= 1280) {
      $scope.change_to_small_screen_layout();
    } else {
      $scope.change_to_large_screen_layout();
    };
  };
  $scope.handle_jquery_events = function() {
    jQuery("#rightCollumn").on("click", function() {
      $scope.left_navigator_count = 0;
      $scope.right_navigator_count = 0;
      jQuery("#leftNavigator").animate({
        left: -308,
      }, 400);
      jQuery("#rightNavigator").animate({
        right: -318,
      }, 400);
      jQuery("#rightCollumn").animate({
        "margin-left": 0,
      }, 400);
    });
    jQuery(window).on("resize", function() {
      var vw = jQuery(window).width();
      if (vw <= 1280) {
        $scope.change_to_small_screen_layout();
      } else {
        $scope.change_to_large_screen_layout();
      }
    });
    jQuery("#suboption_bgImage").on("click", function() {
      jQuery(".content_area_setting").hide();
      jQuery("#div_background_image").fadeIn(400);
    });
    jQuery("#suboption_appView").on("click", function() {
      jQuery(".content_area_setting").hide();
      jQuery("#div_setting_default").fadeIn(400);
    });
  };
  $scope.initiate_global_variables = function() {
    if (!$window.localStorage["cassandra_should_hide_login"]) {
      $window.localStorage["cassandra_should_hide_login"] = "no";
    };
    // $window.localStorage["cassandra_should_hide_login"] = "no";
    console.log($window.localStorage["cassandra_should_hide_login"]);
    if ($window.localStorage["cassandra_background_image_link"]) {
      $scope.custom_background = JSON.parse($window.localStorage["cassandra_background_image_link"]);
    } else {
      $scope.custom_background = 'background/win.png';
    };
    if ($window.localStorage["cassandra_background_images"]) {
      $scope.background_images = JSON.parse($window.localStorage["cassandra_background_images"]);
    } else {
      $scope.background_images = [
        
        {
          name: "Canon",
          link: "background/canon.png",
          is_selected: false,
        },
        
        {
          name: "Valley",
          link: "background/valley.png",
          is_selected: false,
        },
        
        
        {
          name: "Forest",
          link: "background/forest.jpeg",
          is_selected: false,
        },
        {
          name: "Hill",
          link: "background/hill.png",
          is_selected: false,
        },
        {
          name: "Donate",
          link: "background/donate.png",
          is_selected: false,
        },
        
        {
          name: "Lubu",
          link: "background/lubu.jpg",
          is_selected: false,
        },
        {
          name: "Mountain",
          link: "background/mountain.jpg",
          is_selected: false,
        },
        {
          name: "Rocket",
          link: "background/rocket.jpg",
          is_selected: false,
        },
        {
          name: "Tower",
          link: "background/tower.png",
          is_selected: false,
        },
        {
          name: "Window",
          link: "background/win.png",
          is_selected: true,
        },
      ];
    };
    $scope.settings = [
      {
        name: "Window appearance",
        element_id: "option_winAppear",
        sub_options: [
          {
            name: "Background image",
            image: "images/options/image.png",
            element_id: "suboption_bgImage"
          },
          {
            name: "View mode",
            image: "images/options/view.png",
            element_id: "suboption_appView"
          },
          {
            name: "Widget design",
            image: "images/options/widget.png",
            element_id: "suboption_cardTrans"
          }
        ],
      },
      {
        name: "Detection algorithm",
        element_id: "option_detectAlgo",
        sub_options: [
          {
            name: "Algorithm design",
            image: "images/options/design.png",
            element_id: "suboption_algoDesign"
          }
        ],
      },
      {
        name: "Records managament",
        element_id: "option_recordMana",
        sub_options: [
          {
            name: "Automatic Sync",
            image: "images/options/backup.png",
            element_id: "suboption_autoSync"
          },
          {
            name: "Storage",
            image: "images/options/storage.png",
            element_id: "suboption_storage"
          }
        ],
      },
    ];
    $scope.connected_devices = [];
    $scope.left_navigator_count = 0;
    $scope.right_navigator_count = 0;
    $scope.loading_message = "Under processing. Please wait...";
    $scope.should_display_connected_devices = false;
    $scope.port_to_be_displayed_in_message_box;
    $scope.notifications = [
      {
        title: "Version 1.0 publised",
        sender: "Nguyen, Pham",
        action: {
          type: "redirect",
          link: "/personal",
          extra: ""
        }
      },
      {
        title: "New messages received",
        sender: "Hung, Le",
        action: {
          type: "redirect",
          link: "/messages",
          extra: ""
        }
      },
    ];
    $scope.doctors = [];
    $scope.devices = [];
    $scope.messages = [];
    $scope.chat_messages = [];
    $scope.currentMailType = "inbox";
    $scope.clickedMail = {
        title: "",
        fromAvarta: "",
        date: "",
        content: "",
        from: ""
    };
    $scope.newMails = [
        {
            title: "Spime Version 2.0 announcement",
            from: "Nguyen Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/nguyen.png",
            to: "Nguyen Pham",
            date: "14/12/2015",
            content: "Dear our treasured candidates,<br/>Spime Core Team is currently developing the second module of the website. We hope that we could make it in time in April 24th.<br/>If you find our project interesting please help us improve the products by participating in.<br/>Here are some requirements:<br/>     1.    Good at working independently<br/>     2.    Love Angular js and Css technique<br/>     3.    Willing to work without MONEY<br/>If you still want to help us accomplish our goal in spite of the hardship, we would be very pround to have you side by side as a core team member.<br/>Thank you very much for reading this letter. Spime team wishes you a new nice day of work.<br/>Khoi Nguyen"
        },
        {
            title: "Academic revise 12/2015",
            from: "Nguyen Anh",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/anh2.png",
            to: "Nguyen Pham",
            date: "11/12/2015",
            content: "Dear Nguyen,<br/>Please send me the revised version of your academic transcript. I need it for contacting your university.<br/>Thank you,<br/>Nguyen Anh"
        },
        {
            title: "Military Education",
            from: "Kieu Khanh",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/phuong2.png",
            to: "Nguyen Pham",
            date: "8/12/2015",
            content: "Hi Nguyen,<br/>Miss Hien announce that we should go top the Student OSA room to take our certification for completing military education.<br/>Best,<br/>Khanh"
        },
        {
            title: "Congratz on winning the Startup event",
            from: "Huy Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/huy2.png",
            to: "Nguyen Pham",
            date: "1/12/2015",
            content: "We win it !<br/>Will yopu consider celebrating? This time we gonna invite Miss An and Mr Trung for sure.<br/>Huy"
        },
        {
            title: "About Research 3A project",
            from: "Trung Le",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/nguyenminhthanh.png",
            to: "Nguyen Pham",
            date: "28/11/2015",
            content: "Dear Nguyen,<br/>I would like you to complete the student research log for the Research 3A Project. Please work on it untill September 28th so that we could have time to revise.<br/>Best,<br/>"
        },
        {
            title: "Business Plan revised",
            from: "Thinh Nguyen",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/nguyenphihung.png",
            to: "Nguyen Pham",
            date: "13/12/2015",
            content: ""
        },
        {
            title: "Pictures of the event",
            from: "Thu Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/thu2.png",
            to: "Nguyen Pham",
            date: "14/11/2015",
            content: ""
        },
        {
            title: "Group Work on Monday",
            from: "Nguyen Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/trung2.png",
            to: "Nguyen Pham",
            date: "11/12/2015",
            content: ""
        }
    ];
    $scope.outMails = [
        {
            title: "Spime Version 2.0 announcement",
            from: "Nguyen Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/nguyen.png",
            to: "Nguyen Pham",
            date: "14/12/2015",
            content: "Dear our treasured candidates,<br/>Spime Core Team is currently developing the second module of the website. We hope that we could make it in time in April 24th.<br/>If you find our project interesting please help us improve the products by participating in.<br/>Here are some requirements:<br/>     1.    Good at working independently<br/>     2.    Love Angular js and Css technique<br/>     3.    Willing to work without MONEY<br/>If you still want to help us accomplish our goal in spite of the hardship, we would be very pround to have you side by side as a core team member.<br/>Thank you very much for reading this letter. Spime team wishes you a new nice day of work.<br/>Khoi Nguyen"
        },
        {
            title: "Amato Gozila",
            from: "Nguyen Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/phuong2.png",
            to: "Nguyen Pham",
            date: "11/12/2015",
            content: "Dear Nguyen,<br/>Please send me the revised version of your academic transcript. I need it for contacting your university.<br/>Thank you,<br/>Nguyen Anh"
        },
        {
            title: "Children of The North",
            from: "Kieu Khanh",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/anh2.png",
            to: "Nguyen Pham",
            date: "8/12/2015",
            content: "Hi Nguyen,<br/>Miss Hien announce that we should go top the Student OSA room to take our certification for completing military education.<br/>Best,<br/>Khanh"
        },
        {
            title: "Hulu Hula",
            from: "Huy Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/trung2.png",
            to: "Nguyen Pham",
            date: "1/12/2015",
            content: "We win it !<br/>Will yopu consider celebrating? This time we gonna invite Miss An and Mr Trung for sure.<br/>Huy"
        }
    ];
  };
  $scope.initiate_global_functions = function() {
    $scope.is_selected = function(value) {
      if (value) {
        return "2px solid rgb(84,151,211);";
      } else {
        return "2px solid transparent;";
      };
    };
    $scope.select_this_image_as_background = function(index) {
      $window.localStorage["cassandra_should_hide_login"] = "no";
      var should_choose_this_bg = confirm("Changing background-image will cause the app to restart. If it does not restart automatically, please quit and re-open the app. Do you want to proceed?");
      if (should_choose_this_bg) {
        $scope.custom_background = $scope.background_images[index].link;
        $window.localStorage["cassandra_background_image_link"] = JSON.stringify($scope.custom_background);
        for (var loop = 0; loop < $scope.background_images.length; loop++) {
          if ($scope.background_images[loop].is_selected) {
            $scope.background_images[loop].is_selected = false;
            break;
          };
        };
        $scope.background_images[index].is_selected = true;
        $window.localStorage["cassandra_background_images"] = JSON.stringify($scope.background_images);
        $scope.custom_timeout = $timeout(function() {
          $timeout.cancel($scope.custom_timeout);
          $scope.custom_timeout = null;
          $scope.socket.emit("change_background_image_require_app_restart");
        }, 600);
      };
    };
    $scope.initiate_gridster = function() {
      var gridster = null;
      gridster = jQuery(".gridster ul").gridster({
        widget_base_dimensions: ['auto', 100],
        autogenerate_stylesheet: true,
        min_cols: 1,
        max_cols: 3,
        widget_margins: [0, 5],
        resize: {
          enabled: true
        },
        draggable: {
          enabled: true,
        },
        avoid_overlapped_widgets: true,
        // widget_selector: ">li",
      }).data('gridster');
      jQuery('.gridster  ul').css({'padding': '0'});
      // gridster.disable();
    };
    $scope.resize_gridster = function() {
      $scope.custom_timeout = $timeout(function() {
        $scope.initiate_gridster();
        $timeout.cancel($scope.custom_timeout);
        $scope.custom_timeout = null;
      }, 1000);
    };
    $scope.show_left_navigator = function() {
      jQuery("#leftNavigator").animate({
        left: 0,
      }, 400);
      jQuery("#rightCollumn").animate({
        "margin-left": 40,
      }, 400);
      $scope.right_navigator_count = 0;
      jQuery("#rightNavigator").animate({
        right: -318,
      }, 400);
    };
    $scope.hide_left_navigator = function() {
      jQuery("#leftNavigator").animate({
        left: -308,
      }, 400);
      jQuery("#rightCollumn").animate({
        "margin-left": 0,
      }, 400);
    };
    $scope.show_right_navigator = function() {
      jQuery("#rightNavigator").animate({
        right: 0,
      }, 400);
      jQuery("#rightCollumn").animate({
        "margin-left": -40,
      }, 400);
      $scope.left_navigator_count = 0;
      jQuery("#leftNavigator").animate({
        left: -308,
      }, 400);
    };
    $scope.hide_right_navigator = function() {
      jQuery("#rightNavigator").animate({
        right: -318,
      }, 400);
      jQuery("#rightCollumn").animate({
        "margin-left": 0,
      }, 400);
    };
    $scope.toggle_left_navigator = function() {
      $scope.left_navigator_count += 1;
      if ($scope.left_navigator_count % 2 == 1) {
        $scope.show_left_navigator();
      } else {
        $scope.hide_left_navigator();
      };
    };
    $scope.toggle_right_navigator = function() {
      $scope.right_navigator_count += 1;
      if ($scope.right_navigator_count % 2 == 1) {
        $scope.show_right_navigator();
      } else {
        $scope.hide_right_navigator();
      };
    };
    $scope.open_message_box = function() {
      jQuery("#message_box").show();
      jQuery("#message_box > form > .div_small_popup").animate({
        top: 100,
        opacity: 1
      }, 400);
    };
    $scope.open_spime_messgae_box = function() {
      var value = (jQuery(window).height() - 500) / 2 - 10;
      if (value < 0) {
        value = 0;
      };
      jQuery("#spime_message_box").show();
      jQuery("#mailApp").animate({
        top: 76,
        opacity: 1
      }, 400);
    };
    $scope.close_message_box = function() {
      jQuery("#message_box > form > .div_small_popup").animate({
        top: 60,
        opacity: 0
      }, 400, function() {
        jQuery("#message_box").hide();
      });
    };
    $scope.close_spime_message_box = function() {
      var value = (jQuery(window).height() - 500) / 2 - 80;
      if (value < 0) {
        value = 0;
      };
      jQuery("#mailApp").animate({
        top: 36,
        opacity: 0
      }, 400, function() {
        jQuery("#spime_message_box").hide();
      });
      $scope.hide_mail_response_section();
    };
    $scope.open_setting_box = function() {
      $scope.hide_right_navigator();
      $scope.right_navigator_count = 0;
      var value = (jQuery(window).height() - 500) / 2 - 10;
      if (value < 0) {
        value = 0;
      };
      jQuery("#setting_box").show();
      jQuery("#settingApp").animate({
        top: 40,
        opacity: 1
      }, 400);
    };
    $scope.close_setting_box = function() {
      var value = (jQuery(window).height() - 500) / 2 - 80;
      if (value < 0) {
        value = 0;
      };
      jQuery("#settingApp").animate({
        top: 10,
        opacity: 0
      }, 400, function() {
        jQuery("#setting_box").hide();
      });
    };
    // REMEMBER TO COMMENT THIS FOR UBUNTU AND LINIX, IT NOT IT WILL FAILS
    $scope.tell_nodejs_to_remove_this_port = function(port) {
      $scope.port_to_be_displayed_in_message_box = port;
      $scope.loading_message = "Closing serial port device. Please wait...";
      jQuery("#page_loading").show();
      $scope.socket.emit("client_say_nodejs_to_remove_this_port", port);
    };
    $scope.show_if_length_larger_than_0 = function(obj) {
      if (obj.length > 0) {
        return true;
      } else {
        return false;
      }
    }
    $scope.innit_login = function() {
      $scope.displayText = "This is just a demo version. You can log-in with any name and password. :')";
      $scope.displayStyle = "scnd-font-color";
    };
    $scope.login_failed = function() {
      $scope.displayText = "Login unsuccessful ! Password must not be 456.";
      $scope.displayStyle = "material-pink";
    };
    $scope.show_login = function() {
      $scope.user_email = "";
      $scope.user_password = "";
      jQuery("#divMain").hide();
      jQuery("#divLogin").fadeIn(400);
    };
    $scope.hide_login = function() {
      jQuery("#divLogin").fadeOut(400, function() {
        jQuery("#divMain").fadeIn(400);
      });
      $scope.custom_timeout = $timeout(function() {
        $scope.initiate_gridster();
        $timeout.cancel($scope.custom_timeout);
        $scope.custom_timeout = null;
      }, 1000);
    };
    $scope.performLogin = function() {
        var userInfo = {
          email: $scope.user_email,
          password: $scope.user_password
        };
        userInfo.user_id = Math.floor(Math.random() * 1000) + "-" + Math.floor(Math.random() * 1000) + "-" + Math.floor(Math.random() * 10000);
        if (userInfo.password == 456) {
          $scope.login_failed();
        } else {
          $scope.userInfo = userInfo;
          $window.localStorage["cassandra_userInfo"] = JSON.stringify(userInfo);
          console.log($scope.userInfo);
          $scope.hide_login();
          $window.localStorage["cassandra_should_hide_login"] = "yes";
        };
    };
    $scope.hide_if_zero = function(array) {
      var len = array.length;
      if (len == 0) {
        return true;
      } else {
        return false;
      };
    };
    $scope.openLaboratory = function() {
      $scope.left_navigator_count = 0;
      $scope.hide_left_navigator();
      $window.open("laboratory.html", "_blank", 'width=1260,height=760');
      // $scope.socket.emit("command_app_to_open_laboratory_as_new_window");
      // var win = new $scope.BrowserWindow({ width: 1024, height: 690 });
      // win.loadURL('www.google.com');
    };
    if ($window.localStorage["cassandra_userInfo"]) {
      $scope.userInfo = JSON.parse($window.localStorage["cassandra_userInfo"]);
      $scope.userInfo.account_type = "Primary user";
      if ($window.localStorage["cassandra_should_hide_login"] == "yes") {
        $scope.hide_login();
      };
      // console.log($scope.userInfo);
    };
    $scope.change_to_large_screen_layout = function() {
      jQuery("#header_menu_button").hide();
      jQuery("#leftCollumn").show();
      jQuery("#rightCollumn").css({
        width: "calc(100vw - 308px)",
        left: 306,
      });
      $scope.hide_left_navigator();
    };
    $scope.change_to_small_screen_layout = function() {
      jQuery("#header_menu_button").show();
      jQuery("#leftCollumn").hide();
      jQuery("#rightCollumn").css({
        width: "100vw",
        left: 0,
      });
    };
    $scope.go_back_to_dashboard = function() {
      $location.url('/dashboard');
      $scope.left_navigator_count = 0;
      $scope.hide_left_navigator();
      $scope.resize_gridster();
    };
    $scope.go_to_records_page = function() {
      $location.url('/records');
      $scope.left_navigator_count = 0;
      $scope.hide_left_navigator();
    };
    $scope.go_to_personal_info_page = function() {
      $location.url('/personal');
      $scope.right_navigator_count = 0;
      $scope.hide_right_navigator();
    };
    $scope.getThisMailType = function (mailType) {
        if (mailType == "inbox") {
            return $scope.newMails;
        };
        if (mailType == "outbox") {
            return $scope.outMails;
        };
    };
    $scope.viewThisMail = function (obj) {
        var currObj = obj;
        angular.element("#mail_content_lbcontent").$sce.trustAsHtml(currObj.content);
        anuglra.element("#mail_content_lbfrom").trustAsHtml(currObj.from + " - " + currObj.date);
        anuglra.element("#mail_content_lbtitle").trustAsHtml(currObj.title);
    };
    $scope.clickThisMail = function (object) {
        if ($scope.hEd) {
          $scope.hide_mail_response_section();
        }
        $scope.clickedMail.content = object.content;
        $scope.clickedMail.fromAvarta = object.fromAvarta;
        $scope.clickedMail.date = object.date;
        $scope.clickedMail.title = object.title;
        $scope.clickedMail.from = object.from;
        jQuery("#mail_contentArea_noMailSelected").hide();
    };
    $scope.getLength = function (array) {
        return array.length;
    };
    $scope.bindInbox = function () {
        $scope.currentMailType = "inbox";
    };
    $scope.bindOutbox = function () {
        $scope.currentMailType = "outbox";
    };
    $scope.initiate_ckeditor = function() {
      $scope.hEd = CKEDITOR.instances['mailpage_congcuright_tacomposenewmail'];
      if ($scope.hEd) {
          CKEDITOR.remove($scope.hEd);
      };
      $scope.hEd = CKEDITOR.replace('mailpage_congcuright_tacomposenewmail', {
        language: 'en',
        height: '250px',
        on: {
          'instanceReady': function(evt) {
              //Set the focus to your editor
              CKEDITOR.instances['mailpage_congcuright_tacomposenewmail'].focus();
          },
        },
      });
      $scope.scroll_to_bottom_of_spime_message_box();
    };
    $scope.destroy_ckeditor = function() {
      if ($scope.hEd) {
        CKEDITOR.instances['mailpage_congcuright_tacomposenewmail'].destroy();
        $scope.hEd = null;
      };
    };
    $scope.display_mail_response_section = function() {
      jQuery("#mailpage_congcuright_tacomposenewmail").show();
      $scope.initiate_ckeditor();
    };
    $scope.hide_mail_response_section = function() {
      $scope.destroy_ckeditor();
      jQuery("#mailpage_congcuright_tacomposenewmail").hide();
    };
    $scope.scroll_to_bottom_of_spime_message_box = function() {
      console.log("OK");
      $scope.scroll_timeout = $timeout(function() {
        jQuery(".app_contentArea_scroll").animate({ scrollTop: jQuery(this).height() }, 600);
        $timeout.cancel($scope.scroll_timeout);
      }, 100);
    };
  };
  $scope.establish_server_connections();
  $scope.initiate_global_variables();
  $scope.initiate_global_functions();
  $scope.configure_on_pageload();
  $scope.innit_login();
  $scope.handle_jquery_events();
}])
.controller("personalController", ["$scope", "$http", "$rootScope", "$window", "printService", 'FileSaver', 'Blob', '$location', function ($scope, $http, $rootScope, $window, printService, FileSaver, Blob, $location) {
  console.log("personal");
  if ($window.localStorage["cassandra_userInfo"]) {
    $scope.userInfo = JSON.parse($window.localStorage["cassandra_userInfo"]);
  };
  if ($window.localStorage["cassandra_my_ehealth"]) {
    $scope.ehealth = JSON.parse($window.localStorage["cassandra_my_ehealth"]);
  } else {
    $scope.ehealth = {
      fullname: $scope.userInfo.email,
      date_of_birth: "",
      mssid: $scope.userInfo.user_id,
      sex: "",
      occupation: "",
      email: "",
      phone: "",
      country: "",
      city: "",
      address_line_1: "",
      address_line_2: "",
      my_doctors: [
        {
          fullname: "",
          dssid: "",
          specity: "",
          work_address: "",
          phone: "",
          email: "",
        },
      ],
      location: {lat: "", lng: ""},
      medical_history: {
        history_stroke: false,
        obesity: false,
        high_blood_pressure: false,
        alcoholism: false,
      },
      clinical_symptoms: {
        chest_pain: true,
        shortness_of_breath: false,
        severe_sweating: true,
        dizziness: false,
      },
    };
  };
  $scope.updateInfo = function() {
    $window.localStorage["cassandra_my_ehealth"] = JSON.stringify($scope.ehealth);
  };
  var handle_geolocation = function(position) {

  };
  $scope.get_current_location = function() {
    navigator.geolocation.getCurrentPosition(function success(position) {
      $scope.ehealth.location.lat = position.coords.latitude;
      $scope.ehealth.location.lng = position.coords.longitude;
      console.log(position.coords.longitude);
    }, function error(error) {
      alert("This software version does not support geolocation");
    });
  };

  $scope.init_google_map = function() {
    // var uluru = {lat: -25.363, lng: 131.044};
    var map = new google.maps.Map(document.getElementById('google-map'), {
      zoom: 4,
      center: $scope.ehealth.location
    });
    var marker = new google.maps.Marker({
      position: $scope.ehealth.location,
      map: map
    });
  };
  // $scope.get_current_location();
  // $scope.init_google_map();
  $scope.test = function() {
    console.log($scope.ehealth.clinical_symptoms.dizziness);
  }
}])
.controller("recordsController", ["$scope", "$http", "$rootScope", "$window", "printService", 'FileSaver', 'Blob', '$location', '$interval', '$timeout', 'dsp', function ($scope, $http, $rootScope, $window, printService, FileSaver, Blob, $location, $interval, $timeout, dsp) {
  $scope.establish_server_connections = function() {
    $scope.local_server = {
      name: "Local server",
      link: "http://localhost:8000",
    };
    $scope.transaction_server = {
      name: "Cassandra express server",
      link: "http://103.15.51.249:1337",
    };
    $scope.socket = io.connect($scope.local_server.link, { 'force new connection': true } );
    $scope.heroku_socket = io.connect($scope.transaction_server.link, { 'force new connection': true } );
    $scope.socket.on("serialdevice_connected_sent_by_server", function(data) {
      jQuery("#page_loading").show();
      $scope.custom_timeout = $timeout(function() {
        jQuery("#page_loading").hide();
        var obj = {
          name: "USB Bluetooth Donggle",
          icon: "images/usb.png",
          detail_info: data,
        };
        $scope.connected_devices.push(obj);
        $scope.$apply(function() {
          $scope.should_display_connected_devices = $scope.show_if_length_larger_than_0($scope.connected_devices);
        });
        $timeout.cancel($scope.custom_timeout);
        $scope.custom_timeout = null;
      }, 2100);
    });
    $scope.socket.on("port_unplugged_successfully_sent_by_server", function(port) {
      var ind = $scope.connected_devices.indexOf(port);
      $scope.connected_devices.splice(ind, 1);
      $scope.$apply(function() {
        $scope.should_display_connected_devices = $scope.show_if_length_larger_than_0($scope.connected_devices);
      });
      $scope.close_message_box();
      $scope.loading_message = "Connecting serial port device. Please wait...";
      $scope.custom_timeout = $timeout(function() {
        $scope.$apply(function() {
          $scope.port_to_be_displayed_in_message_box = null;
        });
        $timeout.clear($scope.custom_timeout);
        $scope.custom_timeout = null;
      }, 800);
    });
    $scope.socket.on("port_closed_successfully_sent_by_server", function() {
      $scope.custom_timeout = $timeout(function() {
        $scope.open_message_box();
        jQuery("#page_loading").hide();
        $timeout.cancel($scope.custom_timeout);
        $scope.custom_timeout = null;
      }, 2100);
    });
    $scope.socket.on("save_record_to_local_server_successed", function(response) {
      $scope.$apply(function() {
        $scope.records.push(response);
      });
    });
    if ($scope.userInfo) {
      $scope.socket.emit("make_server_listen_to_this_socket", $scope.userInfo.user_id);
      var pipline_name = "data_from_phone_" + $scope.userInfo.user_id + "_to_server";
      var pipline_result = "data_from_phone_" + $scope.userInfo.user_id + "_to_server_result_to_diagnosis_app";
      var test_message = "Hello " + $scope.userInfo.email;
      $scope.socket.emit(pipline_name, test_message);
      $scope.socket.on(pipline_result, function(data) {
        console.log(data);
      });
    };
    
  };
  $scope.configure_on_pageload = function() {
    jQuery("#message_box").hide();
    jQuery("#page_loading").hide();
    jQuery("#spime_message_box").hide();
    jQuery("#smallPopup_messagebox").tinyDraggable({ handle: '.header' });
    jQuery(".general_black_box_dragable").tinyDraggable({ handle: '.header' });
    jQuery("#mailApp").tinyDraggable({ handle: '.app_topBar' });
    jQuery("#settingApp").tinyDraggable({ handle: '.app_topBar' });
    var vw = jQuery(window).width();
    if (vw <= 1280) {
      $scope.change_to_small_screen_layout();
    } else {
      $scope.change_to_large_screen_layout();
    };
  };
  $scope.handle_jquery_events = function() {
    jQuery("#rightCollumn").on("click", function() {
      $scope.left_navigator_count = 0;
      $scope.right_navigator_count = 0;
      jQuery("#leftNavigator").animate({
        left: -308,
      }, 400);
      jQuery("#rightNavigator").animate({
        right: -318,
      }, 400);
      jQuery("#rightCollumn").animate({
        "margin-left": 0,
      }, 400);
    });
    jQuery(window).on("resize", function() {
      var vw = jQuery(window).width();
      if (vw <= 1280) {
        $scope.change_to_small_screen_layout();
      } else {
        $scope.change_to_large_screen_layout();
      }
    });
    jQuery("#suboption_bgImage").on("click", function() {
      jQuery(".content_area_setting").hide();
      jQuery("#div_background_image").fadeIn(400);
    });
    jQuery("#suboption_appView").on("click", function() {
      jQuery(".content_area_setting").hide();
      jQuery("#div_setting_default").fadeIn(400);
    });
  };
  $scope.initiate_global_variables = function() {
    if (!$window.localStorage["cassandra_should_hide_login"]) {
      $window.localStorage["cassandra_should_hide_login"] = "no";
    };
    // $window.localStorage["cassandra_should_hide_login"] = "no";
    console.log($window.localStorage["cassandra_should_hide_login"]);
    if ($window.localStorage["cassandra_background_image_link"]) {
      $scope.custom_background = JSON.parse($window.localStorage["cassandra_background_image_link"]);
    } else {
      $scope.custom_background = 'background/win.png';
    };
    if ($window.localStorage["cassandra_background_images"]) {
      $scope.background_images = JSON.parse($window.localStorage["cassandra_background_images"]);
    } else {
      $scope.background_images = [
        
        {
          name: "Canon",
          link: "background/canon.png",
          is_selected: false,
        },
        
        {
          name: "Valley",
          link: "background/valley.png",
          is_selected: false,
        },
        
        
        {
          name: "Forest",
          link: "background/forest.jpeg",
          is_selected: false,
        },
        {
          name: "Hill",
          link: "background/hill.png",
          is_selected: false,
        },
        {
          name: "Donate",
          link: "background/donate.png",
          is_selected: false,
        },
        
        {
          name: "Lubu",
          link: "background/lubu.jpg",
          is_selected: false,
        },
        {
          name: "Mountain",
          link: "background/mountain.jpg",
          is_selected: false,
        },
        {
          name: "Rocket",
          link: "background/rocket.jpg",
          is_selected: false,
        },
        {
          name: "Tower",
          link: "background/tower.png",
          is_selected: false,
        },
        {
          name: "Window",
          link: "background/win.png",
          is_selected: true,
        },
      ];
    };
    $scope.settings = [
      {
        name: "Window appearance",
        element_id: "option_winAppear",
        sub_options: [
          {
            name: "Background image",
            image: "images/options/image.png",
            element_id: "suboption_bgImage"
          },
          {
            name: "View mode",
            image: "images/options/view.png",
            element_id: "suboption_appView"
          },
          {
            name: "Widget design",
            image: "images/options/widget.png",
            element_id: "suboption_cardTrans"
          }
        ],
      },
      {
        name: "Detection algorithm",
        element_id: "option_detectAlgo",
        sub_options: [
          {
            name: "Algorithm design",
            image: "images/options/design.png",
            element_id: "suboption_algoDesign"
          }
        ],
      },
      {
        name: "Records managament",
        element_id: "option_recordMana",
        sub_options: [
          {
            name: "Automatic Sync",
            image: "images/options/backup.png",
            element_id: "suboption_autoSync"
          },
          {
            name: "Storage",
            image: "images/options/storage.png",
            element_id: "suboption_storage"
          }
        ],
      },
    ];
    $scope.connected_devices = [];
    $scope.left_navigator_count = 0;
    $scope.right_navigator_count = 0;
    $scope.loading_message = "Under processing. Please wait...";
    $scope.should_display_connected_devices = false;
    $scope.port_to_be_displayed_in_message_box;
    $scope.notifications = [
      {
        title: "Version 1.0 publised",
        sender: "Nguyen, Pham",
        action: {
          type: "redirect",
          link: "/personal",
          extra: ""
        }
      },
      {
        title: "New messages received",
        sender: "Hung, Le",
        action: {
          type: "redirect",
          link: "/messages",
          extra: ""
        }
      },
    ];
    $scope.doctors = [];
    $scope.devices = [];
    $scope.messages = [];
    $scope.chat_messages = [];
    $scope.currentMailType = "inbox";
    $scope.clickedMail = {
        title: "",
        fromAvarta: "",
        date: "",
        content: "",
        from: ""
    };
    $scope.newMails = [
        {
            title: "Spime Version 2.0 announcement",
            from: "Nguyen Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/nguyen.png",
            to: "Nguyen Pham",
            date: "14/12/2015",
            content: "Dear our treasured candidates,<br/>Spime Core Team is currently developing the second module of the website. We hope that we could make it in time in April 24th.<br/>If you find our project interesting please help us improve the products by participating in.<br/>Here are some requirements:<br/>     1.    Good at working independently<br/>     2.    Love Angular js and Css technique<br/>     3.    Willing to work without MONEY<br/>If you still want to help us accomplish our goal in spite of the hardship, we would be very pround to have you side by side as a core team member.<br/>Thank you very much for reading this letter. Spime team wishes you a new nice day of work.<br/>Khoi Nguyen"
        },
        {
            title: "Academic revise 12/2015",
            from: "Nguyen Anh",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/anh2.png",
            to: "Nguyen Pham",
            date: "11/12/2015",
            content: "Dear Nguyen,<br/>Please send me the revised version of your academic transcript. I need it for contacting your university.<br/>Thank you,<br/>Nguyen Anh"
        },
        {
            title: "Military Education",
            from: "Kieu Khanh",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/phuong2.png",
            to: "Nguyen Pham",
            date: "8/12/2015",
            content: "Hi Nguyen,<br/>Miss Hien announce that we should go top the Student OSA room to take our certification for completing military education.<br/>Best,<br/>Khanh"
        },
        {
            title: "Congratz on winning the Startup event",
            from: "Huy Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/huy2.png",
            to: "Nguyen Pham",
            date: "1/12/2015",
            content: "We win it !<br/>Will yopu consider celebrating? This time we gonna invite Miss An and Mr Trung for sure.<br/>Huy"
        },
        {
            title: "About Research 3A project",
            from: "Trung Le",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/nguyenminhthanh.png",
            to: "Nguyen Pham",
            date: "28/11/2015",
            content: "Dear Nguyen,<br/>I would like you to complete the student research log for the Research 3A Project. Please work on it untill September 28th so that we could have time to revise.<br/>Best,<br/>"
        },
        {
            title: "Business Plan revised",
            from: "Thinh Nguyen",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/nguyenphihung.png",
            to: "Nguyen Pham",
            date: "13/12/2015",
            content: ""
        },
        {
            title: "Pictures of the event",
            from: "Thu Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/thu2.png",
            to: "Nguyen Pham",
            date: "14/11/2015",
            content: ""
        },
        {
            title: "Group Work on Monday",
            from: "Nguyen Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/trung2.png",
            to: "Nguyen Pham",
            date: "11/12/2015",
            content: ""
        }
    ];
    $scope.outMails = [
        {
            title: "Spime Version 2.0 announcement",
            from: "Nguyen Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/nguyen.png",
            to: "Nguyen Pham",
            date: "14/12/2015",
            content: "Dear our treasured candidates,<br/>Spime Core Team is currently developing the second module of the website. We hope that we could make it in time in April 24th.<br/>If you find our project interesting please help us improve the products by participating in.<br/>Here are some requirements:<br/>     1.    Good at working independently<br/>     2.    Love Angular js and Css technique<br/>     3.    Willing to work without MONEY<br/>If you still want to help us accomplish our goal in spite of the hardship, we would be very pround to have you side by side as a core team member.<br/>Thank you very much for reading this letter. Spime team wishes you a new nice day of work.<br/>Khoi Nguyen"
        },
        {
            title: "Amato Gozila",
            from: "Nguyen Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/phuong2.png",
            to: "Nguyen Pham",
            date: "11/12/2015",
            content: "Dear Nguyen,<br/>Please send me the revised version of your academic transcript. I need it for contacting your university.<br/>Thank you,<br/>Nguyen Anh"
        },
        {
            title: "Children of The North",
            from: "Kieu Khanh",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/anh2.png",
            to: "Nguyen Pham",
            date: "8/12/2015",
            content: "Hi Nguyen,<br/>Miss Hien announce that we should go top the Student OSA room to take our certification for completing military education.<br/>Best,<br/>Khanh"
        },
        {
            title: "Hulu Hula",
            from: "Huy Pham",
            fromID: "BEBEIU13051",
            fromAvarta: "user/avarta/trung2.png",
            to: "Nguyen Pham",
            date: "1/12/2015",
            content: "We win it !<br/>Will yopu consider celebrating? This time we gonna invite Miss An and Mr Trung for sure.<br/>Huy"
        }
    ];
    $scope.ecg_data = [];
    $scope.file_content = [];
    $scope.record_name = "";
    $scope.record_comment = "";
    $scope.record_sampling_frequency = 100;
    $scope.plot_speed = 40;
    $scope.record_duration = Math.floor($scope.file_content.length / ($scope.record_sampling_frequency) * 10) / 10;
    $scope.record_date = new Date();
    $scope.ecg_bin = [];
    $scope.heartrate_bin = [];
    $scope.variability_bin = [];
    $scope.tmag_bin = [];
    $scope.stdeviation_bin = [];
    $scope.qrs_locs_bin = [];
    // FOR ANNOTATION GENERATOR
    $scope.heartrate_bin_ann = [];
    $scope.variability_bin_ann = [];
    $scope.tmag_bin_ann = [];
    $scope.stdeviation_bin_ann = [];
    $scope.qrs_locs_bin_ann = [];
    $scope.red_code = "#FF4081";
    // var orange_code = "#d17905";
    $scope.orange_code = "#FF9800";
    $scope.green_code = "#8BC34A";
    $scope.status_pool = ["Normal", "Brady", "Tarchy", "AF", "Arryth", "Ische", "Stroke", "Deadly"];
    $scope.colors_pool = [$scope.green_code, $scope.orange_code, $scope.red_code];
    $scope.annotations = [];
    $scope.annotations_statistic = [];
    $scope.chat_messages = [];
    $scope.ann_normal = "rgb(17,168,171)";
    $scope.ann_danger = "rgb(226,75,101)";
    $scope.ann_caution = "rgb(252,177,80)";
    $scope.ann_deep_red = "#F44336";
    $scope.ann_red = "#FF4081";
    $scope.ann_orange = "	#ffb234";
    $scope.ann_yellow = "#ffd834";
    $scope.ann_green = "#9fc05a";
    $scope.ann_lightgreen = "#add633";
    $scope.ann_blue = "#0057e7";
    $scope.ann_human = "rgb(255,139,90)";
    $scope.statistics_count = [0, 0, 0];
    $scope.loading_message = "Processing the signal. Please wait...";
    $scope.timer = 0;
    $scope.selected_index = -1;
  };
  $scope.innitiate_global_functions = function() {
    
    $scope.cancel_all_timeouts_and_intervals = function() {
      if ($scope.hover_record_timeout) {
        $timeout.cancel($scope.hover_record_timeout);
        $scope.hover_record_timeout = null;
      };
      if ($scope.timer_interval) {
        $interval.cancel($scope.timer_interval);
        $scope.timer_interval = null;
      };
    };
    $scope.display_record_statistics = function(index) {
        $scope.timer = 1;
        $scope.timer_interval = $interval(function() {
          if ($scope.timer > 0) {
            $scope.timer += 1;
          };
          if ($scope.timer == 6) {
            if ($scope.selected_index >= 0) {
              if (index != $scope.selected_index) {
                $scope.selected_record = $scope.records[index];
                $scope.init_chart($scope.selected_record.statistics[0], $scope.selected_record.statistics[1], $scope.selected_record.statistics[2]);
                $scope.cancel_all_timeouts_and_intervals();
                $scope.selected_index = index;
              }
            } else {
              $scope.selected_record = $scope.records[index];
              $scope.init_chart($scope.selected_record.statistics[0], $scope.selected_record.statistics[1], $scope.selected_record.statistics[2]);
              $scope.cancel_all_timeouts_and_intervals();
              $scope.selected_index = index;
            }
          };
        }, 160);
    };
    $scope.mouse_leave_this_record = function() {
      $scope.timer = 0;
      $scope.cancel_all_timeouts_and_intervals();
    };
    $scope.view_this_signal = function(record) {
      var index = $scope.records.indexOf(record);
      $window.localStorage["cassandra_command_lab_to_run_this_signal"] = JSON.stringify($scope.records[index]);
      $window.open("laboratory.html", "_blank", 'width=1260,height=760');
      // $scope.socket.emit("command_app_to_open_laboratory_as_new_window");
    };
    $scope.analize_this_signal = function(record) {
      var index = $scope.records.indexOf(record);
      $window.localStorage["cassandra_command_analysis_to_run_this_signal"] = JSON.stringify($scope.records[index]);
      $window.open("analysis_2.html", "_blank", 'width=1260,height=760');
      // $scope.socket.emit("command_app_to_open_analysis_as_new_window");
    };
    $scope.delete_this_record = function(index) {
      if (confirm("Delete record " + $scope.records[index].name + "?")) {
        jQuery("#page_loading").show();
        if (index == $scope.records.length - 1) {
          $scope.selected_record = {
            name: "No records hovered",
            statistics: [0, 0, 0],
          };
          $scope.init_chart($scope.selected_record.statistics[0], $scope.selected_record.statistics[1], $scope.selected_record.statistics[2]);
        } else {
          $scope.selected_record = $scope.records[index + 1];
          $scope.init_chart($scope.selected_record.statistics[0], $scope.selected_record.statistics[1], $scope.selected_record.statistics[2]);
        };

        $scope.cancel_all_timeouts_and_intervals();

        $scope.selected_index = index;

        $scope.record = $scope.records[index];
        var record_data_id = $scope.record.record_id;

        $window.localStorage.removeItem(record_data_id);

        console.log("Remove item: " + record_data_id);

        $scope.records.splice(index, 1);
        $window.localStorage["cassandra_records"] = JSON.stringify($scope.records);

        jQuery("#page_loading").hide();

        $scope.cancel_custom_timeout();

        // $scope.update_my_records_to_local_storage();

      };
    };
    $scope.importPackageFromTextFile = function($fileContent) {
      jQuery("#page_loading").show();
      $scope.custom_timeout = $timeout(function() {
        var fullPath = document.getElementById('file-upload').value;
        var filename = "";
        if (fullPath) {
          filename = fullPath.replace(/^.*?([^\\\/]*)$/, '$1');
          filename = filename.substring(0, filename.lastIndexOf('.'));
        };
        var result = [];
        var lines = $fileContent.split('\n');
        for(var line = 2; line < lines.length - 1; line++) {
          values = lines[line].split(/[ ,;\t ]+/).filter(Boolean);
          if (values.length == 1) {
            result.push(values);
          } else {
            var number_of_leads = values.length;
            result.push(values[number_of_leads - 1]);
          };
        };
        $scope.file_content = "";
        var value_to_devine = dsp.find_max(result);
        for (var loop = 0; loop < result.length - 1; loop++) {
          result[loop] = Math.floor(result[loop] / value_to_devine * 1000) / 1000;
          $scope.file_content += (result[loop] + "\n");
        };
        result[result.length - 1] = Math.floor(result[result.length - 1] / value_to_devine * 1000) / 1000;
        $scope.file_content += (result[result.length - 1]);
        $scope.ecg_data = result;
        $scope.record_name = filename;
        $scope.record_duration = Math.floor($scope.ecg_data.length / ($scope.record_sampling_frequency) * 10) / 10;
        jQuery("#page_loading").hide();
        $scope.cancel_custom_timeout();
      }, 200);
    };
    $scope.update_duration = function() {
      $scope.record_duration = Math.floor($scope.ecg_data.length / ($scope.record_sampling_frequency) * 10) / 10;
    };
    $scope.update_ecg_data_and_duration = function() {
      var result = [];
      var lines = $scope.file_content.split('\n');
      for(var line = 2; line < lines.length; line++) {
        values = lines[line].split(/[ ,;\t ]+/).filter(Boolean);
        if (values.length == 1) {
          result.push(values);
        } else {
          var number_of_leads = values.length;
          result.push(values[number_of_leads - 1]);
        };
      };
      $scope.file_content = "";
      for (var loop = 0; loop < result.length - 1; loop++) {
        $scope.file_content += (result[loop] + "\n");
      };
      $scope.file_content += (result[result.length - 1]);
      $scope.ecg_data = result;
      $scope.record_duration = Math.floor($scope.ecg_data.length / ($scope.record_sampling_frequency) * 10) / 10;
    };
    $scope.open_popup_upload_record = function() {
      $scope.loading_message = "Processing the signal. Please wait...";
      jQuery("#upload_record_popup").show();
      jQuery("#upload_record_popup > form > .div_small_popup").animate({
        top: 100,
        opacity: 1
      }, 400);
    };
    $scope.close_popup_upload_record = function() {
      $scope.file_content = [];
      $scope.record_name = "";
      $scope.record_comment = "";
      $scope.record_sampling_frequency = 100;
      $scope.record_duration = Math.floor($scope.file_content.length / ($scope.record_sampling_frequency) * 10) / 10;
      $scope.record_date = new Date();
      $scope.custom_timeout = $timeout(function () {
        jQuery("#upload_record_popup > form > .div_small_popup").animate({
          top: 140,
          opacity: 0
        }, 400, function() {
          jQuery("#upload_record_popup").hide();
        });
        $scope.cancel_custom_timeout();
      }, 160);
    };
    $scope.save_this_record = function() {
      $scope.loading_message = "Processing the signal. Please wait...";
      jQuery("#page_loading").show();
      $scope.custom_timeout = $timeout(function() {
        $scope.ecg_bin = $scope.ecg_data;
        $scope.down_sampling_value = Math.floor($scope.record_sampling_frequency / $scope.plot_speed);
        // console.log($scope.down_sampling_value);
        for (i = 0; i < $scope.ecg_bin.length; i ++) {
          $scope.ecg_bin[i] = $scope.ecg_bin[i] * 1000;
        };
        var value = dsp.cal_mean($scope.ecg_bin);
        for (i = 0; i < $scope.ecg_bin.length; i ++) {
          $scope.ecg_bin[i] = $scope.ecg_bin[i] - value;
        };
        $scope.ecg_bin = dsp.noise_removal_using_low_pass_filter($scope.ecg_bin);
        $scope.ecg_bin = dsp.smooth_signal_with_moving_avarage(4, $scope.ecg_bin);
        $scope.ecg_bin = dsp.down_sampling($scope.down_sampling_value, $scope.ecg_bin);
        $scope.ecg_bin = dsp.baseline_remove_using_moving_average($scope.ecg_bin);
        $scope.generate_annotations_for_this_segment();
        $scope.new_record = {
          name: $scope.record_name,
          date: $scope.record_date,
          uploaded_by: $scope.userInfo.email + "-desktop",
          record_id: "record__" + Math.floor(Math.random() * 1000000) + "__" + $scope.record_name.split(' ').join('_') + "__" + $scope.record_comment.split(' ').join('_'),
          data_link: $scope.local_server.link + "\\bin\\saved-records\\" + $scope.record_name.split(' ').join('_') + ".txt",
          description: $scope.record_comment,
          clinical_symptoms: {
            chest_pain: false,
            shortness_of_breath: false,
            severe_sweating: false,
            dizziness: false,
          },
          statistics: $scope.transform_statistics($scope.statistics_count),
          send_to_doctor: false,
          user_info: JSON.parse($window.localStorage["cassandra_userInfo"]),
        };
        $scope.record_data = {
          record_id: $scope.new_record.record_id,
          sampling_frequency: $scope.record_sampling_frequency,
          data: $scope.ecg_data,
          user_info: JSON.parse($window.localStorage["cassandra_userInfo"]),
          // annotations: $scope.annotations,
        };
        $scope.records.push($scope.new_record);
        $window.localStorage["cassandra_records"] = JSON.stringify($scope.records);
        $window.localStorage[$scope.record_data.record_id] = JSON.stringify($scope.record_data);
        alert("Record uploaded successfully");
        jQuery("#page_loading").hide();
        $scope.cancel_custom_timeout();
        $scope.close_popup_upload_record();

        var package_to_database_server = {
          record_info: $scope.new_record,
          record_data: $scope.record_data,
          user_info: $scope.userInfo,
        };
        $scope.heroku_socket.emit("save_this_record_directly_to_database_server", package_to_database_server);
      }, 1600);
    };
    $scope.transform_statistics = function(array) {
      var total = array[0] + array[1] + array[2];
      array[0] = Math.floor(array[0] / total * 100);
      array[1] = Math.floor(array[1] / total * 100);
      array[2] = 100 - array[0] - array[1];
      return array;
    };
    $scope.init_chart = function(normal, risk, danger) {
      var chart = new Chartist.Pie('.ct-chart', {
        series: [normal, risk, danger],
        labels: ["Normal", "Risk", "Danger"]
      }, {
        donut: true,
        donutWidth: 56,
        startAngle: 340,
        showLabel: false
      });
      chart.on('draw', function(data) {
        if(data.type === 'slice') {
          // Get the total path length in order to use for dash array animation
          var pathLength = data.element._node.getTotalLength();

          // Set a dasharray that matches the path length as prerequisite to animate dashoffset
          data.element.attr({
            'stroke-dasharray': pathLength + 'px ' + pathLength + 'px'
          });

          // Create animation definition while also assigning an ID to the animation for later sync usage
          var animationDefinition = {
            'stroke-dashoffset': {
              id: 'anim' + data.index,
              dur: 900,
              from: -pathLength + 'px',
              to:  '0px',
              easing: Chartist.Svg.Easing.easeOutQuint,
              // We need to use `fill: 'freeze'` otherwise our animation will fall back to initial (not visible)
              fill: 'freeze'
            }
          };

          // If this was not the first slice, we need to time the animation so that it uses the end sync event of the previous animation
          if(data.index !== 0) {
            animationDefinition['stroke-dashoffset'].begin = 'anim' + (data.index - 1) + '.end';
          }

          // We need to set an initial value before the animation starts as we are not in guided mode which would do that for us
          data.element.attr({
            'stroke-dashoffset': -pathLength + 'px'
          });

          // We can't use guided mode as the animations need to rely on setting begin manually
          // See http://gionkunz.github.io/chartist-js/api-documentation.html#chartistsvg-function-animate
          data.element.animate(animationDefinition, false);
        }
      });

      // For the sake of the example we update the chart every time it's created with a delay of 8 seconds
      chart.on('created', function() {
        if(window.__anim21278907124) {
          clearTimeout(window.__anim21278907124);
          window.__anim21278907124 = null;
        };

      });
    };
    
    $scope.generate_annotation_for_this_beat = function(hr, hrv, std, tp, loc, beat, left) {
      var obj = {
        text: null,
        color: null,
        tooltip: null,
        qrs_loc: loc,
        beat_num: beat,
        left: left,
      };
      if (std >= 35 && tp >= 80) {
        // $scope.health_condition = 2;
        $scope.statistics_count[2] += 1;
        obj.text = "ST+";
        obj.color = $scope.ann_danger;
        obj.tooltip = "<b>ST Elevation</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      };
      if (std <= -20 && tp <= -20) {
        // $scope.health_condition = 2;
        $scope.statistics_count[2] += 1;
        obj.text = "ST-";
        obj.color = $scope.ann_danger;
        obj.tooltip = "<b>ST Depression</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      };
      if (tp >= 160 && hr <= 70 && std < 35) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "PVC";
        obj.color = $scope.ann_human;
        obj.tooltip = "<b>Premature Ventricular Complex</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      } else {
        if (hrv >= 20 && hr <= 70 && tp < 0) {
          // $scope.health_condition = 1;
          $scope.statistics_count[1] += 1;
          obj.text = "PVC";
          obj.color = $scope.ann_human;
          obj.tooltip = "<b>Premature Ventricular Complex</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
          return obj;
        }
      }
      if (hrv >= 10 && (hr >= 120 || hr <= 70)) {
        // $scope.health_condition = 2;
        $scope.statistics_count[1] += 1;
        obj.text = "ARR";
        obj.color = $scope.ann_caution;
        obj.tooltip = "<b>Arrythmia</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      };

      if (tp <= -5) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "T-";
        obj.color = $scope.ann_caution;
        obj.tooltip = "<b>T Inverted</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      };

      if (tp >= 100) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "T+";
        obj.color = $scope.ann_caution;
        obj.tooltip = "<b>T Peaked</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      };

      if (std <= -8 || std >= 20) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.color = $scope.ann_red;
        if (std <= -8) {
          obj.text = "SD-";
          obj.tooltip = "<b>Negative ST Deviation</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        } else {
          obj.text = "SD+";
          obj.tooltip = "<b>Positive ST Deviation</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        }
        // obj.text = "STD";
        // obj.tooltip = "ST Deviation";
        return obj;
      };


      if (hr >= 140) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "TAR";
        obj.color = $scope.ann_human;
        obj.tooltip = "<b>Tarchycardia</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      };

      if (hr <= 50) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "BRA";
        obj.color = $scope.ann_human;
        obj.tooltip = "<b>Bradycardia</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      };
      if (tp >= -5 && tp <= 5) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "T0";
        obj.color = $scope.ann_caution;
        obj.tooltip = "<b>T Absence</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      };
      // $scope.health_condition = 0;
      if (tp > 5 && hr > 40 && hr < 140 && hrv < 10 && std > -8 && std < 20) {
        // $scope.health_condition = 1;
        $scope.statistics_count[0] += 1;
        obj.text = "N";
        obj.color = $scope.ann_normal;
        obj.tooltip = "<b>Normal</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
        return obj;
      };
      $scope.statistics_count[0] += 1;
      obj.text = "M";
      obj.color = $scope.ann_green;
      obj.tooltip = "<b>Missed Beat</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>STD:  " + std + "%</li></ul>";
      return obj;
    };
    $scope.generate_annotations_for_this_segment = function() {
      var qrs_locs = dsp.qrs_detect(40, $scope.ecg_bin);
      $scope.qrs_locs_bin_ann = qrs_locs;
      var hr_bin = dsp.calculate_heart_rates(40, $scope.ecg_bin, qrs_locs);
      var hrv_bin = [];
      var t_result = dsp.t_peaks_detect(40, $scope.ecg_bin, qrs_locs);
      var tp_bin = t_result[0];
      var tlocs = t_result[1];
      var std_bin = dsp.std_detect(40, $scope.ecg_bin, qrs_locs, tlocs);
      for (var loop = 0; loop < hr_bin.length; loop++) {
        var segment = [];
        var span = 3;
        for (var ind = -1; ind < (span - 1); ind++) {
          if (hr_bin[loop + ind]) {
            segment.push(hr_bin[loop + ind]);
          } else {
            segment.push(hr_bin[loop + ind - span]);
          };
        };
        hrv_bin.push(dsp.cal_std(segment));
      };
      $scope.heartrate_bin_ann = hr_bin;
      $scope.variability_bin_ann = hrv_bin;
      $scope.tmag_bin_ann = tp_bin;
      $scope.stdeviation_bin_ann = std_bin;
      for (var beat_ind = 0; beat_ind < hr_bin.length; beat_ind++) {
        var hrv = hrv_bin[beat_ind];
        var hr = hr_bin[beat_ind];
        var std = std_bin[beat_ind];
        var tp = tp_bin[beat_ind];
        var margin_left_value = (qrs_locs[beat_ind] - 2) / $scope.ecg_bin.length * (jQuery("#ecg_chart_container").width() * $scope.window_percentage / 100 + 60) - (qrs_locs[beat_ind] - 2) / $scope.ecg_bin.length * 66;
        var result = $scope.generate_annotation_for_this_beat(hr, hrv, std, tp, qrs_locs, beat_ind, margin_left_value);
        $scope.annotations.push(result);
      };
      var len = $scope.annotations.length;
      var last_ann = {
        text: $scope.annotations[len - 1].text,
        color: $scope.annotations[len - 1].color,
        tooltip: $scope.annotations[len - 1].tooltip,
        qrs_loc: 2 * $scope.annotations[len - 1].qrs_loc - $scope.annotations[len - 2].qrs_loc,
        beat_num: $scope.annotations[len - 1].beat_num + 1,
        left: 2 * $scope.annotations[len - 1].left - $scope.annotations[len - 2].left,
      }
      $scope.annotations.push(last_ann);
      console.log("qrs:" + qrs_locs.length + "-hr:" + hr_bin.length + "-ann:" + $scope.annotations.length);
    };
    $scope.cancel_custom_timeout = function() {
      if ($scope.custom_timeout) {
        $timeout.cancel($scope.custom_timeout);
        $scope.custom_timeout = null;
      };
    };
  };
  $scope.establish_server_connections();
  $scope.initiate_global_variables();
  $scope.configure_on_pageload();
  $scope.innit_login();
  $scope.handle_jquery_events();
  $scope.innitiate_global_functions();
  jQuery("#upload_record_popup").hide();
  jQuery("#smallPopup_uploadRecord").tinyDraggable({ handle: '.header' });
  
  
  $scope.custom_timeout = $timeout(function() {
    if ($window.localStorage["cassandra_records"]) {
      $scope.records = JSON.parse($window.localStorage["cassandra_records"]);
      jQuery("#loading_records_spinner").hide();
    } else {
      $scope.records = [];
      jQuery("#loading_records_spinner").hide();
    };
    $scope.cancel_custom_timeout();
  }, 1600);
  $scope.selected_record = {
    name: "No records hovered",
    statistics: [0, 0, 0],
  };
  $scope.openLaboratory = function() {
    $scope.left_navigator_count = 0;
    $scope.hide_left_navigator();
    $window.open("laboratory.html", "_blank", 'width=1260,height=760');
    // $scope.socket.emit("command_app_to_open_laboratory_as_new_window");
    // var win = new $scope.BrowserWindow({ width: 1024, height: 690 });
    // win.loadURL('www.google.com');
  };
  
}]);
