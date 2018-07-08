var app = angular.module("app", ['ngRoute', 'ngSanitize', 'ngFileSaver']);

app.config(function ($routeProvider) {
  $routeProvider
    // .when("/", {
    //   templateUrl: "templates/homeTemplate_2.html",
    //   controller: "mainController"
    // })
    .when("/", {
      templateUrl: "templates/recordsTemplate.html",
      controller: "recordsController"
    })
    .when("/dashboard", {
      templateUrl: "templates/homeTemplate_2.html",
      controller: "mainController"
    })
    // .when("/laboratory", {
    //   templateUrl: "templates/laboratoryTemplate.html",
    //   controller: "laboratoryController"
    // })
    .when("/personal", {
      templateUrl: "templates/personalTemplate.html",
      controller: "personalController"
    })
    .when("/records", {
      templateUrl: "templates/recordsTemplate.html",
      controller: "recordsController"
    })
    .otherwise(
      { redirectTo: "/dashboard"}
    );
});

app.service("printService", function () {
  var data = "random";
  var saveProject = function (string) {
    data = string;
  };
  var exportProject = function () {
    return data;
  };
  return {
    set: saveProject,
    return: exportProject
  };
});

app.directive('onReadFile', function ($parse) {
	return {
		restrict: 'A',
		scope: false,
		link: function(scope, element, attrs) {
            var fn = $parse(attrs.onReadFile);

			element.on('change', function(onChangeEvent) {
				var reader = new FileReader();
				reader.onload = function(onLoadEvent) {
					scope.$apply(function() {
						fn(scope, {$fileContent:onLoadEvent.target.result});
					});
				};

        reader.onprogress = function(event) {

        };

				reader.readAsText((onChangeEvent.srcElement || onChangeEvent.target).files[0]);
			});
		}
	};
});

