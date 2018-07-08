var app = angular.module("app")
.controller("laboratoryController", ["$scope", "$http", "$rootScope", "$window", "printService", 'FileSaver', 'Blob', '$location', '$interval', 'dsp', '$timeout', function ($scope, $http, $rootScope, $window, printService, FileSaver, Blob, $location, $interval, dsp, $timeout) {
  console.log("laboratory");
  jQuery("#upload_record_popup").hide();
  // Down sampled ecg, fs = 40, used for plotting only
  var ecg_bin = [];
  // Original ecg, fs predefined, used to store data
  $scope.ecg_storage = [];
  $scope.flag_from_receive_data_from_phone = 0;
  $scope.cancel_custom_timeout = function() {
    if ($scope.custom_timeout) {
      $timeout.cancel($scope.custom_timeout);
      $scope.custom_timeout = null;
    };
  };
  $scope.cancel_initiate_chart_interval = function() {
    if ($scope.initiate_chart_interval) {
      $interval.cancel($scope.initiate_chart_interval);
      $scope.initiate_chart_interval = null;
    };
  };
  $scope.cancel_resume_chart_update_interval = function() {
    if ($scope.resume_chart_update_interval) {
      $interval.cancel($scope.resume_chart_update_interval);
      $scope.resume_chart_update_interval = null;
    };
  };
  var random_data = function(number_of_point, scale) {
    var data = [];
    for (i = 0; i < number_of_point; i++) {
      data.push(Math.random() * scale);
    };
    return data;
  };

  $scope.local_server = {
    name: "Local server",
    link: "http://localhost:8000",
  };
  $scope.transaction_server = {
    name: "Cassandra express server",
    link: "http://103.15.51.249:1337",
  };
  $scope.update_duration = function() {
    $scope.record_duration = Math.floor($scope.ecg_storage.length / ($scope.record_sampling_frequency) * 10) / 10;
  };
  $scope.ecg_data = [];
  $scope.file_content = [];
  $scope.record_name = "";
  $scope.record_comment = "";
  $scope.record_sampling_frequency = 100;
  $scope.record_duration = Math.floor($scope.file_content.length / ($scope.record_sampling_frequency) * 10) / 10;
  $scope.record_date = new Date();

  var socket = io.connect($scope.local_server.link, { 'force new connection': true } );

  var heroku_socket = io.connect($scope.transaction_server.link, { 'force new connection': true } );

  var red_code = "#f05b4f";
  // var orange_code = "#d17905";
  var orange_code = "#FF9800";
  var green_code = "#8BC34A";

  if ($window.localStorage["cassandra_userInfo"]) {
    $scope.userInfo = JSON.parse($window.localStorage["cassandra_userInfo"]);
  };

  // jQuery(window).on("resize", function() {
  //   var vw = jQuery(window).width();
  //   if (vw < 1000) {
  //     jQuery(".lab_view").css("padding-top","10px");
  //     jQuery(".feature-block").css("padding","0px");
  //   } else {
  //     jQuery(".lab_view").css("padding-top","90px");
  //     jQuery(".feature-block").css("padding","0px 0px 0px 10px;");
  //   };
  // });

  $scope.status_pool = ["Normal", "Brady", "Tarchy", "AF", "Arryth", "Ische", "Stroke", "Deadly"];
  var colors_pool = [green_code, orange_code, red_code];

  $scope.initiate_variables = function() {
    $scope.data_pointer = 0;
    $scope.sampling_frequency = 80;    // 100 points per 1,000ms

    $scope.heart_rate = "---";
    $scope.variability = "---";
    $scope.tmag = "---";
    $scope.std_val = "---";
    $scope.health = "-----";

    $scope.heart_rate_condition = 0;
    $scope.variability_condition = 0;
    $scope.health_condition = 0;
    $scope.tmag_condition = 0;
    $scope.std_condition = 0;

    $scope.heart_rate_color       = colors_pool[$scope.heart_rate_condition];
    $scope.variability_color      = colors_pool[$scope.variability_condition];
    $scope.health_color           = colors_pool[$scope.health_condition];
    $scope.tmag_color             = colors_pool[$scope.tmag_condition];
    $scope.std_color              = colors_pool[$scope.std_condition];

    $scope.statistics_count = [0, 0, 0];
  };
  $scope.check_for_health_color = function() {
    var value = 0;
    var arr = [$scope.heart_rate_condition, $scope.variability_condition, $scope.tmag_condition, $scope.std_condition];
    for (var loop = 0; loop < arr.length; loop++) {
      if (arr[loop] > value) {
        value = arr[loop];
      };
    };
    return value;
  };
  $scope.initiate_window_settings = function() {
    $scope.plot_speed = 40;             // 50 points per 1,000ms
    $scope.tick_speed = Math.floor(1000 / $scope.plot_speed);
    $scope.chart_pointer = 0;
    $scope.window_span = 4;             // in seconds
    $scope.window_leng = $scope.plot_speed * $scope.window_span;
  };
  var preprocess_signal = function(smooth_options, down_sampling_value, should_normalized, data) {
    if (smooth_options) {
      data = dsp.moving_average(smooth_options[0], smooth_options[1], data);
    };
    if (down_sampling_value) {
      data = dsp.down_sampling(down_sampling_value, data);
    };
    if (should_normalized) {
      data = dsp.perform_normalization(data);
    };
    return data;
  };

  $scope.initiate_variables();
  $scope.initiate_window_settings();
  var chart_data = {
    series: [
      random_data($scope.window_leng, 800),
    ]
  };
  var create_chart_options = function(x_axis_ops, y_axis_ops, high_val, low_val) {
    var obj = {
      showPoint: false,
      showArea: false,
      fullWidth: true,
      high: high_val,
      low: low_val,
      axisX: {
        showGrid: x_axis_ops[0],
        showLabel: x_axis_ops[1],
      },
      axisY: {
        showGrid: y_axis_ops[0],
        showLabel: y_axis_ops[1],
      },
      chartPadding: {
        right: 20,
        bottom: 0
      },
    };
    return obj;
  };
  var create_chart_options_without_highlow = function(x_axis_ops, y_axis_ops) {
    var obj = {
      showPoint: false,
      showArea: false,
      fullWidth: true,
      axisX: {
        showGrid: x_axis_ops[0],
        showLabel: x_axis_ops[1],
      },
      axisY: {
        showGrid: y_axis_ops[0],
        showLabel: y_axis_ops[1],
      },
      chartPadding: {
        right: 20,
        bottom: 0
      },
    };
    return obj;
  };
  var initiate_chart_data_first_time = function() {
    chart_data_data = [];
    for (var loop = 0; loop < $scope.window_leng; loop++) {
      chart_data_data.push(0);
    };
    chart_data = {
      series: [
        chart_data_data,
      ]
    };
  };
  $scope.initiateChart = function(max_val, min_val) {
    if (max_val) {
      $scope.chart = new Chartist.Line('.ct-chart', chart_data, create_chart_options([true, false], [true, true], max_val, min_val));
    } else {
      $scope.chart = new Chartist.Line('.ct-chart', chart_data, create_chart_options_without_highlow([true, false], [true, true]));
    }
    // $scope.chart_fecg = new Chartist.Line('.fecg-chart', chart_data, create_chart_options([false, false], [false, false], dsp.find_max(ecg_bin), dsp.find_min(ecg_bin)));
    // $scope.chart.on('draw', function(context) {
    //   // First we want to make sure that only do something when the draw event is for bars. Draw events do get fired for labels and grids too.
    //   if(context.type === 'line') {
    //     // With the Chartist.Svg API we can easily set an attribute on our bar that just got drawn
    //     context.element.attr({
    //       // Now we set the style attribute on our bar to override the default color of the bar. By using a HSL colour we can easily set the hue of the colour dynamically while keeping the same saturation and lightness. From the context we can also get the current value of the bar. We use that value to calculate a hue between 0 and 100 degree. This will make our bars appear green when close to the maximum and red when close to zero.
    //       style: 'stroke: ' + $scope.health_color
    //     });
    //   }
    // });
    // $scope.chart_fecg.on('draw', function(context) {
    //   // First we want to make sure that only do something when the draw event is for bars. Draw events do get fired for labels and grids too.
    //   if(context.type === 'line') {
    //     // With the Chartist.Svg API we can easily set an attribute on our bar that just got drawn
    //     context.element.attr({
    //       // Now we set the style attribute on our bar to override the default color of the bar. By using a HSL colour we can easily set the hue of the colour dynamically while keeping the same saturation and lightness. From the context we can also get the current value of the bar. We use that value to calculate a hue between 0 and 100 degree. This will make our bars appear green when close to the maximum and red when close to zero.
    //       style: 'stroke: ' + $scope.health_color
    //     });
    //   }
    // });
  };

  var perform_diagnosis_for_these_features = function(hr, hrv, std, tp) {
    if (std > 30 && tp > 80) {
      // $scope.health_condition = 2;
      $scope.statistics_count[2] += 1;
      $scope.health = "ST Elevate";
      // $scope.health = "Danger";
      return;
    };
    if (std < -20 && tp < -20) {
      // $scope.health_condition = 2;
      $scope.statistics_count[2] += 1;
      $scope.health = "NSTEMI";
      // $scope.health = "Danger";
      return;
    };
    if (hrv > 20 && hr < 90) {
      // $scope.health_condition = 1;
      $scope.statistics_count[1] += 1;
      $scope.health = "PVC";
      // $scope.health = "Caution";
      return;
    };
    if (hrv > 12) {
      // $scope.health_condition = 2;
      $scope.statistics_count[1] += 1;
      $scope.health = "Arrythmia";
      // $scope.health = "Caution";
      return;
    };

    if (tp < -10) {
      // $scope.health_condition = 1;
      $scope.statistics_count[1] += 1;
      $scope.health = "T inverted";
      // $scope.health = "Caution";
      return;
    };

    if (tp > 100) {
      // $scope.health_condition = 1;
      $scope.statistics_count[1] += 1;
      $scope.health = "T peaked";
      // $scope.health = "Caution";
      return;
    };

    if (std < -10 || std > 20) {
      // $scope.health_condition = 1;
      $scope.statistics_count[1] += 1;
      $scope.health = "ST Deviate";
      // $scope.health = "Caution";
      return;
    };


    if (hr > 140) {
      // $scope.health_condition = 1;
      $scope.statistics_count[1] += 1;
      $scope.health = "Tarchy";
      // $scope.health = "Caution";
      return;
    };
    if (hr > 120) {
      // $scope.health_condition = 1;
      $scope.statistics_count[0] += 1;
      $scope.health = "Fast HR";
      // $scope.health = "Caution";
      return;
    };
    if (hr < 50) {
      // $scope.health_condition = 1;
      $scope.statistics_count[1] += 1;
      $scope.health = "Brady";
      // $scope.health = "Caution";
      return;
    };
    if (hr < 60) {
      // $scope.health_condition = 1;
      $scope.statistics_count[0] += 1;
      $scope.health = "Slow HR";
      // $scope.health = "Caution";
      return;
    };
    if (tp > -10 && tp < 4) {
      // $scope.health_condition = 1;
      $scope.statistics_count[1] += 1;
      $scope.health = "T Absence";
      return;
    };
    // $scope.health_condition = 0;
    $scope.statistics_count[0] += 1;
    $scope.health = "Normal";
  };

  $scope.initiate_chart_when_ecg_bin_has_data = function(should_flexible_axis) {
    if (should_flexible_axis) {
      $scope.initiate_chart_interval = $interval(function() {
        if (ecg_bin.length > 0) {
          $scope.initiateChart();
          $scope.cancel_initiate_chart_interval();
          jQuery("#page_loading").hide();
        };
      }, 2000);
    } else {
      $scope.initiate_chart_interval = $interval(function() {
        if (ecg_bin.length > 0) {
          $scope.initiateChart(dsp.find_max(ecg_bin), dsp.find_min(ecg_bin));
          $scope.cancel_initiate_chart_interval();
          jQuery("#page_loading").hide();
        };
      }, 2000);
    };
  };

  if ($window.localStorage["cassandra_command_lab_to_run_this_signal"]) {

    $scope.record_of_interest = JSON.parse($window.localStorage["cassandra_command_lab_to_run_this_signal"]);
    $window.localStorage.removeItem("cassandra_command_lab_to_run_this_signal");

    var record_data_id = $scope.record_of_interest.record_id;
    console.log("Record_id: " + record_data_id);

    $scope.record_name = $scope.record_of_interest.name;
    $scope.record_comment = $scope.record_of_interest.description;
    $scope.record_data_to_display = JSON.parse($window.localStorage[record_data_id]);

    ecg_bin = $scope.record_data_to_display.data;
    $scope.ecg_storage = ecg_bin;

    $scope.sampling_frequency = $scope.record_data_to_display.sampling_frequency;
    $scope.record_sampling_frequency = $scope.sampling_frequency;

    if ($scope.sampling_frequency > 40) {

      $scope.down_sampling_value = Math.floor($scope.sampling_frequency / $scope.plot_speed);
      for (i = 0; i < ecg_bin.length; i ++) {
        ecg_bin[i] = ecg_bin[i] * 1000;
      };
      var value = dsp.cal_mean(ecg_bin);
      for (i = 0; i < ecg_bin.length; i ++) {
        ecg_bin[i] = ecg_bin[i] - value;
      };
      // NEU DATA DC DO TU MAY CASSANDRA, THEM BASELINE REMOVE
      if ($scope.sampling_frequency == 320) {
        ecg_bin = dsp.noise_removal_using_low_pass_filter(ecg_bin);
        ecg_bin = dsp.smooth_signal_with_moving_avarage(4, ecg_bin);
        ecg_bin = dsp.down_sampling($scope.down_sampling_value, ecg_bin);
        ecg_bin = dsp.baseline_remove_using_moving_average(ecg_bin);
        $scope.initiate_chart_when_ecg_bin_has_data(true);
        // var chart_max_value = dsp.find_max(ecg_bin);
        // var chart_min_value = dsp.find_min(ecg_bin);
        // $scope.initiateChart(chart_max_value, chart_min_value);
      } else {
        ecg_bin = dsp.noise_removal_using_low_pass_filter(ecg_bin);
        ecg_bin = dsp.smooth_signal_with_moving_avarage(4, ecg_bin);
        ecg_bin = dsp.down_sampling($scope.down_sampling_value, ecg_bin);
        ecg_bin = dsp.baseline_remove_using_moving_average(ecg_bin);
        var chart_max_value = dsp.find_max(ecg_bin);
        var chart_min_value = dsp.find_min(ecg_bin);
        $scope.initiateChart(chart_max_value, chart_min_value);
      };
    } else {
      var chart_max_value = dsp.find_max(ecg_bin);
      chart_max_value = chart_max_value * 1.4;
      var chart_min_value = dsp.find_min(ecg_bin);
      $scope.initiateChart(chart_max_value, chart_min_value);
    };
  } else {
    jQuery("#page_loading").show();
    $scope.custom_timeout = $timeout(function() {

      // DUNG DUNG VO, QUAN TRONG REFERENCE

      // socket.emit("get_sample_ecg_data_from_server", { number_of_samples: 320 });
      // socket.on("get_sample_ecg_data_from_server_result", function(data) {
      //   console.log("Received data package from server");
      //   $scope.sampling_frequency = data.sampling_frequency;
      //   var ecg_package = data.ecg_data;
      //   $scope.ecg_storage = $scope.ecg_storage.concat(ecg_package);
      //   $scope.record_sampling_frequency = $scope.sampling_frequency;
      //   $scope.down_sampling_value = Math.floor($scope.sampling_frequency / $scope.plot_speed);
      //   for (var loop = 0; loop < ecg_package.length; loop ++) {
      //     ecg_package[loop] = ecg_package[loop] * 1000;
      //   };
      //   ecg_package = preprocess_signal([10, 1500], $scope.down_sampling_value, null, ecg_package);
      //   ecg_bin = ecg_bin.concat(ecg_package);
      // });
      // $scope.cancel_custom_timeout();
      // $scope.initiate_chart_when_ecg_bin_has_data();
      // jQuery("#page_loading").hide();

      $scope.data_retrieved_from = "server";
      heroku_socket.on("data_array_from_phone_to_server_then_to_clients", function(data_array) {
          $scope.data_retrieved_from = "phone";
          $scope.flag_from_receive_data_from_phone = 1;
          console.log("Received " + data_array.length + " data from server");
          $scope.sampling_frequency = 200;
          $scope.record_sampling_frequency = $scope.sampling_frequency;
          var ecg_package = data_array;  // ecg_package == values
          var ecg_temp_bin = [];
          var ecg_special_temp = [];
          for (var loop = 0; loop < ecg_package.length; loop++) {
            // if (ecg_package[loop] > 4000000 && ecg_package[loop] < 8000000) {
            //   ecg_temp_bin.push(ecg_package[loop]);
            // };
            if (ecg_package[loop] > 0 && ecg_package[loop] < 8000000) {
              ecg_temp_bin.push(ecg_package[loop]);
            };
          };
          var value_to_substract_important = ecg_temp_bin[0];
          for (var loop = 0; loop < ecg_temp_bin.length; loop++) {
            ecg_temp_bin[loop] = ecg_temp_bin[loop] - value_to_substract_important;
          };
          $scope.down_sampling_value = Math.floor($scope.sampling_frequency / $scope.plot_speed);
          ecg_temp_bin = dsp.noise_removal_using_low_pass_filter(ecg_temp_bin);
          ecg_temp_bin = dsp.smooth_signal_with_moving_avarage(4, ecg_temp_bin);
          ecg_package = ecg_temp_bin;
          $scope.ecg_storage = $scope.ecg_storage.concat(ecg_package);
          ecg_package = dsp.down_sampling($scope.down_sampling_value, ecg_package);
          ecg_package = dsp.baseline_remove_using_moving_average(ecg_package);
          ecg_bin = ecg_bin.concat(ecg_package);
      });
      socket.on("data_array_from_serial_port_to_client", function(data_array) {
          $scope.data_retrieved_from = "serialport";
          $scope.flag_from_receive_data_from_phone = 1;
          console.log("Received " + data_array.length + " data from server");
          $scope.sampling_frequency = 80;
          $scope.record_sampling_frequency = $scope.sampling_frequency;
          var ecg_package = data_array;  // ecg_package == values
          var ecg_temp_bin = [];
          var ecg_special_temp = [];
          ecg_temp_bin = ecg_package;
          // for (var loop = 0; loop < ecg_package.length; loop++) {
          //   if (ecg_package[loop] > 4000000 && ecg_package[loop] < 8000000) {
          //     ecg_temp_bin.push(ecg_package[loop]);
          //   };
          // };
          // var value_to_substract_important = ecg_temp_bin[0];
          // for (var loop = 0; loop < ecg_temp_bin.length; loop++) {
          //   ecg_temp_bin[loop] = ecg_temp_bin[loop] - value_to_substract_important;
          // };
          $scope.down_sampling_value = Math.floor($scope.sampling_frequency / $scope.plot_speed);
          ecg_temp_bin = dsp.noise_removal_using_low_pass_filter(ecg_temp_bin);
          ecg_temp_bin = dsp.smooth_signal_with_moving_avarage(4, ecg_temp_bin);
          ecg_package = ecg_temp_bin;
          $scope.ecg_storage = $scope.ecg_storage.concat(ecg_package);
          ecg_package = dsp.down_sampling($scope.down_sampling_value, ecg_package);
          ecg_package = dsp.baseline_remove_using_moving_average(ecg_package);
          // ecg_package = preprocess_signal([10, 1500], $scope.down_sampling_value, null, ecg_package);
          ecg_bin = ecg_bin.concat(ecg_package);
      });
      $scope.cancel_custom_timeout();
      $scope.initiate_chart_when_ecg_bin_has_data(true);
    }, 50);
  };
  var hrv_data = [],
      hrh_data = [],
      hrvh_data = [],
      stdh_data = [],
      th_data = [];

  $scope.updateChart = function(chart_data) {
    $scope.chart.update(chart_data);
  };
  $scope.updateChartData = function(index, value) {
    if (index < $scope.window_leng - 2) {
      chart_data.series[0][index] = value;
      chart_data.series[0][index + 1] = null;
    } else {
      // When reaching the end of the window_leng
      // update the value
      chart_data.series[0][index] = value;
      // Then perform diagnosis
      var t0 = performance.now();
      heart_rate_interval_tasks_handle();
      variability_interval_tasks_handle();
      t_and_std_interval_tasks_handle();
      health_interval_tasks_handle();

      chart_data.series[0][0] = null;
      var t1 = performance.now();
      console.log(Math.floor((t1 - t0) * 100) / 100 + "ms");
    };
  };

  var chart_update_interval_tasks_handle = function() {
    var value = ecg_bin[$scope.data_pointer];
    if ($scope.chart_pointer == $scope.window_leng) {
      $scope.chart_pointer = 0;
    };
    $scope.updateChartData($scope.chart_pointer, value);
    $scope.updateChart(chart_data);
    $scope.data_pointer += 1;
    $scope.chart_pointer += 1;
  };
  var special_resume_chart_update_interval = function() {
    $scope.resume_chart_update_interval = $interval(function() {
      if ($scope.data_pointer < ecg_bin.length) {
        $scope.chart_update_interval = $interval(function() {
          if ($scope.data_pointer < ecg_bin.length) {
            chart_update_interval_tasks_handle();
          } else {
            stop_all_intervals_and_timeouts();
            special_resume_chart_update_interval();
          };
        }, $scope.tick_speed);
        $scope.cancel_resume_chart_update_interval();
      } else {
        console.log("No more data found!");
      }
    }, 2000);
  };

  var stop_all_intervals_and_timeouts = function() {

    // chart draw interval
    if ($scope.chart_update_interval != null) {
      $interval.cancel($scope.chart_update_interval);
    };
    $scope.chart_update_interval = null;

    // heart rate interval
    if ($scope.heart_rate_interval != null) {
      $interval.cancel($scope.heart_rate_interval);
    };
    $scope.heart_rate_interval = null;

    // variability interval
    if ($scope.variability_interval != null) {
      $interval.cancel($scope.variability_interval);
    };
    $scope.variability_interval = null;

    // health interval
    if ($scope.health_interval != null) {
      $interval.cancel($scope.health_interval);
    };
    $scope.health_interval = null;

    // chart update timeout interval
    if ($scope.chart_update_timeout != null) {
      $timeout.cancel($scope.chart_update_timeout);
    };
    $scope.chart_update_timeout = null;
  };

  var animate_blinking = function() {
    jQuery(".blinking").animate({
      "opacity": 0.6
    }, 1000, function() {
      jQuery(".blinking").animate({
        "opacity": 1
      }, 1000, function() {
        animate_blinking();
      });
    });
  };
  var scroll_to_bottom_of_message_box = function() {
    $scope.scroll_timeout = $timeout(function() {
      jQuery("#div_chat_content").animate({ scrollTop: jQuery(this).height() }, 400);
      $timeout.cancel($scope.scroll_timeout);
    }, 100);
  };

  animate_blinking();

  var update_colors =  function() {
    $scope.heart_rate_color       = colors_pool[$scope.heart_rate_condition];
    $scope.variability_color      = colors_pool[$scope.variability_condition];
    $scope.health_color           = colors_pool[$scope.health_condition];
    $scope.tmag_color             = colors_pool[$scope.tmag_condition];
    $scope.std_color              = colors_pool[$scope.std_condition];
  };

  $scope.chat_messages = [];

  $scope.insert_chat_message = function(content) {
    var chat_message = {
      name: "Me",
      style: "color:" + green_code,
      content: content,
      time: new Date()
    };
    $scope.chat_messages.push(chat_message);
    $scope.message_content = "";
    scroll_to_bottom_of_message_box();
    chat_message_to_server = {
      name: $scope.userInfo.email,
      style: "color:" + orange_code,
      content: content,
      time: new Date()
    };
    heroku_socket.emit("chat_message_send_to_other_machine_in_laboratory", chat_message_to_server);
  };

  heroku_socket.on("chat_message_send_to_other_machine_in_laboratory_send_by_server", function(chat_message) {
    $scope.chat_messages.push(chat_message);
    scroll_to_bottom_of_message_box();
  });

  if ($scope.userInfo.email) {
    heroku_socket.emit("notify_other_people_in_laboratory_iam_online", $scope.userInfo.email);
  };

  heroku_socket.on("notify_other_people_in_laboratory_iam_online_send_by_server", function(user_name) {
    var chat_message = {
      name: "Server",
      style: "color:" + red_code,
      content: "user <span style='corlor:" + orange_code + "'>" + user_name + "</span> is online." ,
      time: new Date()
    };
    $scope.chat_messages.push(chat_message);
    scroll_to_bottom_of_message_box();
  });

  $scope.chart_update_interval = $interval(function() {
    if ($scope.data_pointer < ecg_bin.length) {
      chart_update_interval_tasks_handle();
    } else {
      // $scope.data_pointer = 0;
      // chart_update_interval_tasks_handle();
      stop_all_intervals_and_timeouts();
      special_resume_chart_update_interval();
    };
  }, $scope.tick_speed);

  var heart_rate_interval_tasks_handle = function() {
    var fs   = $scope.plot_speed;
    var data = chart_data.series[0];
    var heart_rates = dsp.calculate_heart_rates(fs, data);
    for (i = 0; i < heart_rates.length; i++) {
      hrv_data.push(heart_rates[i]);
      hrh_data.push(heart_rates[i]);
    };
    $scope.heart_rate = Math.round(dsp.cal_mean(heart_rates));
    if ($scope.heart_rate <= 40) {
      $scope.heart_rate_condition = 2;
    } else {
      if ($scope.heart_rate <= 54) {
        $scope.heart_rate_condition = 1;
      } else {
        if ($scope.heart_rate <= 120) {
          $scope.heart_rate_condition = 0;
        } else {
          if ($scope.heart_rate <= 140) {
            $scope.heart_rate_condition = 1;
          } else {
            $scope.heart_rate_condition = 2;
          };
        };
      };
    };
    update_colors();
  };
  var variability_interval_tasks_handle = function() {
    var value = dsp.cal_std(hrv_data);
    value = (Math.floor(value * 100) / 100).toFixed(2);
    hrvh_data.push(value);
    $scope.variability = value;
    if (value <= 10) {
      $scope.variability_condition = 0;
    } else {
      if (value <= 20) {
        $scope.variability_condition = 1;
      } else {
        $scope.variability_condition = 2;
      }
    }
    update_colors();
    hrv_data = [];
  };
  var t_and_std_interval_tasks_handle = function() {
    var fs   = $scope.plot_speed;
    var data = chart_data.series[0];
    var qrs_locs = dsp.qrs_detect(fs, data);
    var result = dsp.t_peaks_detect(fs, data, qrs_locs);
    t_peaks = result[0];
    t_locs = result[1];
    var std_results = dsp.std_detect(fs, data, qrs_locs, t_locs);
    var t_peak = Math.round(dsp.cal_mean(t_peaks));
    var std_val = Math.round(dsp.cal_mean(std_results));
    // Push data into heathcare bin
    for (ali = 0; ali < t_peaks.length; ali++) {
      th_data.push(t_peaks[ali]);
    };
    for (ali = 0; ali < std_results.length; ali++) {
      stdh_data.push(std_results[ali]);
    };
    // End of healthcare bin
    // Handle t peak
    $scope.tmag = t_peak;
    if (t_peak <= -20) {
      $scope.tmag_condition = 2;
    } else {
      if (t_peak >= -20 && t_peak <= -10) {
        $scope.tmag_condition = 1;
      } else {
        if (t_peak >= 80) {
          $scope.tmag_condition = 1;
        } else {
          if (t_peak >= -10 && t_peak <= 4) {
            $scope.tmag_condition = 1;
          } else {
            $scope.tmag_condition = 0;
          }
        };
      };
    };
    // Hanlde std value
    $scope.std_val = std_val;
    if (std_val <= -20 || std_val >= 30) {
      $scope.std_condition = 2;
    } else {
      if (std_val >= -20 && std_val <= -10) {
        $scope.std_condition = 1;
      } else {
        if (std_val >= 20 && std_val <= 30) {
          $scope.std_condition = 1;
        } else {
          $scope.std_condition = 0;
        };
      }

    };
    update_colors();
  };
  var health_interval_tasks_handle = function() {
    var mean_hr = dsp.cal_mean(hrh_data);
    var mean_hrv = dsp.cal_mean(hrvh_data);
    var mean_t = dsp.cal_mean(th_data);
    var mean_std = dsp.cal_mean(stdh_data);
    perform_diagnosis_for_these_features(mean_hr, mean_hrv, mean_std, mean_t);
    $scope.health_condition = $scope.check_for_health_color();
    update_colors();
    var obj = {
      mean_hr: mean_hr,
      mean_hrv: $scope.variability,
      mean_t: mean_t,
      mean_std: mean_std,
      health_status: $scope.health,
      health_status_color: colors_pool[$scope.health_condition],
    };
    heroku_socket.emit("features_extraction_result_from_desktop_app_to_phone", obj);
    hrh_data = [];
    hrvh_data = [];
    stdh_data = [];
    th_data = [];
  };

  var using_intervals_to_diagnose = function(hr, vari, heal) {
    if (hr) {
      $scope.heart_rate_interval = $interval(function() {
        heart_rate_interval_tasks_handle();
      }, hr);
    };
    if (vari) {
      $scope.variability_interval = $interval(function () {
        variability_interval_tasks_handle();
      }, vari);
    };
    if (heal) {
      $scope.health_interval = $interval(function () {
        health_interval_tasks_handle();
      }, heal);
    };
  };

  //using_intervals_to_diagnose(2000, 2000, 2000);

  socket.on("diag_server-welcome-new-user", function(data) {
    var chat_message = {
      name: "Server",
      style: "color:" + red_code,
      content: "Hello there, group conversation goes here. Enjoy :)",
      time: new Date()
    };
    $scope.chat_messages.push(chat_message);
  });

  var transform_statistics = function(array) {
    var total = array[0] + array[1] + array[2];
    array[0] = Math.floor(array[0] / total * 100);
    array[1] = Math.floor(array[1] / total * 100);
    array[2] = 100 - array[0] - array[1];
    return array;
  };

  $scope.open_popup_upload_record = function() {
    // stop_all_intervals_and_timeouts();
    jQuery("#upload_record_popup").show();
    jQuery("#upload_record_popup > form > .div_small_popup").animate({
      top: 90,
      opacity: 1
    }, 400);
    $scope.custom_timeout = $timeout(function() {
      jQuery("#page_loading").show();
      var text_output_to_area = "";

      // NOT TO DELETE
      // IMPORTANT

      if ($scope.flag_from_receive_data_from_phone == 0) {
        var value_to_devine = dsp.find_max($scope.ecg_storage);
        for (var loop = 0; loop < $scope.ecg_storage.length - 1; loop++) {
          var value = Math.floor($scope.ecg_storage[loop] / value_to_devine * 10000) / 10000;
          text_output_to_area += (value + "\n");
        };
        var last_value = Math.floor($scope.ecg_storage[$scope.ecg_storage.length - 1] / value_to_devine * 10000) / 10000;
        text_output_to_area += (last_value);
      } else {
        for (var loop = 0; loop < $scope.ecg_storage.length - 1; loop++) {
          var value = $scope.ecg_storage[loop];
          text_output_to_area += (value + "\n");
        };
        var last_value = $scope.ecg_storage[$scope.ecg_storage.length - 1];
        text_output_to_area += (last_value);
      };

      // var value_to_devine = dsp.find_max($scope.ecg_storage);
      // for (var loop = 0; loop < $scope.ecg_storage.length - 1; loop++) {
      //   var value = Math.floor($scope.ecg_storage[loop] / value_to_devine * 10000) / 10000;
      //   text_output_to_area += (value + "\n");
      // };
      // var last_value = Math.floor($scope.ecg_storage[$scope.ecg_storage.length - 1] / value_to_devine * 10000) / 10000;
      // text_output_to_area += (last_value);

      $scope.file_content = text_output_to_area;
      $scope.update_duration();
      jQuery("#page_loading").hide();
      $scope.cancel_custom_timeout();
    }, 500);
  };
  $scope.open_popup_find_nearby_device = function() {
    jQuery("#find_nearby_devices_popup").show();
    jQuery("#find_nearby_devices_popup > .div_small_popup").animate({
      top: 90,
      opacity: 1
    }, 400);
  };
  $scope.close_popup_upload_record = function() {
    jQuery("#upload_record_popup > form > .div_small_popup").animate({
      top: 60,
      opacity: 0
    }, 400, function() {
      jQuery("#upload_record_popup").hide();
    });
  };
  $scope.close_popup_find_nearby_devices = function() {
    jQuery("#find_nearby_devices_popup > .div_small_popup").animate({
      top: 60,
      opacity: 0
    }, 400, function() {
      jQuery("#find_nearby_devices_popup").hide();
    });
  };

  if ($window.localStorage["cassandra_userInfo"]) {
    $scope.userInfo = JSON.parse($window.localStorage["cassandra_userInfo"]);
  };

  $scope.save_this_record = function() {
    jQuery("#page_loading").show();
    $scope.custom_timeout = $timeout(function() {
      var lines = $scope.file_content.split("\n");
      $scope.ecg_to_store = [];
      for (var loop = 0; loop < lines.length; loop++) {
        $scope.ecg_to_store.push(lines[loop]);
      };
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
        statistics: transform_statistics($scope.statistics_count),
        send_to_doctor: false,
        user_info: JSON.parse($window.localStorage["cassandra_userInfo"]),
      };
      $scope.record_data = {
        record_id: $scope.new_record.record_id,
        sampling_frequency: $scope.record_sampling_frequency,
        data: $scope.ecg_to_store,
        user_info: JSON.parse($window.localStorage["cassandra_userInfo"]),
      };
      if ($window.localStorage["cassandra_records"]) {
        $scope.records = JSON.parse($window.localStorage["cassandra_records"]);
      } else {
        $scope.records = [];
      };
      $scope.records.push($scope.new_record);
      $window.localStorage["cassandra_records"] = JSON.stringify($scope.records);
      $window.localStorage[$scope.record_data.record_id] = JSON.stringify($scope.record_data);
      socket.emit("save_this_record_to_local_server", $scope.new_record);
      var package_to_database_server = {
        record_info: $scope.new_record,
        record_data: $scope.record_data,
        user_info: $scope.userInfo,
      };
      heroku_socket.emit("save_this_record_directly_to_database_server", package_to_database_server);
      alert("Record saved successfully");
      jQuery("#page_loading").hide();
      $scope.cancel_custom_timeout();
      $scope.close_popup_upload_record();
    }, 1600);
  };
  jQuery("#page_loading").hide();
}]);
