var app = angular.module("app")
.controller("laboratoryController", ["$scope", "$http", "$rootScope", "$window", "printService", 'FileSaver', 'Blob', '$location', '$interval', 'dsp', '$timeout', function ($scope, $http, $rootScope, $window, printService, FileSaver, Blob, $location, $interval, dsp, $timeout) {
  console.log("analysis");
  $scope.innitiate_global_variables = function() {
    $scope.socket;
    $scope.heroku_socket;
    $scope.ecg_data = [];
    $scope.file_content = [];
    $scope.record_name = "";
    $scope.record_comment = "";
    $scope.record_sampling_frequency = 100;
    $scope.record_duration = Math.floor($scope.file_content.length / ($scope.record_sampling_frequency) * 10) / 10;
    $scope.record_date = new Date();
    // Down sampled ecg, fs = 40, used for plotting only
    $scope.ecg_bin = [];
    // FOR GRAPH, MUST NOT CHANGE, STABLE THE OUTCOME
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
    // Original ecg, fs predefined, used to store data
    $scope.ecg_storage = [];
    $scope.flag_from_receive_data_from_phone = 0;
    $scope.red_code = "#FF4081";
    // var orange_code = "#d17905";
    $scope.orange_code = "#FF9800";
    $scope.green_code = "#8BC34A";
    if ($window.localStorage["cassandra_userInfo"]) {
      $scope.userInfo = JSON.parse($window.localStorage["cassandra_userInfo"]);
    };
    $scope.custom_background = 'background/win.png';
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
  };
  $scope.initiate_chart_variables = function() {
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

    $scope.heart_rate_color       = $scope.colors_pool[$scope.heart_rate_condition];
    $scope.variability_color      = $scope.colors_pool[$scope.variability_condition];
    $scope.health_color           = $scope.colors_pool[$scope.health_condition];
    $scope.tmag_color             = $scope.colors_pool[$scope.tmag_condition];
    $scope.std_color              = $scope.colors_pool[$scope.std_condition];

    $scope.statistics_count = [0, 0, 0];
    $scope.chart_spanner_width = 100;
    $scope.chart_spanner_position = 0;

    $scope.show_ecg_chart = true;
    $scope.show_variability_chart = true;
    $scope.show_heartrate_chart = true;
    $scope.show_tmag_chart = true;
    $scope.show_stdeviation_chart = true;

    $scope.should_show_ecgchart_timeline = true;
    $scope.ecgchart_length_of_1s_in_pixel = (jQuery("#ecg_chart_container").width() - 16) * 1 / 4;
    $scope.secondarychart_intervals = [];
  };
  $scope.initiate_window_settings = function() {
    $scope.plot_speed = 40;             // 50 points per 1,000ms
    $scope.tick_speed = Math.floor(1000 / $scope.plot_speed);
    $scope.chart_pointer = 0;
    $scope.window_span = 4;             // in seconds
    $scope.window_leng = $scope.plot_speed * $scope.window_span;
    $scope.window_percentage = 100;
    $scope.window_leng_30s = false;
    $scope.window_leng_10s = false;
    $scope.window_leng_4s = true;
  };
  $scope.initiate_global_functions = function() {
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
    $scope.random_data = function(number_of_point, scale) {
      var data = [];
      for (i = 0; i < number_of_point; i++) {
        data.push(Math.random() * scale);
      };
      return data;
    };
    $scope.update_duration = function() {
      $scope.record_duration = Math.floor($scope.ecg_storage.length / ($scope.record_sampling_frequency) * 10) / 10;
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
    $scope.chart_data = {
      series: [
        $scope.random_data($scope.window_leng, 800),
      ]
    };
    $scope.transform_statistics = function(array) {
      var total = array[0] + array[1] + array[2];
      array[0] = Math.floor(array[0] / total * 100);
      array[1] = Math.floor(array[1] / total * 100);
      array[2] = 100 - array[0] - array[1];
      return array;
    };
    $scope.create_chart_options = function(x_axis_ops, y_axis_ops, high_val, low_val) {
      var obj = {
        showPoint: false,
        showArea: true,
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
        lineSmooth: Chartist.Interpolation.cardinal({
          fillHoles: true,
        }),
        chartPadding: {
          right: 20,
          bottom: 0
        },
      };
      return obj;
    };
    $scope.create_chart_options_without_highlow = function(x_axis_ops, y_axis_ops) {
      var obj = {
        showPoint: false,
        showArea: true,
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
    $scope.initiate_chart_data_first_time = function() {
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
    $scope.perform_diagnosis_for_these_features = function(hr, hrv, std, tp) {
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
    $scope.initiate_ecg_chart = function(max_val, min_val, chart_data) {
      var chart_data_obj = {
        series: [
          [],[],[],
          chart_data
        ]
      };
      var chart_option_obj = {
        showPoint: true,
        showArea: false,
        fullWidth: true,
        high: max_val,
        low: min_val,
        axisX: {
          showGrid: false,
          showLabel: false,
        },
        axisY: {
          showGrid: true,
          showLabel: false,
        },
        lineSmooth: Chartist.Interpolation.cardinal({
          fillHoles: true,
        }),
        chartPadding: {
          right: 20,
          bottom: 0
        },
      };
      $scope.ecg_chart = new Chartist.Line('#ecg_chart', chart_data_obj, $scope.create_chart_options([false, false], [false, false], max_val, min_val));
      // $scope.ecg_chart = new Chartist.Line('#ecg_chart', chart_data_obj, chart_option_obj);
      $scope.ecg_chart_label = new Chartist.Line('#ecg_chart_label', {series: [[]]}, $scope.create_chart_options([false, false], [false, true], max_val, min_val));
    };
    $scope.initiate_heartrate_chart = function(max_val, min_val, chart_data) {
      var chart_data_obj = {
        series: [
          [],[],[],[],
          chart_data
        ]
      };
      if (max_val < 80) {
        max_val = 80;
      }
      $scope.heartrate_chart = new Chartist.Line('#heartrate_chart', chart_data_obj, $scope.create_chart_options([false, false], [false, true], max_val, min_val));
      var chart_grid_data_obj = {
        series: [
          [],[],[],[],[],[],[],[],
          chart_data
        ]
      };
      // $scope.heartrate_chart_grid = new Chartist.Line('#heartrate_chart_grid', chart_grid_data_obj, $scope.create_chart_options([false, false], [false, false], max_val, min_val));
    };
    $scope.initiate_variability_chart = function(max_val, min_val, chart_data) {
      var chart_data_obj = {
        series: [
          [],[],[],[],[],
          chart_data
        ]
      };
      if (max_val < 15) {
        max_val = 15;
      };
      if (min_val > 0) {
        min_val = 0;
      };
      $scope.variability_chart = new Chartist.Line('#variability_chart', chart_data_obj, $scope.create_chart_options([false, false], [false, true], max_val, min_val));
      $scope.variability_chart.on('draw', function(context) {
        if(context.type === 'label' && context.axis.units.pos === 'y') {
          context.element.attr({
            x: context.axis.chartRect.width() + 40
          });
        }
      });
    };
    $scope.initiate_tmag_chart = function(max_val, min_val, chart_data) {
      var chart_data_obj = {
        series: [
          [],[],[],[],[],[],
          chart_data
        ]
      };
      if (max_val <= 120 && max_val > 90 && min_val >= 0) {
        max_val = 120;
        min_val = 0;
      };
      if (max_val <= 90 && min_val >= 0) {
        max_val = 90;
        min_val = 0;
      };
      if (max_val <= 10 && min_val < 0) {
        max_val = 0;
      };
      $scope.tmag_chart = new Chartist.Line('#tmag_chart', chart_data_obj, $scope.create_chart_options([false, false], [false, true], max_val, min_val));
    };
    $scope.initiate_stdeviation_chart = function(max_val, min_val, chart_data) {
      var chart_data_obj = {
        series: [
          [],[],[],[],[],[],[],
          chart_data
        ]
      };
      if (max_val <= 60 && min_val >= 0) {
        max_val = 60;
        min_val = 0;
      };
      if (max_val <= 10 && min_val < 0) {
        max_val = 0;
      };
      $scope.stdeviation_chart = new Chartist.Line('#stdeviation_chart', chart_data_obj, $scope.create_chart_options([false, false], [false, true], max_val, min_val));
      $scope.stdeviation_chart.on('draw', function(context) {
        if(context.type === 'label' && context.axis.units.pos === 'y') {
          context.element.attr({
            x: context.axis.chartRect.width() + 40
          });
        }
      });
    };
    $scope.initiate_statistic_chart = function(normal, risk, danger) {
      var chart = new Chartist.Pie('#statistic_chart', {
        series: [normal, risk, danger],
        labels: ["Normal", "Risk", "Danger"]
      }, {
        donut: true,
        donutWidth: 48,
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
    $scope.select_window_leng_all = function() {
      $scope.window_percentage = 100;
      $scope.chart_spanner_width = 100;
      jQuery("#ecg_chart_container").scrollLeft(0);
      $scope.chart_spanner_position = 0;
      $scope.window_leng_all = true;
      $scope.window_leng_30s = false;
      $scope.window_leng_10s = false;
      $scope.window_leng_4s = false;
      var chart_max_value = dsp.find_max($scope.ecg_bin);
      var chart_min_value = dsp.find_min($scope.ecg_bin);
      $scope.should_show_ecgchart_timeline = false;
      $scope.initiate_ecg_chart(chart_max_value, chart_min_value, $scope.ecg_bin);
      $scope.recalculate_annotations_margin();
    };
    $scope.select_window_leng_30s = function() {
      $scope.calculate_window_percentage(30);
      $scope.calcualte_chart_spanner_width(30);
      jQuery("#ecg_chart_container").scrollLeft(0);
      $scope.chart_spanner_position = 0;
      $scope.window_leng_all = false;
      $scope.window_leng_30s = true;
      $scope.window_leng_10s = false;
      $scope.window_leng_4s = false;
      var chart_max_value = dsp.find_max($scope.ecg_bin);
      var chart_min_value = dsp.find_min($scope.ecg_bin);
      $scope.initiate_ecg_chart(chart_max_value, chart_min_value, $scope.ecg_bin);
      $scope.should_show_ecgchart_timeline = true;
      $scope.calculate_ecgchart_1s_length_in_pixel(30);
      $scope.recalculate_annotations_margin();
    };
    $scope.select_window_leng_10s = function() {
      $scope.calculate_window_percentage(10);
      $scope.calcualte_chart_spanner_width(10);
      jQuery("#ecg_chart_container").scrollLeft(0);
      $scope.chart_spanner_position = 0;
      $scope.window_leng_all = false;
      $scope.window_leng_30s = false;
      $scope.window_leng_10s = true;
      $scope.window_leng_4s = false;
      var chart_max_value = dsp.find_max($scope.ecg_bin);
      var chart_min_value = dsp.find_min($scope.ecg_bin);
      $scope.initiate_ecg_chart(chart_max_value, chart_min_value, $scope.ecg_bin);
      $scope.should_show_ecgchart_timeline = true;
      $scope.calculate_ecgchart_1s_length_in_pixel(10);
      $scope.recalculate_annotations_margin();
    };
    $scope.select_window_leng_4s = function() {
      $scope.calculate_window_percentage(4);
      $scope.calcualte_chart_spanner_width(4);
      jQuery("#ecg_chart_container").scrollLeft(0);
      $scope.chart_spanner_position = 0;
      $scope.window_leng_all = false;
      $scope.window_leng_30s = false;
      $scope.window_leng_10s = false;
      $scope.window_leng_4s = true;
      var chart_max_value = dsp.find_max($scope.ecg_bin);
      var chart_min_value = dsp.find_min($scope.ecg_bin);
      $scope.initiate_ecg_chart(chart_max_value, chart_min_value, $scope.ecg_bin);
      $scope.should_show_ecgchart_timeline = true;
      $scope.calculate_ecgchart_1s_length_in_pixel(4);
      $scope.recalculate_annotations_margin();
    };
    $scope.calculate_window_percentage = function(span) {
      $scope.window_percentage = Math.ceil($scope.ecg_bin.length / (span * 40) * 100);
    };
    $scope.calcualte_chart_spanner_width = function(span) {
      $scope.chart_spanner_width = Math.ceil(span * 40 / $scope.ecg_bin.length * 10000) / 100;
    };
    $scope.handle_ecg_chart_container_croll_event = function() {
      jQuery("#ecg_chart_container").scroll(function() {
        var scrolled_pixels = jQuery(this).scrollLeft();
        var scrolled_percent = scrolled_pixels / jQuery("#ecg_chart").width();
        $scope.$apply(function() {
          $scope.chart_spanner_position = Math.ceil(scrolled_percent * 10000) / 100;
        });
      });
    };
    $scope.handle_spanner_drag_event = function() {
        var $body = jQuery('body');
  		  var $target = null;
  		  var isDraggEnabled = false;
		    $body.on("mousedown", ".chart_spanner", function(e) {
		    	var $this = jQuery(this);
		      isDraggEnabled = $this.data("draggable");
		      if (isDraggEnabled) {
		       	if(e.offsetX==undefined) {
						  x = e.pageX - jQuery(this).offset().left;
					  } else{
						  x = e.offsetX;
					  };
					  $this.addClass('draggable');
		        $body.addClass('noselect');
		        $target = jQuery(e.target);
		      };
		    });

		    $body.on("mouseup", function(e) {
		      $target = null;
		      $body.find(".draggable").removeClass('draggable');
		      $body.removeClass('noselect');
		    });

		     $body.on("mousemove", function(e) {
	         if ($target) {
             var value;
             if (jQuery(window).width() < 1200) {
               value = e.pageX - x - 50;
             } else {
               value = e.pageX - x - 50 - jQuery(window).width() * 0.25 + 4;
             }
             var min_offset = 0;
             var max_offset = jQuery(".chart_spanner_container").width() - $target.width();
             if (value < min_offset) {
               value = min_offset;
             };
             if (value > max_offset) {
               value = max_offset;
             };
	           //$target.css("margin-left", value);
             $scope.$apply(function() {
               $scope.chart_spanner_position = value / jQuery(".chart_spanner_container").width() * 100;
               var pixel_to_scroll = $scope.chart_spanner_position / 100 * jQuery("#ecg_chart").width();
               jQuery("#ecg_chart_container").scrollLeft(pixel_to_scroll);
             });
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
        type: "",
      };
      if (std >= 35 && tp >= 80) {
        // $scope.health_condition = 2;
        $scope.statistics_count[2] += 1;
        obj.text = "ST+";
        obj.type = "ST Elevation";
        obj.color = $scope.ann_danger;
        obj.tooltip = "<b>ST Elevation</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      };
      if (std <= -20 && tp <= -20) {
        // $scope.health_condition = 2;
        $scope.statistics_count[2] += 1;
        obj.text = "ST-";
        obj.type = "ST Depression";
        obj.color = $scope.ann_danger;
        obj.tooltip = "<b>ST Depression</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      };
      if (tp >= 160 && hr <= 70 && std < 35) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "PVC";
        obj.type = "Premature Ventricular Contraction";
        obj.color = $scope.ann_human;
        obj.tooltip = "<b>Premature Ventricular Complex</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      } else {
        if (hrv >= 20 && hr <= 70 && tp < 0) {
          // $scope.health_condition = 1;
          $scope.statistics_count[1] += 1;
          obj.text = "PVC";
          obj.type = "Premature Ventricular Contraction";
          obj.color = $scope.ann_human;
          obj.tooltip = "<b>Premature Ventricular Complex</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
          return obj;
        }
      }
      if (hrv >= 10 && (hr >= 120 || hr <= 70)) {
        // $scope.health_condition = 2;
        $scope.statistics_count[1] += 1;
        obj.text = "ARR";
        obj.type = "Arrythmia";
        obj.color = $scope.ann_caution;
        obj.tooltip = "<b>Arrythmia</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      };

      if (tp <= -5) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "T-";
        obj.type = "T Wave Invert";
        obj.color = $scope.ann_caution;
        obj.tooltip = "<b>T Inverted</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      };

      if (tp >= 100) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "T+";
        obj.type = "T Wave Peaked";
        obj.color = $scope.ann_caution;
        obj.tooltip = "<b>T Peaked</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      };

      if (std <= -8 || std >= 20) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.color = $scope.ann_red;
        if (std <= -8) {
          obj.text = "SD-";
          obj.type = "Negative ST Deviation";
          obj.tooltip = "<b>Negative ST Deviation</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        } else {
          obj.text = "SD+";
          obj.type = "Positive ST Deviation";
          obj.tooltip = "<b>Positive ST Deviation</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        }
        // obj.text = "STD";
        // obj.tooltip = "ST Deviation";
        return obj;
      };


      if (hr >= 140) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "TAR";
        obj.type = "Tarchycardia";
        obj.color = $scope.ann_human;
        obj.tooltip = "<b>Tarchycardia</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      };

      if (hr <= 50) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "BRA";
        obj.type = "Bradycardia";
        obj.color = $scope.ann_human;
        obj.tooltip = "<b>Bradycardia</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      };
      if (tp >= -5 && tp <= 5) {
        // $scope.health_condition = 1;
        $scope.statistics_count[1] += 1;
        obj.text = "T0";
        obj.type = "T Wave Absence";
        obj.color = $scope.ann_caution;
        obj.tooltip = "<b>T Absence</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      };
      // $scope.health_condition = 0;
      if (tp > 5 && hr > 40 && hr < 140 && hrv < 10 && std > -8 && std < 20) {
        // $scope.health_condition = 1;
        $scope.statistics_count[0] += 1;
        obj.text = "N";
        obj.type = "Normal Beat";
        obj.color = $scope.ann_normal;
        obj.tooltip = "<b>Normal</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
        return obj;
      };
      $scope.statistics_count[0] += 1;
      obj.text = "M";
      obj.type = "Missed Beat";
      obj.color = $scope.ann_green;
      obj.tooltip = "<b>Missed Beat</b><br/><ul><li>HR:  " + hr + "</li><li>T:  " + tp + "%</li><li>ST:  " + std + "%</li></ul>";
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
    $scope.recalculate_annotations_margin = function() {
      var calculated_width = (jQuery("#ecg_chart_container").width()) * 3;
      console.log(calculated_width);
      for (var loop = 0; loop < $scope.annotations.length; loop++) {
        $scope.annotations[loop].left = ($scope.qrs_locs_bin_ann[loop] - 2) / $scope.ecg_bin.length * (jQuery("#ecg_chart_container").width() * $scope.window_percentage / 100 + 60) - ($scope.qrs_locs_bin_ann[loop] - 2) / $scope.ecg_bin.length * 66;
        $scope.annotations[loop].percent = Math.floor($scope.annotations[loop].left / calculated_width * 10000) / 100;
      };
    };
    $scope.update_statistics_count = function() {
      $scope.statistics_count = $scope.transform_statistics($scope.statistics_count);
    };
    $scope.calculate_ecgchart_1s_length_in_pixel = function(span) {
      $scope.ecgchart_length_of_1s_in_pixel = (jQuery("#ecg_chart_container").width() - 16) * 1 / span;
    };
    $scope.fill_out_mising_pieces = function(data) {
      for (var loop = 0; loop < data.length; loop++) {
        if (!data[loop]) {
          if (!data[loop - 1]) {
            for (var increase = 0; increase < data.length; increase++) {
              if (data[loop + increase]) {
                data[loop] = data[loop + increase];
                break;
              };
            };
          } else {
            if (data[loop - 2]) {
              data[loop] = (data[loop - 1] + data[loop - 2]) / 2;
            } else {
              data[loop] = data[loop - 1];
            };
          };
        };
      };
      return data;
    };
    $scope.check_and_increase_this_annotation_statistic = function(ann) {
      for (var ind = 0; ind < $scope.annotations_statistic.length; ind++) {
        if ($scope.annotations_statistic[ind].text == ann.text ) {
          $scope.annotations_statistic[ind].count += 1;
          return;
        }
      };
      var obj = {
        text: ann.text,
        count: 1,
        color: ann.color,
        tooltip: ann.tooltip,
        percentage: 0,
        type: ann.type,
      };
      $scope.annotations_statistic.push(obj);
    }
    $scope.generate_annotation_statistics = function() {
      $scope.annotations_statistic = [];
      var obj = {
        text: $scope.annotations[0].text,
        count: 1,
        color: $scope.annotations[0].color,
        tooltip: $scope.annotations[0].tooltip,
        percentage: 0,
        type: $scope.annotations[0].type,
      };
      $scope.annotations_statistic.push(obj);
      for (var loop = 1; loop < $scope.annotations.length; loop++) {
        $scope.check_and_increase_this_annotation_statistic($scope.annotations[loop]);
      };
      // console.log($scope.annotations_statistic);
    };
    $scope.normalize_annotation_statistics = function() {
      var arr = [];
      for (var loop = 0; loop < $scope.annotations_statistic.length; loop++) {
        arr.push($scope.annotations_statistic[loop].count);
      };
      var maximum_val = dsp.find_max(arr);
      for (var loop = 0; loop < $scope.annotations_statistic.length; loop++) {
        $scope.annotations_statistic[loop].percentage = $scope.annotations_statistic[loop].count / maximum_val * 100;
      };
      console.log($scope.annotations_statistic);
    };
    $scope.scroll_to_bottom_of_message_box = function() {
      $scope.scroll_timeout = $timeout(function() {
        jQuery("#div_chat_content").animate({ scrollTop: jQuery(this).height() }, 400);
        $timeout.cancel($scope.scroll_timeout);
      }, 100);
    };
    $scope.insert_chat_message = function(content) {
      var chat_message = {
        name: "Me",
        style: "color:" + $scope.green_code,
        content: content,
        time: new Date()
      };
      // console.log("OK");
      $scope.chat_messages.push(chat_message);
      $scope.message_content = "";
      $scope.custom_timeout = $timeout(function() {
        $scope.scroll_to_bottom_of_message_box();
        $timeout.cancel($scope.custom_timeout);
        $scope.custom_timeout = null;
      }, 40);

      chat_message_to_server = {
        name: $scope.userInfo.email,
        style: "color:" + $scope.orange_code,
        content: content,
        time: new Date()
      };
      // heroku_socket.emit("chat_message_send_to_other_machine_in_laboratory", chat_message_to_server);
    };
    $scope.export_this_analysis_as_pdf_report = function() {
      var reportObj = {
        record_info: $scope.record_of_interest,
        record_data: {
          wave_form: $scope.ecg_bin,
          sampling_frequency: $scope.sampling_frequency,
          duration: $scope.record_length_in_seconds,
        },
        record_statistics: $scope.statistics_count,
        record_annotations: $scope.annotations,
        record_annotations_statistic: $scope.annotations_statistic,
        record_graphs: {
          heart_rate: $scope.heartrate_bin_ann,
          variability: $scope.variability_bin_ann,
          tmag: $scope.tmag_bin_ann,
          stdeviation: $scope.stdeviation_bin_ann,
        }
      };
      // $scope.socket.emit("export_this_analysis_as_pdf_report_send_to_server", reportObj);
      $window.localStorage["cassandra_command_print_to_print_this_report_as_pdf"] = JSON.stringify(reportObj);
      $window.open("print.html", "_blank", 'width=1280,height=720');
    };
  };
  $scope.animate_on_pageload = function() {
    jQuery("#upload_record_popup").hide();
    jQuery("#message_box").hide();
    jQuery("#page_loading").show();
    $scope.loading_message = "Loading components. Please wait ...";
  };
  $scope.connect_socketio_on_pageload = function() {
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
  };
  $scope.handling_data_on_pageload = function() {
    $scope.record_of_interest = JSON.parse($window.localStorage["cassandra_command_analysis_to_run_this_signal"]);
    $window.localStorage.removeItem("cassandra_command_lab_to_run_this_signal");
    var record_data_id = $scope.record_of_interest.record_id;
    $scope.record_name = $scope.record_of_interest.name;
    $scope.record_comment = $scope.record_of_interest.description;
    $scope.record_data_to_display = JSON.parse($window.localStorage[record_data_id]);
    $scope.ecg_bin = $scope.record_data_to_display.data;
    $scope.ecg_storage = $scope.ecg_bin;
    console.log("Analize record: " + record_data_id + ", length " + $scope.ecg_bin.length);
    $scope.sampling_frequency = $scope.record_data_to_display.sampling_frequency;
    $scope.record_sampling_frequency = $scope.sampling_frequency;
  };
  $scope.preprocess_signal = function() {
    $scope.down_sampling_value = Math.floor($scope.sampling_frequency / $scope.plot_speed);
    if (dsp.find_max($scope.ecg_bin[0]) < 10) {
      for (i = 0; i < $scope.ecg_bin.length; i ++) {
        $scope.ecg_bin[i] = $scope.ecg_bin[i] * 1000;
      };
    };
    var value = dsp.cal_mean($scope.ecg_bin);
    for (i = 0; i < $scope.ecg_bin.length; i ++) {
      $scope.ecg_bin[i] = $scope.ecg_bin[i] - value;
    };
    $scope.ecg_bin = dsp.noise_removal_using_low_pass_filter($scope.ecg_bin);
    $scope.ecg_bin = dsp.smooth_signal_with_moving_avarage(4, $scope.ecg_bin);
    $scope.ecg_bin = dsp.down_sampling($scope.down_sampling_value, $scope.ecg_bin);
    $scope.ecg_bin = dsp.baseline_remove_using_moving_average($scope.ecg_bin);
    $scope.record_length_in_seconds = Math.floor($scope.ecg_bin.length / 40);
    for (var loop = 0; loop < 8; loop++) {
      $scope.secondarychart_intervals.push(Math.floor($scope.record_length_in_seconds / 8 * loop));
    };
    $scope.record_length_in_seconds_arr = [];
    for (var loop = 0; loop < $scope.record_length_in_seconds - 1; loop++) {
      $scope.record_length_in_seconds_arr.push(1);
    };
  };
  $scope.preprocess_signal_before_charting = function() {
    $scope.qrs_locs_bin = $scope.qrs_locs_bin_ann;
    $scope.heartrate_bin = $scope.heartrate_bin_ann;
    $scope.variability_bin = $scope.variability_bin_ann;
    $scope.tmag_bin = $scope.tmag_bin_ann;
    $scope.stdeviation_bin = $scope.stdeviation_bin_ann;
    $scope.stdeviation_bin = $scope.fill_out_mising_pieces($scope.stdeviation_bin);
    $scope.heartrate_bin = $scope.fill_out_mising_pieces($scope.heartrate_bin);
    $scope.variability_bin = $scope.fill_out_mising_pieces($scope.variability_bin);
    $scope.tmag_bin = $scope.fill_out_mising_pieces($scope.tmag_bin);
  };
  $scope.initiate_charts = function() {
    $scope.calculate_window_percentage(4);
    $scope.calcualte_chart_spanner_width(4);
    $scope.handle_ecg_chart_container_croll_event();
    $scope.handle_spanner_drag_event();
    var chart_max_value = dsp.find_max($scope.ecg_bin);
    var chart_min_value = dsp.find_min($scope.ecg_bin);
    $scope.initiate_ecg_chart(chart_max_value, chart_min_value, $scope.ecg_bin);
    var chart_max_value = dsp.find_max($scope.heartrate_bin);
    var chart_min_value = dsp.find_min($scope.heartrate_bin);
    $scope.initiate_heartrate_chart(chart_max_value, chart_min_value, $scope.heartrate_bin_ann);
    var chart_max_value = dsp.find_max($scope.variability_bin);
    var chart_min_value = dsp.find_min($scope.variability_bin);
    $scope.initiate_variability_chart(chart_max_value, chart_min_value, $scope.variability_bin_ann);
    var chart_max_value = dsp.find_max($scope.tmag_bin);
    var chart_min_value = dsp.find_min($scope.tmag_bin);
    $scope.initiate_tmag_chart(chart_max_value, chart_min_value, $scope.tmag_bin_ann);
    var chart_max_value = dsp.find_max($scope.stdeviation_bin);
    var chart_min_value = dsp.find_min($scope.stdeviation_bin);
    $scope.initiate_stdeviation_chart(chart_max_value, chart_min_value, $scope.stdeviation_bin_ann);
  };
  $scope.animate_on_pageload();
  $scope.innitiate_global_variables();
  $scope.initiate_chart_variables();
  $scope.initiate_window_settings();
  $scope.initiate_global_functions();
  $scope.connect_socketio_on_pageload();
  jQuery("#page_loading").hide();
  if ($window.localStorage["cassandra_command_analysis_to_run_this_signal"]) {
    $scope.handling_data_on_pageload();
    $scope.preprocess_signal();
    $scope.generate_annotations_for_this_segment();
    $scope.preprocess_signal_before_charting();
    $scope.initiate_charts();
    $scope.recalculate_annotations_margin();
    $scope.generate_annotation_statistics();
    $scope.update_statistics_count();
    $scope.normalize_annotation_statistics();
    $scope.initiate_statistic_chart($scope.statistics_count[0],$scope.statistics_count[1],$scope.statistics_count[2]);
  };
  jQuery(window).on("resize", function() {
    $scope.recalculate_annotations_margin();
  });
}]);