app.service ('dsp', function() {
  this.moving_average = function(span, scale, data) {
    var output = [];
    if (scale == null) {
      scale = 1;
    };
    for (var i = 0; i < data.length - span; i++) {
      var sum = data[i] * scale;
      for (var k = 2; k <= span; k++) {
        sum += data[i + k - 1];
      };
      var result = Math.floor(sum / (span + scale - 1));
      if (result) {
        output.push(result);
      } else {
        output.push(null);
      };
    };
    return data;
  };
  this.down_sampling = function(factor, data) {
    factor = Math.round(factor);
    if (factor <= 1) {
      return data;
    };
    var result = [];
    for (var i = 0; i < data.length - factor; i += factor) {
      var value = 0;
      for (var k = 1; k <= factor; k++) {
        value += data[i + k - 1];
      };
      value = Math.floor(value / factor);
      result.push(value);
    };
    return result;
  };
  this.magnify_maximum = function(data, baseline, power) {
    var new_data = [];
    if (baseline == null) {
      baseline = 250;
    };
    if (power == null) {
      power = 4;
    };
    for (var i = 1; i < data.length - 1; i++) {
      if ((data[i] > data[i - 1]) && (data[i] > data[i + 1])) {
        var value = Math.pow((Math.pow(data[i] - data[i - 1], power) + Math.pow(data[i] - data[i + 1], power)), 1 / power)  + baseline;
        new_data.push(value);
      } else {
        new_data.push(baseline);
      };
    };
    return new_data;
  };
  this.cal_mean = function(data) {
    var sum = 0;
    var deduce = 0;
    for (var i = 0; i < data.length; i++) {
      if (data[i]) {
        sum += data[i];
      } else {
        deduce += 1;
      };
    };
    return sum / (data.length - deduce);
  };
  this.cal_std = function(data) {
    var mean = this.cal_mean(data);
    var sum_of_sqrt = 0;
    var deduce = 0;
    for (var i = 0; i < data.length; i++) {
      if (data[i]) {
        sum_of_sqrt += Math.pow(data[i] - mean, 2);
      } else {
        deduce += 1;
      };
    };
    return Math.sqrt(sum_of_sqrt / (data.length - deduce - 1));
  };
  this.find_max = function(data) {
    var max = data[0];
    for (var i = 0; i < data.length; i++) {
      if (data[i] > max) {
        max = data[i];
      };
    };
    return max;
  };
  this.find_min = function(data) {
    var min = data[0];
    for (var i = 0; i < data.length; i++) {
      if (data[i] < min) {
        min = data[i];
      };
    };
    return min;
  };
  this.find_peaks = function(data, min_peak_value, min_peak_distance) {
    var peak_locs = [];
    for (var i = 1; i < data.length - 1; i++) {
      if ((data[i] > data[i - 1]) && (data[i] > data[i + 1])) {
        peak_locs.push(i);
      };
    };
    if (min_peak_value != null) {
      var new_peak_locs = [];
      for (var i = 0; i < peak_locs.length; i++) {
        if (data[peak_locs[i]] >= min_peak_value) {
          new_peak_locs.push(peak_locs[i]);
        };
      };
      peak_locs = new_peak_locs;
    };
    if (min_peak_distance != null) {
      var new_peak_locs = [peak_locs[0]];
      var last_peak_index = peak_locs[0];

      for (var i = 1; i < peak_locs.length; i++) {
        if ((peak_locs[i] - last_peak_index) > min_peak_distance) {
          new_peak_locs.push(peak_locs[i]);
          last_peak_index = peak_locs[i];
        };
      };

      peak_locs = new_peak_locs;
    };
    return peak_locs;
  };
  this.perform_normalization = function(data) {
    var result = [];
    var min_value = this.find_min(data);
    if (min_value < 0) {
      var value_to_add = Math.abs(min_value);
      for (norm_1 = 0; norm_1 < data.length; norm_1++) {
        result.push(data[norm_1] + value_to_add);
      };
    };
    // Normalize here
    var max_value = this.find_max(result);
    for (norm_2 = 0; norm_2 < result.length; norm_2++) {
      result[norm_2] = Math.floor(result[norm_2] / max_value * 1000);
    };
    return result;
  };
  this.create_threshold = function(points, value) {
    var result = [];
    for (var i = 0; i < points; i++) {
      result.push(value);
    };
    return result;
  };
  this.qrs_detect = function(fs, data, baseline, power) {
    var max_hr_hz = 3;       // around 180 bpm
    if (baseline == null) {
      baseline = 250;
    };
    if (power == null) {
      power = 1;            // SWEET SPOT, NOT TO CHANGE
    };
    data2 = this.magnify_maximum(data, baseline, power);
    // var min_peak_value = this.cal_mean(data2) + 1.5 * this.cal_std(data2);
    var min_peak_value = this.cal_mean(data2) + 1 * this.cal_std(data2);
    var min_peak_distance = Math.floor(1 / max_hr_hz * fs);
    try {
      var qrs_locs = this.find_peaks(data2, min_peak_value, min_peak_distance);
    } catch(err) {
      return null;
    };
    return qrs_locs;
  };
  var last_t_loc_1, last_t_loc_2;
  var last_t_val_1, last_t_val_2;
  var last_maximum_peak_ind;
  this.t_peaks_detect = function(fs, ecg_data, qrs_locs, baseline, power) {
    if (baseline == null) {
      baseline = 250;
    };
    if (power == null) {
      power = 5;
    };
    if (qrs_locs == null) {
      qrs_locs = this.qrs_detect(fs, ecg_data, baseline, power);
    };
    var t_peaks = [];
    var t_locs = [];
    // var iso = this.cal_mean(ecg_data);
    for (var hk = 0; hk < qrs_locs.length; hk++) {
      var delay_of_qrs = 1;
      var delay_of_iso = -2;
      var qrs = ecg_data[qrs_locs[hk] + delay_of_qrs];
      var iso = ecg_data[qrs_locs[hk] + delay_of_iso];
      if (iso < -500) { iso = 0; };
      // console.log(iso);
      var qrs_amplitude = Math.abs(qrs - iso);
      var qrs_leng = qrs_locs[hk + 1] - qrs_locs[hk];
      // var iso = ecg_data[Math.ceil((qrs_locs[hk] + qrs_locs[hk + 1]) / 2)];
      var segment = [];
      var test_segment = [];
      var start_ind = 0.12;  // SWEET SPOT HERE, NOT TO CHANGE
      var end_ind = 0.6;     // SWEET SPOT HERE, NOT TO CHANGE
      // IMPROVE: REMOVE EFFECT OF PPG
      if (qrs_leng < 15) {          // Higher than 160 bpm
        start_ind = 0.05;
        end_ind = 0.75;
      } else if (qrs_leng < 20) {   // Higher than 120 bpm
        start_ind = 0.06;
        end_ind = 0.65;
      } else if (qrs_leng < 30) {   // Higher than 80 bpm
        start_ind = 0.08;
        end_ind = 0.60;
      } else if (qrs_leng < 40) {   // Higher than 60 bpm
        start_ind = 0.12;
        end_ind = 0.60;
      } else {                      // Lower than 60 bpm
        start_ind = 0.12;
        end_ind = 0.65;
      };
      // console.log("t_start:" + start_ind + "-t_end:" + end_ind);
      var index_to_start  = Math.ceil(start_ind * qrs_leng) + qrs_locs[hk];
      var index_to_end    = Math.floor(end_ind * qrs_leng) + qrs_locs[hk];
      for (var lm = index_to_start; lm < index_to_end; lm++) {
        var value = ecg_data[lm] - iso;
        segment.push(Math.abs(value));
        test_segment.push(ecg_data[lm]);
      };
      // Improve code here
      segment = this.magnify_maximum(segment, 0, 4);
      var t_amplitude_abs, t_loc, t_amplitude;
      var delay_after_magnify = 1;
      var min_peak_value = this.cal_mean(segment) + 0.5 * this.cal_std(segment);
      var min_peak_distance = 4;
      try {
        var possible_t_peaks = this.find_peaks(segment, min_peak_value);
        t_loc = this.find_max(possible_t_peaks);
      } catch(err) {
        t_amplitude_abs = this.find_max(segment);
        t_loc = segment.indexOf(t_amplitude_abs);
      };
      if (t_loc == undefined) {
        t_amplitude_abs = this.find_max(segment);
        t_loc = segment.indexOf(t_amplitude_abs);
      };
      if (t_loc < 2) {
        if (last_t_loc_1 && last_t_loc_2) {
          t_loc = Math.ceil((last_t_loc_1 + last_t_loc_2) / 2);
        }
      }
      // console.log(t_loc);
      if (last_t_loc_1 && last_t_loc_2) {
        var diff_1 = Math.abs(t_loc - last_t_loc_1);
        var diff_2 = Math.abs(t_loc - last_t_loc_2);
        if (diff_1 / t_loc > 0.4 || diff_2 / t_loc > 0.4) {
          t_loc = Math.floor((last_t_loc_1 + last_t_loc_2) / 2);
          last_t_loc_2 = last_t_loc_1;
          last_t_loc_1 = t_loc;
        } else {
          last_t_loc_2 = last_t_loc_1;
          last_t_loc_1 = t_loc;
        }
      } else {
        last_t_loc_1 = t_loc;
        last_t_loc_2 = t_loc;
      };
      // console.log(test_segment[t_loc]);
      t_loc = t_loc + index_to_start + delay_after_magnify;
      // console.log(ecg_data[t_loc] + " " + iso);
      // REMOVE EFFECT OF PPG
      // if (ecg_data[t_loc] < ecg_data[t_loc + 10] && ecg_data[t_loc] < ecg_data[t_loc - 10]) {    // Check if trending is downward, most likely a minima
      if (ecg_data[t_loc] < iso) {
        var peak_locs = this.find_peaks(test_segment);
        // console.log(peak_locs);
        var maximum_peak = iso;      // New candiadate must be a maxima and higher than iso
        var maximum_peak_ind = null;
        for (var ind = 0; ind < peak_locs.length; ind++) {
          if (test_segment[peak_locs[ind]] > maximum_peak) {
            maximum_peak = test_segment[peak_locs[ind]];
            maximum_peak_ind = peak_locs[ind];
          };
        };
        // console.log(maximum_peak_ind);
        if (maximum_peak_ind) {
          var value = Math.abs(maximum_peak_ind / last_maximum_peak_ind);
          if (value < 0.25 || value > 4) {
            maximum_peak_ind = last_maximum_peak_ind;
          } else {
            last_maximum_peak_ind = maximum_peak_ind;
          }
        } else {
          maximum_peak_ind = last_maximum_peak_ind;
        }
      };
      // IMPORTANT, NOT TO CHANGE
      var global_ind = maximum_peak_ind + index_to_start;
      // console.log(global_ind);
      // console.log(ecg_data[t_loc] + " " + iso + " " + ecg_data[global_ind]);
      var mag_1 = ecg_data[t_loc] - iso;
      var mag_2 = ecg_data[global_ind] - iso;
      var value_1 =  Math.abs(mag_2 / mag_1);
      if (value_1 > 0.56) {
        t_loc = global_ind;
      };
      // END OF REMOVE EFFECT OF PPG
      // REVERSE ENGINEERING FOR T_AMPLITUDE
      t_amplitude = ecg_data[t_loc] - iso;
      // console.log(ecg_data[t_loc] + "-" + iso);
      if (!t_amplitude) {
        if (last_t_val_1 && last_t_val_2) {
          t_amplitude = Math.ceil((last_t_val_1 + last_t_val_2) / 2);
        };
      } else {
        if (last_t_val_1 && last_t_val_2) {
          var diff_1 = Math.abs(t_amplitude - last_t_val_1);
          var diff_2 = Math.abs(t_amplitude - last_t_val_2);
          var tich_1 = t_amplitude * last_t_val_1;
          var tich_2 = t_amplitude * last_t_val_2;
          if ((diff_1 / last_t_val_1 > 2.5 || diff_2 / last_t_val_2 > 2.5) || ((tich_1 < 0 || tich_2 < 0) && Math.abs(last_t_val_1) > 20)) {
            t_amplitude = Math.floor((last_t_val_1 * 25 + last_t_val_2 * 25 + t_amplitude * 2) / 52);
            last_t_val_2 = last_t_val_1;
            last_t_val_1 = t_amplitude;
          } else {
            last_t_val_2 = last_t_val_1;
            last_t_val_1 = t_amplitude;
          }
        } else {
          last_t_val_1 = t_amplitude;
          last_t_val_2 = t_amplitude;
        };
      };
      // END OF REVERSE ENGINEERING
      t_peaks.push(Math.floor(t_amplitude / qrs_amplitude * 100));
      t_locs.push(t_loc);
    };
    return [t_peaks, t_locs];
  };
  var last_std_val_1, last_std_val_2;
  this.std_detect = function(fs, ecg_data, qrs_locs, t_locs, baseline, power) {
    if (baseline == null) {
      baseline = 250;
    };
    if (power == null) {
      power = 5;
    };
    if (qrs_locs == null) {
      qrs_locs = this.qrs_detect(fs, ecg_data, baseline, power);
    };
    if (t_locs == null) {
      var ohyeah = this.t_peaks_detect(fs, ecg_data, qrs_locs, baseline, power);
      t_locs = ohyeah[1];
    };
    var std_bin = [];
    for (var hk = 0; hk < t_locs.length; hk++) {
      var std = 0;
      var delay_of_qrs = 1;
      var delay_of_iso = -2;
      var qrs = ecg_data[qrs_locs[hk] + delay_of_qrs];
      var iso = ecg_data[qrs_locs[hk] + delay_of_iso];
      if (iso < -500) { iso = 0; };
      // var iso = ecg_data[(Math.ceil(qrs_locs[hk] + qrs_locs[hk + 1]) / 2)];
      var qrs_amplitude = Math.abs(qrs - iso);
      var rt_length = t_locs[hk] - qrs_locs[hk];
      var st_index_start = Math.ceil(rt_length / 3) + qrs_locs[hk] + delay_of_qrs;
      var st_index_end = Math.ceil(rt_length / 3 * 2.1) + qrs_locs[hk] + delay_of_qrs;
      // var st_index_start = Math.ceil(rt_length / 10 * 2) + qrs_locs[hk] + delay_of_qrs;
      // var st_index_end = Math.ceil(rt_length / 10 * 6) + qrs_locs[hk] + delay_of_qrs;
      for (var lm = st_index_start; lm < st_index_end; lm++) {
        std += (ecg_data[lm] - iso);
      };
      std = std / (st_index_end - st_index_start);
      std = Math.floor(std / qrs_amplitude * 100);
      if (!std) {
        if (last_std_val_1 && last_std_val_2) {
          std = Math.ceil((last_std_val_1 + last_std_val_2) / 2);
        };
      };
      // console.log(std);
      if (last_std_val_1 && last_std_val_2) {
        var diff_1 = Math.abs(std - last_std_val_1);
        var diff_2 = Math.abs(std - last_std_val_1);
        var tich_1 = std * last_std_val_1;
        var tich_2 = std * last_std_val_2;
        // console.log(tich_1);
        if (((diff_1 / last_std_val_1 > 2.5 || diff_2 / last_std_val_2 > 2.5) || ((tich_1 < 0 && tich_2 < 0) && (Math.abs(diff_1 / std) > 3 || Math.abs(diff_2 / std) > 3)))) {
          std = Math.floor((last_std_val_1 + last_std_val_2) / 2);
          last_std_val_2 = last_std_val_1;
          last_std_val_1 = std;
        } else {
          last_std_val_2 = last_std_val_1;
          last_std_val_1 = std;
        }
      } else {
        last_std_val_1 = std;
        last_std_val_2 = std;
      };
      // console.log(std);
      std_bin.push(std);
    };
    return std_bin;
  };
  this.calculate_heart_rates = function(fs, ecg_data, qrs_locs, baseline, power) {
    var hrs = [];
    if (baseline == null) {
      baseline = 250;
    };
    if (power == null) {
      power = 8;
    };
    if (ecg_data != null && qrs_locs == null) {
      qrs_locs = this.qrs_detect(fs, ecg_data, baseline, power);
    };
    if (qrs_locs.length <= 1) {
      return "---";
    };
    for (i = 0; i < qrs_locs.length - 1; i++) {
      var hr = Math.floor(60 / ((qrs_locs[i + 1] - qrs_locs[i])  / fs));
      hrs.push(hr);
    };
    return hrs;
  };
  this.smooth_signal_with_moving_avarage = function(span, data) {
    var data_leng = data.length;
    if (span == null) {
      span = Math.ceil(data_leng / 2);
    };
    if (span > data_leng) {
      span = data_leng;
    };
    var output = [];
    for (var loop = 0; loop < data_leng; loop++) {
      var cumsum = 0;
      for (var ind = 0; ind < span; ind++) {
        if ((loop + ind) < data_leng) {
          cumsum += data[loop + ind];
        } else {
          cumsum += data[loop + ind - span];
        }
      };
      output.push(Math.floor(cumsum / span));
    };
    return output;
  };
  this.filter = function(b, a, data) {
      var dataafterfilter = [];
      var sx = data.length;
      dataafterfilter[0] = b[0] * data[0];
      for (var i = 1; i < sx; i++) {
          dataafterfilter[i] = 0;
          for (var j = 0; j <= i; j++) {
              var k = i - j;
              if (j > 0) {
                  if ((k < b.length) && (j < data.length)) {
                      dataafterfilter[i] += (b[k] * data[j]);
                  }
                  if ((k < dataafterfilter.length) && (j < a.length)) {
                      dataafterfilter[i] -= (a[j] * dataafterfilter[k]);
                  }
              } else {
                  if ((k < b.length) && (j < data.length)) {
                      dataafterfilter[i] += (b[k] * data[j]);
                  }
              }
          }
      }
      return dataafterfilter;
  };
  this.convolution = function(a, b) {
        var dataafterconv = [];
        var na = a.length;
        var nb = b.length;
        if (na > nb) {
            if (nb > 1) {
                var output_leng = na + nb - 1;
                for (var i = 0; i < output_leng; i++) {
                    if (i < a.length) {
                        dataafterconv[i] = a[i];
                    } else {
                        dataafterconv[i] = 0;
                    }
                }
                a = dataafterconv;
            }
            dataafterconv = this.filter(b, [1], a);
        } else {
            if (na > 1) {
                var output_leng = na + nb - 1;
                for (var i = 0; i < output_leng; i++) {
                    if (i < b.length) {
                        dataafterconv[i] = b[i];
                    } else {
                        dataafterconv[i] = 0;
                    }
                }
                b = dataafterconv;
            }
            dataafterconv = this.filter(a, [1], b);
        }
        return dataafterconv;
  };
  this.baseline_remove_using_moving_average = function(data) {
    var baseline_wander = [];
    var output = [];
    var span = 40;
    for (var ecg_ind = 0; ecg_ind < data.length; ecg_ind += span) {
      var segment = [];
      for (var loop = 0; loop < span; loop++) {
        if ((ecg_ind + loop) < data.length) {
          segment.push(data[ecg_ind + loop]);
        } else {
          break;
        };
      };
      var output = this.smooth_signal_with_moving_avarage(span, segment);
      baseline_wander = baseline_wander.concat(output);
    };
    baseline_wander = this.smooth_signal_with_moving_avarage(20, baseline_wander);
    baseline_wander = this.smooth_signal_with_moving_avarage(20, baseline_wander);
    for (var loop = 0; loop < data.length; loop++) {
      output[loop] = data[loop] - baseline_wander[loop];
    };
    return output;
  };
  this.baseline_remove_using_convolution = function(data) {
    var arr = [-0.000159964932011480,	-0.000619884504940720,	-0.00153974351839410,	0.997840352109070,	-0.00153974351839410,	-0.000619884504940720,	-0.000159964932011480];
    var dataafterfilter = this.convolution(arr, data);
    return dataafterfilter;
  };
  this.baseline_remove_using_highpass_filter = function(data) {
    var a = [1,	-4.98729199315188,	9.94924868189903,	-9.92399377016952,4.94940946801650,	-0.987372386593206];
    var b = [0.993666134369692,	-4.96833067184846,	9.93666134369692,	-9.93666134369692,	4.96833067184846,	-0.993666134369692];
    var dataafterfilter = this.filter(b, a, data);
    return dataafterfilter;
  };
  this.noise_removal_using_low_pass_filter = function(data) {
    var a = [1,	-0.985325239279239,	0.973849331836765,	-0.386356558648449,	0.111163840578342,	-0.0112635124565659];
    var b = [0.0219396206884642,	0.109698103442321,	0.219396206884642,	0.219396206884642,	0.109698103442321,	0.0219396206884642];
    var dataafterfilter = this.filter(b, a, data);
    return dataafterfilter;
  };
});

app.filter("sanitize", ['$sce', function ($sce) {
    return function (htmlCode) {
        return $sce.trustAsHtml(htmlCode);
    };
}]);
