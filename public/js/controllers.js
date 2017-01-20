var app = angular.module("app")

.controller("mainController", ["$scope", "$http", "$rootScope", "$window", "printService", function ($scope, $http, $rootScope, $window, printService) {
    //var jq = $.noConflict();
    $scope.stepNum = 1;
    $scope.shouldInsertThisItemToProject = false;
    $scope.shouldInsertThisItemToSelectedProject = false;
    $scope.selectedGroupIndex = -1;
    $scope.actionToInsertNewItemAboveThisItemAndThisItemHasNoGroup = false;
    $scope.actionToInsertNewItemAboveThisItemAndThisItemBelongToAGroup = false;
    $scope.indexOfItemToInsertAboveAndHasNoGroup = -1;
    $scope.indexOfItemToInsertAboveAndBelongToAGroup = -1;
    $scope.nameOfTheGroupWhereNewItemInsertAbove = null;
    jQuery("#btn_displayCreateNewProject").on("click", function () {
        jQuery("#currentSelectedItemsView").css("display", "inline-block");
        jQuery("#savedGroupsView").css("display", "none");
    });
    jQuery("#btn_displayMySavedGroups").on("click", function () {
        jQuery("#currentSelectedItemsView").css("display", "none");
        jQuery("#savedGroupsView").css("display", "inline-block");
    });
    $scope.goToCheckOutPage = function () {
      jQuery("#previewProjectView").css("display", "none");
      jQuery("#checkOutView").css("display", "inline-block");
    };
    jQuery("#goBackToPreviewProjectPage").on("click", function () {
        jQuery("#previewProjectView").css("display", "inline-block");
        jQuery("#checkOutView").css("display", "none");
    });
    jQuery("#btn_indertItemToProject").on("click", function () {
      jQuery("#nenDen").css("display", "inline-block");
      jQuery(".left_col").hide();
      jQuery("#popup").css("display", "inline-block");
      $scope.shouldInsertThisItemToProject = true;
    });
    jQuery("#btn_indertItemToSelectedProject").on("click", function () {
      jQuery("#nenDen").css("display", "inline-block");
      jQuery(".left_col").hide();
      jQuery("#popup2").hide();
      jQuery("#popup").css("display", "inline-block");
      $scope.shouldInsertThisItemToSelectedProject = true;
    });
    jQuery(".btnComment").on("click", function () {
      jQuery(".formComment").hide();
      jq(this).next().toggle();
    });
    jQuery("#nenDen").on("click", function () {
      if ( $scope.currentlyInSelectedProjectView == false ) {
        jQuery("#nenDen").css("display", "none");
        jQuery("#popup").css("display", "none");
        jQuery("#popup2").css("display", "none");
        jQuery(".left_col").show();
      } else {
        jQuery("#popup").hide();
        jQuery("#popup2").show();
      };

    });
    $scope.currentlyInSelectedProjectView = false;
    $scope.selectedProject = {};
    $scope.exportReport = function (reportObj) {
            // If JSONData is not an object then JSON.parse will parse the JSON string in an Object
            var JSONData = reportObj.jsonString;
            var ReportTitle = reportObj._id;
            var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
            var dataObj = arrData.data;   // dataObj is the object that represents the report itself
            console.log(dataObj);
            //
            var CSV = '';
            //Set Report title in first row or line
            // Static Info
            CSV += '_id:,' + dataObj._id + '\r\n';
            CSV += 'Day created:,' + '"' + dataObj.Lastmodified + '"' + '\r\n';
            CSV += 'Status:,'  + dataObj.Status + '\r\n';
            CSV += 'Comment:,' + dataObj.Comment + '\r\n';
            CSV += '\r\n\n';
            //Create table headers

            var row = '_id,Name,Category,Unit,Origin,Spec,Price,Quantity,Labor,Money';
            CSV += row + '\r\n';


            //1st loop is to extract each item in the items array
            for (var i = 0; i <= dataObj.items.length - 1; i++) {
                var row = '';
                var thisItemCost = parseInt(dataObj.items[i].quantity) * parseInt(dataObj.items[i].UnitMaterial) + parseInt(dataObj.items[i].UnitLabor);
                // insert the value into the row string, comma-seperated
                row += ( dataObj.items[i]._id + ',"' + dataObj.items[i].Description + '",' + dataObj.items[i].Category + ',' + dataObj.items[i].Unit + ',' + dataObj.items[i].Origin + ',' + dataObj.items[i].Spec + ',' + dataObj.items[i].UnitMaterial + ',' + dataObj.items[i].quantity );
                row += ( ',' + dataObj.items[i].UnitLabor + ',' + thisItemCost );

                row.slice(0, row.length - 1);

                //add a line break after each row
                CSV += row + '\r\n';
            };
            //2nd loop to insert all groups items to CSV
            for (var j = 0; j <= dataObj.groups.length - 1; j++) {
              var name = dataObj.groups[j].name + '\r\n';
              CSV += name;
              for (var i = 0; i <= dataObj.groups[j].items.length - 1; i++) {
                  var row = '';
                  var thisItemCost = parseInt(dataObj.groups[j].items[i].quantity) * parseInt(dataObj.groups[j].items[i].UnitMaterial) + parseInt(dataObj.groups[j].items[i].UnitLabor);
                  // insert the value into the row string, comma-seperated
                  row += ( dataObj.groups[j].items[i]._id + ',"' + dataObj.groups[j].items[i].Description + '",' + dataObj.groups[j].items[i].Category + ',' + dataObj.groups[j].items[i].Unit + ',' + dataObj.groups[j].items[i].Origin + ',' + dataObj.groups[j].items[i].Spec + ',' + dataObj.groups[j].items[i].UnitMaterial + ',' + dataObj.groups[j].items[i].quantity );
                  row += ( ',' + dataObj.groups[j].items[i].UnitLabor + ',' + thisItemCost );

                  row.slice(0, row.length - 1);

                  //add a line break after each row
                  CSV += row + '\r\n';
              };
            };
            console.log(CSV);
            if (CSV == '') {
                alert("Invalid data");
                return;
            }
            // for creating file in web app

            //Generate a file name
            var fileName = "MyReport_";
            //this will remove the blank-spaces from the title and replace it with an underscore
            fileName += ReportTitle.replace(/ /g,"_");

            //Initialize file format you want csv or xls
            var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);

            // Now the little tricky part.
            // you can use either>> window.open(uri);
            // but this will not work in some browsers
            // or you will not get the correct file extension

            //this trick will generate a temp <a /> tag
            var link = document.createElement("a");
            link.href = uri;

            //set the visibility hidden so it will not effect on your web-layout
            link.style = "visibility:hidden";
            link.download = fileName + ".csv";

            //this part will append the anchor tag and remove it after automatic click
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

    $scope.showHomePannel = function () {
      jQuery(".home_pannel").hide();
      jQuery("#homePannel").show();
    };
    $scope.showNewProjectPannel = function () {
      jQuery(".home_pannel").hide();
      jQuery("#newProjectPannel").show();
    };
    $scope.showDatabasePannel = function () {
      jQuery(".home_pannel").hide();
      jQuery("#databasePannel").show();
    };
    var getToDayFormatted = function () {
      var today = new Date();
      var dd = today.getDate();
      var mm = today.getMonth()+1; //January is 0!
      var yyyy = today.getFullYear();

      if(dd<10) {
        dd='0'+dd
      }

      if(mm<10) {
        mm='0'+mm
      }

      today = yyyy + "-" + mm + "-" + dd;
      return today;
    };
    var getToDayFormattedForReportID = function () {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();
        yyyy = yyyy.toString().substr(2,2);
        if (dd < 10) {
            dd = '0' + dd
        }

        if (mm < 10) {
            mm = '0' + mm
        }

        today = yyyy + "" + mm + "" + dd;
        return today;
    };
    $scope.getMonthFormated = function (todateFormated) {
      var monBin = ["Jan", "Feb", "March", "April", "May", "June", "July", "August", "Sept", "Oct", "Nov", "Dec"];
      var month = parseInt(todateFormated.slice(5,7));
      var monthString = monBin[month - 1];
      return monthString;
    };
    $scope.getDayFormated = function (todateFormated) {
      var day = todateFormated.slice(8);
      return day;
    };

    $http.get("http://112.78.3.114:4220/bomService.asmx/getAllOngoingReport").then(function (response) {
        $scope.ongoingReports = response.data;
        for (i = 0; i <= response.data.length - 1; i++) {
          $scope.ongoingReports[i].showInput = false;
        };
        if ($scope.ongoingReports.length > 0) {
          $scope.selectedProject = $scope.ongoingReports[0];
          $scope.selectedProject.groups = [];
          $scope.selectedProject.items = [];
        };
    });
    $http.get("http://112.78.3.114:4220/bomService.asmx/getAllPassReport").then(function (response) {
        $scope.passReports = response.data;
        for (i = 0; i <= response.data.length - 1; i++) {
          $scope.passReports[i].showInput = false;
        };
        if ($scope.passReports.length > 0) {
          $scope.selectedProject = $scope.passReports[0];
          $scope.selectedProject.groups = [];
          $scope.selectedProject.items = [];
        };
    });
    $scope.changeThisReportComment = function (reportObj) {
      reportObj.showInput = false;
      $http.post('http://112.78.3.114:4220/bomService.asmx/updateReportComment', { "_id": reportObj._id, "Comment": reportObj.Comment }).then(function () {
        console.log("Comment changed successfully");
      });
    };
    $http.get("http://112.78.3.114:4220/bomService.asmx/getAllFaliedlReport").then(function (response) {
        $scope.failedReports = response.data;
        for (i = 0; i <= response.data.length - 1; i++) {
          $scope.failedReports[i].showInput = false;
        };
        if ($scope.failedReports.length > 0) {
          $scope.selectedProject = $scope.failedReports[0];
          $scope.selectedProject.groups = [];
          $scope.selectedProject.items = [];
        };
    });
    $scope.showReportInput = function (reportObj) {
      reportObj.showInput = true;
    };
    var getLastItemID = function () {

      if ($scope.items.length > 0) {
        var lastID = parseInt($scope.items[$scope.items.length - 1]._id);
        return lastID;
      }
      else {
        lastID = 0;
        return lastID;
      };

    };
    $http.get("http://112.78.3.114:4220/bomService.asmx/getAllItem").then(function (response) {
      if (response.data.length > 0) {
        $scope.items = response.data;               // use for inserting new item to group
        for (i = 0; i <= $scope.items.length - 1; i++) {
          $scope.items[i].saved = true;
        };
      }
      else {
        $scope.items = [];
      };
    });
    $scope.deleteThisOngoingReport = function (id, index) {
      var cf = confirm("Do you want to delete this report?");
      if (cf == true) {
        console.log("Delete action fired!");
        $http.post("http://112.78.3.114:4220/bomService.asmx/deleteReportByID", { "_id": id }).then(function () {
            $scope.ongoingReports.splice(index, 1);
        });
      };

    };
    $scope.deleteThisPassReport = function (id, index) {

        var cf = confirm("Do you want to delete this report?");
        if (cf == true) {
          console.log("Delete action fired!");
          $http.post("http://112.78.3.114:4220/bomService.asmx/deleteReportByID", { "_id": id }).then(function () {
              $scope.passReports.splice(index, 1);
          });
        };
    };
    $scope.deleteThisFailedReport = function (id, index) {

        var cf = confirm("Do you want to delete this report?");
        if (cf == true) {
          console.log("Delete action fired!");
          $http.post("http://112.78.3.114:4220/bomService.asmx/deleteReportByID", { "_id": id }).then(function () {
              $scope.failedReports.splice(index, 1);
          });
        };
    };
    $scope.admin = {
        name: "Admin",
        password: "123",
        fullname: ""
    };

    $scope.tb_projectName = getToDayFormattedForReportID();
    $scope.reportUrl = "report/" + $scope.tb_projectName + ".json";

    $scope.viewThisReport = function (reportObj) {
      $scope.currentlyInSelectedProjectView = true;
      var dataToFetch = JSON.parse(reportObj.jsonString);
      $scope.selectedProject = dataToFetch.data;
      jQuery("#nenDen").css("display", "inline-block");
      jQuery(".left_col").hide();
      jQuery("#popup2").css("display", "inline-block");
    };
    $scope.currentCategory = {
        name: "",
        items: []
    };
    $scope.savedGroups = [];
    $scope.groupsInView = [];

    // functions

    $scope.createNewProject = function () {
        console.log("button clicked");
        $scope.newProject.name = $scope.tb_projectName;
        $scope.newProject._id = $scope.tb_projectName;
        $scope.newProject.reportUrl = "report/" + $scope.tb_projectName + ".json",
        $scope.newProject.company = $scope.tb_company;
        $scope.newProject.Status = jQuery("#select_Category option:selected").text();
        jQuery("#projectInfoView").css("display", "none");
        jQuery("#previewProjectView").css("display", "inline-block");
        $scope.stepNum = 2;
        console.log($scope.newProject);
    };
    $scope.newProject = {
        name: getToDayFormattedForReportID(),
        _id: getToDayFormattedForReportID(),
        showInput: false,
        company: "Default Company",
        category: "Engineer",
        reportUrl: "report/" + $scope.tb_projectName + ".json",
        Status: "ongoing",
        Createdby: $scope.admin.name,
        Comment: "",
        Lastmodified: getToDayFormatted(),
        jsonString: "",
        items: [],
        groups: []
    };
    $scope.saveThisProjectToServer = function () {
        var dataString = {
            data: $scope.newProject
        };
        $scope.newProject.jsonString = JSON.stringify(dataString);
        console.log($scope.newProject.jsonString);
        $scope.newProject.Comment = "none";
        console.log($scope.newProject._id);
        $http.post("http://112.78.3.114:4220/bomService.asmx/addNewReport", { "_id": $scope.newProject._id, "reportUrl": $scope.newProject.reportUrl, "Status": $scope.newProject.Status, "Comment": $scope.newProject.Comment, "Createdby": $scope.newProject.Createdby, "Lastmodified": $scope.newProject.Lastmodified, "jsonString": $scope.newProject.jsonString }).then(function () {
            if ($scope.newProject.Status == "ongoing") {
                $scope.ongoingReports.push($scope.newProject);
            };
            if ($scope.newProject.Status == "pass") {
                $scope.passReports.push($scope.newProject);
            };
            if ($scope.newProject.Status == "failed") {
                $scope.failedReports.push($scope.newProject);
            };
            $scope.newProject = {
                name: getToDayFormattedForReportID(),
                _id: getToDayFormattedForReportID(),
                showInput: false,
                company: "Default Company",
                category: "Engineer",
                reportUrl: "report/" + $scope.tb_projectName + ".json",
                Status: "ongoing",
                Createdby: $scope.admin.name,
                Comment: "",
                Lastmodified: getToDayFormatted(),
                jsonString: "none",
                items: [],
                groups: []
            };
            console.log("Report saved successfully");
            window.location = "home.html";
        });
    };
    $scope.removeThisGroupFromNewProject = function (index) {
      var cf = confirm("Delete this Group?");
      if (cf == true) {
        $scope.newProject.groups.splice(index, 1);
      };

    };
    $scope.removeThisGroupFromSelectedProject = function (index) {

        var cf = confirm("Delete this Group?");
        if (cf == true) {
          $scope.selectedProject.groups.splice(index, 1);
        };
    };
    $scope.removeThisGroupFromSelectedProject = function (index) {

      var cf = confirm("Delete this Group?");
      if (cf == true) {
        $scope.selectedProject.groups.splice(index, 1);
      };
    };
    $scope.removeThisItemFromNewProject = function (groupName, index, item) {

        var cf = confirm("Delete this Item?");
        if (cf == true) {
          for (i = 0; i <= $scope.newProject.groups.length - 1; i++) {
              if ($scope.newProject.groups[i].name == groupName) {
                  $scope.newProject.groups[i].items.splice(index, 1);
              };
          };
        };
//        $scope.items.splice(item._id, 0, item);
    };
    $scope.removeThisItemFromSelectedProject = function (groupName, index, item) {

      var cf = confirm("Delete this Item?");
      if (cf == true) {
        for (i = 0; i <= $scope.selectedProject.groups.length - 1; i++) {
            if ($scope.selectedProject.groups[i].name == groupName) {
                $scope.selectedProject.groups[i].items.splice(index, 1);
            };
        };
      };
    };
    $scope.removeThisItemFromNewProjectItemsList = function (index) {
      var cf = confirm("Delete this Item?");
      if (cf == true) {
        $scope.newProject.items.splice(index, 1);
      };

    };
    $scope.removeThisItemFromSelectedProjectItemsList = function (index) {

      var cf = confirm("Delete this Item?");
      if (cf == true) {
        $scope.selectedProject.items.splice(index, 1);
      };
    };
    $scope.addItemToCategory = function (item) {
        for (i = 0; i <= $scope.currentCategory.items.length - 1; i++) {
            if ($scope.currentCategory.items[i]._id == item._id) {
                // item has been added
                alert("Item has been added");
                return;

            }
            else {
                continue;
            }
        }
        item.saved = true; // display the save button when update this item
        item.quantity = 1;
        item.totalMoney = parseInt(item.quantity) * parseInt(item.UnitMaterial) + parseInt(item.UnitLabor);
        item.comments = "";
        $scope.currentCategory.items.push(item);
    };
    $scope.updateGroupName = function (index, value) {
        $scope.newProject.groups[index].name = value;
    };
    $scope.removeItemFromCurrentGroup = function (index) {

        var cf = confirm("Delete this Item?");
        if (cf == true) {
          $scope.currentCategory.items.splice(index, 1);
        };
    };
    $scope.saveCurrentCaterogy = function () {
        $scope.currentCategory.name = $scope.tb_currentGroupName;
        $scope.savedGroups.push($scope.currentCategory);
        $scope.newProject.groups.push($scope.currentCategory);
     //   $scope.groupsInView = [];
     //   for (i = 0; i <= $scope.savedGroups.length - 1; i++) {
     //       $scope.groupsInView.push($scope.savedGroups[i]);
     //   };
        // reinitiate blank category
        $scope.currentCategory = {
            name: "",
            items: []
        };
        $scope.tb_currentGroupName = "";

    };
    $scope.saveCurrentCaterogyToSelectedProject = function () {
      var obj = {
        name: $scope.tb_currentGroupNameSelectedProject,
        items: []
      };
      $scope.selectedProject.groups.push(obj);
      $scope.tb_currentGroupNameSelectedProject = "";
    };
    $scope.addNewItemToThisGroup = function (index) {
      $scope.groupIndex = index;
      $scope.shouldInsertThisItemToProject = false;
      jQuery("#nenDen").css("display", "inline-block");
      jQuery(".left_col").hide();
      jQuery("#popup").css("display", "inline-block");
    };
    $scope.addNewItemToThisSelectedGroup = function (index) {
      $scope.selectedGroupIndex = index;
      $scope.shouldInsertThisItemToSelectedProject = false;
      jQuery("#nenDen").css("display", "inline-block");
      jQuery(".left_col").hide();
      jQuery("#popup").show();
      jQuery("#popup2").hide();
    };

    $scope.insertNewItemAboveThisItemAndThisItemHasNoGroup = function (itemIndex) {
      jQuery("#nenDen").css("display", "inline-block");
      jQuery(".left_col").hide();
      jQuery("#popup").show();
      jQuery("#popup2").hide();
      $scope.actionToInsertNewItemAboveThisItemAndThisItemHasNoGroup = true;  // initiated with false
      $scope.indexOfItemToInsertAboveAndHasNoGroup = itemIndex;               // initiated with -1
    };
    $scope.insertNewItemAboveThisItemAndThisItemBelongToAGroup = function (groupName, itemIndex) {
      jQuery("#nenDen").css("display", "inline-block");
      jQuery(".left_col").hide();
      jQuery("#popup").show();
      jQuery("#popup2").hide();
      $scope.actionToInsertNewItemAboveThisItemAndThisItemBelongToAGroup = true;  // initiate with false
      $scope.indexOfItemToInsertAboveAndBelongToAGroup = itemIndex;               // initiate with -1
      $scope.nameOfTheGroupWhereNewItemInsertAbove = groupName;                   // initiate with null
    };
    $scope.addItemToThisGroup = function (itemObj, groupindex, selectedGroupIndex, itemindex) {
      // check first
      // check if this event is fired from currently selected project
      if ( $scope.currentlyInSelectedProjectView == true ) {
        if ( $scope.actionToInsertNewItemAboveThisItemAndThisItemHasNoGroup ) {
          // some actions
          $scope.selectedProject.items.splice($scope.indexOfItemToInsertAboveAndHasNoGroup, 0, JSON.parse(JSON.stringify(itemObj)));
          // then turn it back to normal state
          $scope.actionToInsertNewItemAboveThisItemAndThisItemHasNoGroup = false;
          $scope.indexOfItemToInsertAboveAndHasNoGroup = -1;
        }
        if ( $scope.actionToInsertNewItemAboveThisItemAndThisItemBelongToAGroup ) {
          // some actions
            for (i = 0; i < $scope.selectedProject.groups.length; i ++) {
              if ($scope.selectedProject.groups[i].name == $scope.nameOfTheGroupWhereNewItemInsertAbove) {
                $scope.selectedProject.groups[i].items.splice($scope.indexOfItemToInsertAboveAndBelongToAGroup, 0, JSON.parse(JSON.stringify(itemObj)));
              }
            };
          // then turn it back to normal state
          $scope.actionToInsertNewItemAboveThisItemAndThisItemBelongToAGroup = false;  // initiate with false
          $scope.indexOfItemToInsertAboveAndBelongToAGroup = -1;                       // initiate with -1
          $scope.nameOfTheGroupWhereNewItemInsertAbove = null;
        }
        else {
          if ( $scope.shouldInsertThisItemToSelectedProject ) {   // insert this item to items field
            itemObj.quantity = 1;
            itemObj.saved = true;
            itemObj.comments = "";
            itemObj.totalMoney = parseInt(itemObj.quantity) * parseInt(itemObj.UnitMaterial) + parseInt(itemObj.UnitLabor);
            $scope.selectedProject.items.push(JSON.parse(JSON.stringify(itemObj)));
          }
          else {
            itemObj.quantity = 1;
            itemObj.saved = true;
            itemObj.comments = "";
            itemObj.totalMoney = parseInt(itemObj.quantity) * parseInt(itemObj.UnitMaterial) + parseInt(itemObj.UnitLabor);
            $scope.selectedProject.groups[selectedGroupIndex].items.push(JSON.parse(JSON.stringify(itemObj)));
          };
        };

      } else {                                          // this event is fired from create new project view
        if ( $scope.shouldInsertThisItemToProject ) {   // insert this item to items field
          itemObj.quantity = 1;
          itemObj.saved = true;
          itemObj.comments = "";
          itemObj.totalMoney = parseInt(itemObj.quantity) * parseInt(itemObj.UnitMaterial) + parseInt(itemObj.UnitLabor);
          $scope.newProject.items.push(JSON.parse(JSON.stringify(itemObj)));
        }
        else {
          itemObj.quantity = 1;
          itemObj.saved = true;
          itemObj.comments = "";
          itemObj.totalMoney = parseInt(itemObj.quantity) * parseInt(itemObj.UnitMaterial) + parseInt(itemObj.UnitLabor);
          $scope.newProject.groups[groupindex].items.push(JSON.parse(JSON.stringify(itemObj)));
        };
      }



  //    $scope.items.splice(itemindex, 1);
    };
    $scope.addThisGroupToProject = function (index, group) {
        //add to projects
        $scope.newProject.groups.push(group);
        console.log("Group added to project");
        console.log($scope.newProject.groups.length);
        //update the groups view
        $scope.savedGroups.splice(index, 1);
        jQuery("#btn_goToNextStage").css("display", "inline-block");
    };
    $scope.previewProject = function () {
        jQuery("#addItemView").css("display", "none");
        jQuery("#previewProjectView").css("display", "inline-block");
        $scope.stepNum = 3;
    };
    $scope.goBackToAddItemPage = function () {
        jQuery("#addItemView").css("display", "inline-block");
        jQuery("#previewProjectView").css("display", "none");
        $scope.stepNum = 2;
    };
    $scope.calculateEachItemTotalMoney = function (a, b, c, groupObj, itemObj) {
        var result = parseInt(a) * parseInt(b) + parseInt(c);
        for (i = 0; i <= $scope.newProject.groups.length - 1; i++) {
            if (groupObj.name === $scope.newProject.groups[i].name) {
                for (k = 0; k <= $scope.newProject.groups[i].items.length - 1; k++) {
                    if (itemObj.Description === $scope.newProject.groups[i].items[k].Description) {
                        // update the total Money of each Item to its scope
                        $scope.newProject.groups[i].items[k].totalMoney = result;
                    };
                };
            };
        };
        return result;
    };
    $scope.calculateEachItemTotalMoney2 = function (a, b, c, itemIndex) {
        var result = parseInt(a) * parseInt(b) + parseInt(c);
    //    $scope.newProject.items[itemIndex].totalMoney = result;
        return result;
    };
    $scope.insertQuantity = function (groupObj, itemObj, value) {
        for (i = 0; i <= $scope.newProject.groups.length - 1; i++) {
            if (groupObj.name === $scope.newProject.groups[i].name) {
                for (k = 0; k <= $scope.newProject.groups[i].items.length - 1; k++) {
                    if (itemObj.Description === $scope.newProject.groups[i].items[k].Description) {
                        $scope.newProject.groups[i].items[k].quantity = parseInt(value);
                        $scope.getToTalProjectMoney();
                        // update the total Money of each Item to its scope
                        $scope.newProject.groups[i].items[k].totalMoney = parseInt($scope.newProject.groups[i].items[k].quantity) * parseInt($scope.newProject.groups[i].items[k].UnitMaterial) + parseInt($scope.newProject.groups[i].items[k].UnitLabor);
                    };
                };
            };
        };
    };
    $scope.insertPrice = function (groupObj, itemObj, value) {
        for (i = 0; i <= $scope.newProject.groups.length - 1; i++) {
            if (groupObj.name === $scope.newProject.groups[i].name) {
                for (k = 0; k <= $scope.newProject.groups[i].items.length - 1; k++) {
                    if (itemObj.Description === $scope.newProject.groups[i].items[k].Description) {
                        $scope.newProject.groups[i].items[k].UnitMaterial = parseInt(value);
                        $scope.getToTalProjectMoney();
                        $scope.newProject.groups[i].items[k].totalMoney = parseInt($scope.newProject.groups[i].items[k].quantity) * parseInt($scope.newProject.groups[i].items[k].UnitMaterial) + parseInt($scope.newProject.groups[i].items[k].UnitLabor);
                    };
                };
            };
        };
    };
    $scope.insertLaborCost = function (groupObj, itemObj, value) {
        for (i = 0; i <= $scope.newProject.groups.length - 1; i++) {
            if (groupObj.name === $scope.newProject.groups[i].name) {
                for (k = 0; k <= $scope.newProject.groups[i].items.length - 1; k++) {
                    if (itemObj.Description === $scope.newProject.groups[i].items[k].Description) {
                        $scope.newProject.groups[i].items[k].UnitLabor = parseInt(value);
                        console.log($scope.newProject.groups[i].items[k].UnitLabor);
                        $scope.getToTalProjectMoney();
                        $scope.newProject.groups[i].items[k].totalMoney = parseInt($scope.newProject.groups[i].items[k].quantity) * parseInt($scope.newProject.groups[i].items[k].UnitMaterial) + parseInt($scope.newProject.groups[i].items[k].UnitLabor);
                    };
                };
            };
        };
    };
    $scope.getToTalProjectMoney = function () {
        var totalMoney = 0;
        for (i = 0; i <= $scope.newProject.groups.length - 1; i++) {
            for (k = 0; k <= $scope.newProject.groups[i].items.length - 1; k++) {
                totalMoney += parseInt($scope.newProject.groups[i].items[k].quantity) * parseInt($scope.newProject.groups[i].items[k].UnitMaterial) + parseInt($scope.newProject.groups[i].items[k].UnitLabor);
            };
        };
        for ( j = 0; j <= $scope.newProject.items.length - 1; j ++ ) {
          totalMoney += parseInt($scope.newProject.items[j].quantity) * parseInt($scope.newProject.items[j].UnitMaterial) + parseInt($scope.newProject.items[j].UnitLabor);
        };
        return totalMoney.toString();
    };

    $scope.getToTalSelectedProjectMoney = function () {
        var totalMoney = 0;
        if ($scope.selectedProject.groups != null) {
          for (i = 0; i <= $scope.selectedProject.groups.length - 1; i++) {
              for (k = 0; k <= $scope.selectedProject.groups[i].items.length - 1; k++) {
                  totalMoney += parseInt($scope.selectedProject.groups[i].items[k].quantity) * parseInt($scope.selectedProject.groups[i].items[k].UnitMaterial) + parseInt($scope.selectedProject.groups[i].items[k].UnitLabor);
              };
          };
        };
        if ($scope.selectedProject.items != null) {
          for ( j = 0; j <= $scope.selectedProject.items.length - 1; j ++ ) {
            totalMoney += parseInt($scope.selectedProject.items[j].quantity) * parseInt($scope.selectedProject.items[j].UnitMaterial) + parseInt($scope.selectedProject.items[j].UnitLabor);
          };
          return totalMoney;
        };
    };

//    .controller("updateItemController", ["$scope", "$http", function ($scope, $http) {


//    var jq = $.noConflict();
    jQuery("#btn_createNewItem").on("click", function () {
        jQuery("#createNewItemInfoView").toggle();
    });
    jQuery("#btn_searchItem").on("click", function () {
        jQuery("#tableSearchItem").toggle();
    });
    $scope.sendThisReportToPassedProjectsSentByOngoing = function (reportObj, index) {
      $scope.ongoingReports.splice(index, 1);
      $scope.passReports.push(reportObj);
      $http.post('http://112.78.3.114:4220/bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "pass" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    $scope.sendThisReportToFailedProjectsSentByOngoing = function (reportObj, index) {
      $scope.ongoingReports.splice(index, 1);
      $scope.failedReports.push(reportObj);
      $http.post('http://112.78.3.114:4220/bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "failed" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    ///////
    $scope.sendThisReportToOngoingProjectsSentByPassed = function (reportObj, index) {
      $scope.passReports.splice(index, 1);
      $scope.ongoingReports.push(reportObj);
      $http.post('http://112.78.3.114:4220/bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "ongoing" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    $scope.sendThisReportToFailedProjectsSentByPassed = function (reportObj, index) {
      $scope.passReports.splice(index, 1);
      $scope.failedReports.push(reportObj);
      $http.post('http://112.78.3.114:4220/bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "failed" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    ///////
    $scope.sendThisReportToOngoingProjectsSentByFailed = function (reportObj, index) {
      $scope.failedReports.splice(index, 1);
      $scope.ongoingReports.push(reportObj);
      $http.post('http://112.78.3.114:4220/bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "ongoing" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    $scope.sendThisReportToPassedProjectsSentByFailed = function (reportObj, index) {
      $scope.failedReports.splice(index, 1);
      $scope.passReports.push(reportObj);
      $http.post('http://112.78.3.114:4220/bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "pass" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    ////////
    $scope.saveAndCloseThisReport = function () {
      $scope.currentlyInSelectedProjectView = false;
      var objToStringnify = {
        data: $scope.selectedProject
      };
      $http.post('http://112.78.3.114:4220/bomService.asmx/updateReportJsonString', { "_id": $scope.selectedProject._id, "jsonString": JSON.stringify(objToStringnify) }).then(function (response) {
        console.log("Report saved");
        // now fake the client software
        // loop through every reports
        // and change it!
        for (i = 0; i <= $scope.ongoingReports.length - 1; i++) {
          if ( $scope.selectedProject._id == $scope.ongoingReports[i]._id ) {
            $scope.ongoingReports[i].jsonString = JSON.stringify(objToStringnify);
          };
        };
        for (i = 0; i <= $scope.failedReports.length - 1; i++) {
          if ( $scope.selectedProject._id == $scope.failedReports[i]._id ) {
            $scope.failedReports[i].jsonString = JSON.stringify(objToStringnify);
          };
        };
        for (i = 0; i <= $scope.passReports.length - 1; i++) {
          if ( $scope.selectedProject._id == $scope.passReports[i]._id ) {
            $scope.passReports[i].jsonString = JSON.stringify(objToStringnify);
          };
        };
        jQuery("#nenDen").hide();
        jQuery("#popup").hide();
        jQuery(".left_col").show();
        jQuery("#popup2").hide();
      });
    };
    $scope.CloseThisReport = function () {
      var conf = confirm("Close without saving?");
      if (conf) {
        $scope.currentlyInSelectedProjectView = false;
        jQuery("#nenDen").hide();
        jQuery("#popup").hide();
        jQuery(".left_col").show();
        jQuery("#popup2").hide();
      };

    };
    $scope.saveNewThisReport = function() {
      var modal = document.getElementById('myModal');
      modal.style.display = "inline-block";
      // Get the <span> element that closes the modal
      var span = document.getElementsByClassName("close")[0];

      span.onclick = function() {
          modal.style.display = "none";
      };

// When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
          if (event.target == modal) {
              modal.style.display = "none";
          };
      };
    };
    $scope.tb_newReportName = $scope.selectedProject._id + "_2";
    $scope.saveThisReportAsNewReport = function () {
      $scope.newProject = $scope.selectedProject;
      $scope.newProject._id = $scope.tb_newReportName;
      var dataString = {
          data: $scope.newProject
      };
      $scope.newProject.jsonString = JSON.stringify(dataString);
      console.log($scope.newProject.jsonString);
      $scope.newProject.Comment = "none";
      console.log($scope.newProject._id);
      $http.post("http://112.78.3.114:4220/bomService.asmx/addNewReport", { "_id": $scope.newProject._id, "reportUrl": $scope.newProject.reportUrl, "Status": $scope.newProject.Status, "Comment": $scope.newProject.Comment, "Createdby": $scope.newProject.Createdby, "Lastmodified": $scope.newProject.Lastmodified, "jsonString": $scope.newProject.jsonString }).then(function () {
          if ($scope.newProject.Status == "ongoing") {
              $scope.ongoingReports.push($scope.newProject);
          };
          if ($scope.newProject.Status == "pass") {
              $scope.passReports.push($scope.newProject);
          };
          if ($scope.newProject.Status == "failed") {
              $scope.failedReports.push($scope.newProject);
          };
          $scope.newProject = {
              name: getToDayFormattedForReportID(),
              _id: getToDayFormattedForReportID(),
              showInput: false,
              company: "Default Company",
              category: "Engineer",
              reportUrl: "report/" + $scope.tb_projectName + ".json",
              Status: "ongoing",
              Createdby: $scope.admin.name,
              Comment: "",
              Lastmodified: getToDayFormatted(),
              jsonString: "none",
              items: [],
              groups: []
          };
          console.log("Report saved successfully");
      });
      var modal = document.getElementById('myModal');
      modal.style.display = "none";
    };
    $scope.createNewItem = function () {
      var len = $scope.items.length;
      for (i = 0; i < len; i++) {
        if ($scope.items[i].Description == $scope.tb_itemName) {
          alert("This description has been used. Please choose another item description!");
          break;
        } else {
          if (i == (len - 1)) {
            var item = {};
            var id = getLastItemID() + 1;
            console.log(id);
            item._id = id.toString();
            item.Description = $scope.tb_itemName;
            item.Spec = $scope.tb_itemSize;
            item.Origin = $scope.tb_itemOrigin;
            item.Category = $scope.tb_itemClass;
            item.Unit = $scope.tb_itemUnit;
            item.UnitMaterial = $scope.tb_itemPrice;
            item.UnitLabor = $scope.tb_itemLaborCost;
            item.saved = true;
            // date function

            // save it to time modied field
            item.TimeModified = getToDayFormatted();
            $scope.items.push(item);
            $http.post("http://112.78.3.114:4220/bomService.asmx/addNewItem", { "_id": item._id, "Description": item.Description, "Spec": item.Spec, "Origin": item.Origin, "Category": item.Category, "Unit": item.Unit, "UnitLabor": item.UnitLabor, "UnitMaterial": item.UnitMaterial, "TimeModified": item.TimeModified })
            .then(function() {
              console.log("OK! Item added to server");
            });
            $scope.tb_itemName ="";
            $scope.tb_itemSize = "";
            $scope.tb_itemOrigin = "";
            $scope.tb_itemClass = "";
            $scope.tb_itemUnit = "";
            $scope.tb_itemPrice = "";
            $scope.tb_itemLaborCost = "";
          };
        };
      };

    };
    $scope.deleteThisItem = function (index, itemIDString) {
        $scope.items.splice(index, 1);
        $http.post("http://112.78.3.114:4220/bomService.asmx/deleteItemByID", { "_id": itemIDString }).then(function () {
          console.log("Item Deleted");
        });
    };
    // functions
    // when "save button" is clicked
    $scope.updateThisItem = function (item) {
      $http.post("http://112.78.3.114:4220/bomService.asmx/updateItemByID", { "_id": item._id, "Description": item.Description, "Spec": item.Spec, "Origin": item.Origin, "Category": item.Category, "Unit": item.Unit, "UnitLabor": item.UnitLabor, "UnitMaterial": item.UnitMaterial, "TimeModified": item.TimeModified })
      .then(function() {
        console.log("OK! Item added to server");
        item.saved = true;
      });
    };
    $scope.updateItemName = function (item) {
        item.saved = false;
    };
    $scope.updateItemSize = function (item) {
      item.saved = false;
    };
    $scope.updateItemUnit = function (item) {
      item.saved = false;
    };
    $scope.updateItemClass = function (item) {
      item.saved = false;
    };
    $scope.updateItemPrice = function (item) {
      item.saved = false;
    };
    $scope.updateItemLaborCost = function (item) {
      item.saved = false;
    };

    $scope.printPreview = function (report) {
      //printService.set(report.jsonString);
      $window.localStorage['BOM_reportToPrint_string'] = report.jsonString;
      $window.open("#/print", "_blank");
    };








    $scope.showCreateNewDatabasePannel = function() {
      jQuery(".home_pannel").hide();
      jQuery("#createNewDatabasePannel").show();
      jQuery("#createdDatabasePannel").show();
    };
    $scope.databases = [
      {
        name: "BME students",
        dbid: "db#312",
        date: getToDayFormatted(),
        fields: [
          {
            fieldname: "#ID",
            type: "text",
            currentData: "",
            data: ["BEBEIU13051", "BEBEIU13056", "BEBEIU14567", "BEBEIU17623"],
          },
          {
            fieldname: "Name",
            type: "text",
            currentData: "",
            data: ["Pham, Khoi Nguyen", "Nguyen, Minh Quan", "Tran, Trung Huy", "Do, Viet Duy"],
          },
          {
            fieldname: "Phone",
            type: "text",
            currentData: "",
            data: ["0917264532", "0918273645", "0817453622", "0957346453"],
          },
          {
            fieldname: "GPA",
            type: "text",
            currentData: "",
            data: ["3.45", "3.2", "3.67", "3.9"],
          },
        ]
      }
    ];
    $scope.newDatabase = {
      name: "",
      dbid: "db#" + Math.floor((Math.random() * 10000)),
      date: getToDayFormatted(),
      fields: []
    };
    $scope.newField = {
      fieldname: "",
      type: "text",
      currentData: "",
    };
    $scope.insertThisFieldToNewDatabase = function(fieldObj) {
      fieldObj.data = [];
      $scope.newDatabase.fields.push(fieldObj);
      $scope.newField = {
        fieldname: "",
        type: "text",
        currentData: "",
      };
    };
    $scope.createThisDatabase = function() {
      $scope.databases.push($scope.newDatabase);
      $scope.newDatabase = {
        name: "",
        dbid: "db#" + Math.floor((Math.random() * 10000)),
        date: getToDayFormatted(),
        fields: []
      };
    };
    $scope.deleteThisFieldFromNewDatabase = function(index){
      $scope.newDatabase.fields.splice(index, 1);
    };
    $scope.checkLengthDatabases = function() {
      var len = $scope.databases.length;
      if (len >= 1) {
        return true;
      }
      else {
        return false;
      };
    };

    $scope.chooseThisDatabase = function(index) {

    };
    $scope.updateThisDatabase = function(index) {

    };
    $scope.deleteThisDatabase = function(index) {
      $scope.databases.splice(index, 1);
    };
}])
.controller("printController", ["$scope", "$http", "$rootScope", "printService", "$window", function ($scope, $http, $rootScope, printService, $window) {
  $scope.pageTitle = "Print Template";
  $scope.calculateEachItemTotalMoney2 = function (a, b, c, itemIndex) {
      var result = parseInt(a) * parseInt(b) + parseInt(c);
      return result;
  };
//  $scope.projectString = printService.return();
  $scope.projectString = $window.localStorage['BOM_reportToPrint_string'];
  $scope.project = JSON.parse($scope.projectString).data;
  if ($scope.project.additionalInfo == null) {
    $scope.project.additionalInfo = {
      to: {
        value: "Type someone here...",
        show: false
      },
      from: {
        value: "Đặng Thế Nghiêm",
        show: false
      },
      date: {
        value: $scope.project.Lastmodified,
        show: false
      },
      quotation: {
        value: "Type quotation here...",
        show: false
      },
      toTalPages: {
        value: "Type total pages here...",
        show: false
      },
      yourRef: {
        value: "Type shomething here...",
        show: false
      },
      projectTitle: {
        value: "An awesome project title stays here",
        show: false
      },
      scopeOfWork: {
        value: "Type project scope of work here...",
        show: false
      },
      price: {
        value: "Total price: (exclude 10% VAT, Unit VNĐ): ",
        show: false
      },
      paymentTerm: {
        value: "Dow payment 30% of the contact value after PO/contact signed and<br/>70% balance to be discussed on payment schedule per completion progress",
        show: false
      },
      timming: {
        value: "4 months after PO/confirmation",
        show: false
      },
      validity: {
        value: "This quotation is valid within 30 days",
        show: false
      }
    };
  } else {
    if ($scope.project.additionalInfo.date == null) {
      $scope.project.additionalInfo.date = {
        value : $scope.project.Lastmodified,
        show: false
      };
    };
  };
  $scope.updateThisReport = function () {
    var objToStringnify = {
      data: $scope.project
    };
    $window.localStorage['BOM_reportToPrint_string'] = JSON.stringify(objToStringnify);
    $http.post('http://112.78.3.114:4220/bomService.asmx/updateReportJsonString', { "_id": $scope.project._id, "jsonString": JSON.stringify(objToStringnify) }).then(function (response) {
      console.log("Report saved");
    });
  };
  $scope.getToTalProjectMoney = function () {
      var totalMoney = 0;
      for (i = 0; i <= $scope.project.groups.length - 1; i++) {
          for (k = 0; k <= $scope.project.groups[i].items.length - 1; k++) {
              totalMoney += parseInt($scope.project.groups[i].items[k].quantity) * parseInt($scope.project.groups[i].items[k].UnitMaterial) + parseInt($scope.project.groups[i].items[k].UnitLabor);
          };
      };
      for ( j = 0; j <= $scope.project.items.length - 1; j ++ ) {
        totalMoney += parseInt($scope.project.items[j].quantity) * parseInt($scope.project.items[j].UnitMaterial) + parseInt($scope.project.items[j].UnitLabor);
      };
      return totalMoney.toString();
  };

  window.onbeforeunload = function (e) {
    window.location("home.html");
  };
  $scope.showTo = function () {
    $scope.project.additionalInfo.to.show = true;
  };
  $scope.changeProjectTo = function () {
    $scope.project.additionalInfo.to.show = false;

    $scope.updateThisReport();
  };
  $scope.showFrom = function () {
    $scope.project.additionalInfo.from.show = true;
  };
  $scope.changeProjectFrom = function () {
    $scope.project.additionalInfo.from.show = false;

    $scope.updateThisReport();
  };
  $scope.showDate = function () {
    $scope.project.additionalInfo.date.show = true;
  };
  $scope.changeProjectDate = function () {
    $scope.project.additionalInfo.date.show = false;

    $scope.updateThisReport();
  };
  $scope.showQuotation = function () {
    $scope.project.additionalInfo.quotation.show = true;
  };
  $scope.changeProjectQuotation = function () {
    $scope.project.additionalInfo.quotation.show = false;

    $scope.updateThisReport();
  };
  $scope.showTotalPages = function () {
    $scope.project.additionalInfo.toTalPages.show = true;
  };
  $scope.changeProjectTotalPages = function () {
    $scope.project.additionalInfo.toTalPages.show = false;

    $scope.updateThisReport();
  };
  $scope.showYourRef = function () {
    $scope.project.additionalInfo.yourRef.show = true;
  };
  $scope.changeProjectYourRef = function () {
    $scope.project.additionalInfo.yourRef.show = false;

    $scope.updateThisReport();
  };
  $scope.showTitle = function () {
    $scope.project.additionalInfo.projectTitle.show = true;
  };
  $scope.changeProjectTitle = function () {
    $scope.project.additionalInfo.projectTitle.show = false;

    $scope.updateThisReport();
  };
  $scope.showScopeOfWork = function () {
    $scope.project.additionalInfo.scopeOfWork.show = true;
  };
  $scope.changeProjectScopeOfWork = function () {
    $scope.project.additionalInfo.scopeOfWork.show = false;

    $scope.updateThisReport();
  };
  $scope.showPrice = function () {
    $scope.project.additionalInfo.price.show = true;
  };
  $scope.changeProjectPrice = function () {
    $scope.project.additionalInfo.price.show = false;

    $scope.updateThisReport();
  };
  $scope.showPayment = function () {
    $scope.project.additionalInfo.paymentTerm.show = true;
  };
  $scope.changeProjectPayment= function () {
    $scope.project.additionalInfo.paymentTerm.show = false;

    $scope.updateThisReport();
  };
  $scope.showTimming = function () {
    $scope.project.additionalInfo.timming.show = true;
  };
  $scope.changeProjectTimming= function () {
    $scope.project.additionalInfo.timming.show = false;

    $scope.updateThisReport();
  };
  $scope.showValidity = function () {
    $scope.project.additionalInfo.validity.show = true;
  };
  $scope.changeProjectValidity= function () {
    $scope.project.additionalInfo.validity.show = false;

    $scope.updateThisReport();
  };
  jQuery(document).on("click", ".clickToMargin", function() {
    var hg = jQuery(this).height();
    hg = hg + 20;
    jQuery(this).css({
      "height": hg
    });
    console.log(hg);
  });
}]);
