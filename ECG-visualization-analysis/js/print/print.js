var app = angular.module("app", ['ngRoute', 'ngSanitize']);

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