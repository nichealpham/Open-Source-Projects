var app = angular.module("app", []);

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
    var max_hr_hz = 3;          // around 180 bpm
    if (baseline == null) {
      baseline = 250;
    };
    if (power == null) {
      power = 5;
    };
    data2 = this.magnify_maximum(data, baseline, power);
    var min_peak_value = this.cal_mean(data2) + 1.5 * this.cal_std(data2);
    var min_peak_distance = Math.floor(1 / max_hr_hz * fs);
    try {
      var qrs_locs = this.find_peaks(data2, min_peak_value, min_peak_distance);
    } catch(err) {
      return null;
    };
    return qrs_locs;
  };

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
    for (var hk = 0; hk < qrs_locs.length - 1; hk++) {

      var delay_of_qrs = 1;
      var delay_of_iso = -3;
      var qrs = ecg_data[qrs_locs[hk] + delay_of_qrs];
      var iso = ecg_data[qrs_locs[hk] + delay_of_iso];
      var qrs_amplitude = Math.abs(qrs - iso);

      var qrs_leng = qrs_locs[hk + 1] - qrs_locs[hk];
      // var iso = ecg_data[Math.ceil((qrs_locs[hk] + qrs_locs[hk + 1]) / 2 )];

      var segment = [];
      var index_to_start  = Math.floor(0.1 * qrs_leng) + qrs_locs[hk];
      var index_to_end    = Math.floor(0.5 * qrs_leng) + qrs_locs[hk];
      for (var lm = index_to_start; lm < index_to_end; lm++) {
        var value = ecg_data[lm] - iso;
        segment.push(Math.abs(value));
      };
      // Improve code here
      segment = this.magnify_maximum(segment, 0, 4);
      var delay_after_magnify = 1;
      // End of improve code
      var t_amplitude_abs = this.find_max(segment);
      var t_loc = segment.indexOf(t_amplitude_abs) + index_to_start + delay_after_magnify;
      var t_amplitude = ecg_data[t_loc] - iso;
      t_peaks.push(Math.floor(t_amplitude / qrs_amplitude * 100));
      t_locs.push(t_loc);
    };
    return [t_peaks, t_locs];
  };
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
      var delay_of_iso = -3;
      var qrs = ecg_data[qrs_locs[hk] + delay_of_qrs];
      var iso = ecg_data[qrs_locs[hk] + delay_of_iso];
      var qrs_amplitude = Math.abs(qrs - iso);

      var rt_length = t_locs[hk] - qrs_locs[hk];
      var st_index_start = Math.ceil(rt_length / 3) + qrs_locs[hk] + delay_of_qrs;
      var st_index_end = Math.ceil(rt_length / 3 * 2) + qrs_locs[hk] + delay_of_qrs;

      for (var lm = st_index_start; lm < st_index_end; lm++) {
        std += (ecg_data[lm] - iso);
      };
      std = std / (st_index_end - st_index_start);
      std = Math.floor(std / qrs_amplitude * 100);
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
      power = 4;
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

  this.moving_average_2 = function(span, data) {
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
});
