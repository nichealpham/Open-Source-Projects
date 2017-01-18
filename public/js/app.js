var app = angular.module("app", ['ngRoute', 'ngSanitize']);

app.config(function ($routeProvider) {
  $routeProvider
    .when("/", {
      templateUrl: "templates/homeTemplate.html",
      controller: "mainController"
    })
    .when("/print", {
      templateUrl: "templates/printTemplate.html",
      controller: "printController"
    })
    .otherwise(
      { redirectTo: "/"}
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
