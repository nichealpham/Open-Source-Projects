var app = angular.module("app", ['ngRoute', 'ngSanitize', 'ngFileSaver']);

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

app.directive('onReadFile', function ($parse) {
	return {
		restrict: 'A',
		scope: false,
		link: function(scope, element, attrs) {
            var fn = $parse(attrs.onReadFile);

			element.on('change', function(onChangeEvent) {
				var reader = new FileReader();
        var progressNode = document.getElementById("uploadProgressBar");
        console.log(progressNode);
        progressNode.style.display = "inline-block";
        progressNode.max = 1;
        progressNode.value = 0.2;
				reader.onload = function(onLoadEvent) {
					scope.$apply(function() {
						fn(scope, {$fileContent:onLoadEvent.target.result});
					});
				};

        reader.onprogress = function(event) {
          if (event.lengthComputable) {
              progressNode.value = 1;
          };
        };

				reader.readAsText((onChangeEvent.srcElement || onChangeEvent.target).files[0]);
			});
		}
	};
});
