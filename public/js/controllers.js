var app = angular.module("app")

.controller("mainController", ["$scope", "$http", "$rootScope", "$window", "printService", function ($scope, $http, $rootScope, $window, printService) {

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
            var JSONData = reportObj.jsonString;
            var ReportTitle = reportObj._id;
            var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
            var dataObj = arrData.data;
            console.log(dataObj);
            var CSV = '';
            CSV += '_id:,' + dataObj._id + '\r\n';
            CSV += 'Day created:,' + '"' + dataObj.Lastmodified + '"' + '\r\n';
            CSV += 'Status:,'  + dataObj.Status + '\r\n';
            CSV += 'Comment:,' + dataObj.Comment + '\r\n';
            CSV += '\r\n\n';

            var row = "";
            for (i = 0; i < dataObj.fields.length; i++) {
              var data = dataObj.fields[i].fieldname;
              data = data.replace(",", "");
              row = row + "," + data;
            };
            row = row.slice(1);
            CSV += row + '\r\n';

            for (var i = 0; i <= dataObj.items.length - 1; i++) {
              var entry = dataObj.items[i];
              var row = "";
              for (k = 0; k < entry.length; k++) {
                var data = entry[k];
                data = data.replace(",", "");
                row = row + "," + data;
              };
              row = row.slice(1);
              CSV += row + '\r\n';
            };
            for (var j = 0; j <= dataObj.groups.length - 1; j++) {
              var name = dataObj.groups[j].name + '\r\n';
              CSV += name;
              for (var i = 0; i <= dataObj.groups[j].items.length - 1; i++) {
                  var row = "";
                  var entry = dataObj.groups[j].items[i];
                  for (k = 0; k < entry.length; k++) {
                    var data = entry[k];
                    data = data.replace(",", "");
                    row = row + "," + data;
                  };
                  row = row.slice(1);
                  CSV += row + '\r\n';
              };
            };
            console.log(CSV);
            if (CSV == '') {
                alert("Invalid data");
                return;
            }
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
    $scope.changeThisReportComment = function (reportObj) {
      reportObj.showInput = false;
      $scope.updateAllReports();
    };
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
    $scope.deleteThisOngoingReport = function (id, index) {
      var cf = confirm("Do you want to delete this report?");
      if (cf == true) {
        console.log("Delete action fired!");
        $rootScope.ongoingReports.splice(index, 1);
      };
      $scope.updateAllReports();
    };
    $scope.deleteThisPassReport = function (id, index) {

        var cf = confirm("Do you want to delete this report?");
        if (cf == true) {
          console.log("Delete action fired!");

          $rootScope.passReports.splice(index, 1);

        };
        $scope.updateAllReports();
    };
    $scope.deleteThisFailedReport = function (id, index) {

        var cf = confirm("Do you want to delete this report?");
        if (cf == true) {
          console.log("Delete action fired!");
          $rootScope.failedReports.splice(index, 1);
        };
        $scope.updateAllReports();
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

    $scope.savedGroups = [];
    $scope.groupsInView = [];
    if ($window.localStorage["databases"]) {
      $rootScope.databases = JSON.parse($window.localStorage["databases"]);
    } else {
      $rootScope.databases = [
        {
          name: "BME students",
          dbid: "db#312",
          date: getToDayFormatted(),
          fields: [
            {
              fieldname: "#ID",
              type: "text",
              currentData: "",
            },
            {
              fieldname: "Name",
              type: "text",
              currentData: "",
            },
            {
              fieldname: "Phone",
              type: "text",
              currentData: "",
            },
            {
              fieldname: "GPA",
              type: "text",
              currentData: "",
            },
          ],
          data: [
            ["BEBEIU13051"  ,"Pham, Khoi Nguyen"  ,"0914 118 896" ,"3.62"],
            ["BEBEIU14092"  ,"Nguyen, Minh Quan"  ,"0872 345 284" ,"3.54"],
            ["BABANM09233"  ,"Tran, Hoang Nam"    ,"0453 274 283" ,"3.87"],
            ["BTBTMA17212"  ,"Do, Duy Viet"       ,"0917 273 247" ,"3.91"],
            ["BABANM09233"  ,"Hoang, Phuong Bac"  ,"0172 482 465" ,"3.23"],
          ],
        }
      ];
    };

    $scope.selectedDatabaseIndex = $rootScope.databases.length - 1;
    $scope.selectedDatabase = $rootScope.databases[$scope.selectedDatabaseIndex];
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
        fields: $scope.selectedDatabase.fields,
        items: [],
        groups: []
    };
    $scope.currentCategory = {
        name: "",
        items: []
    };
    if ($window.localStorage['ongoingReports']) {
      $rootScope.ongoingReports = JSON.parse($window.localStorage['ongoingReports']);
    }
    else {
      $rootScope.ongoingReports = [];
    };
    if ($window.localStorage['passReports']) {
      $rootScope.passReports = JSON.parse($window.localStorage['passReports']);
    }
    else {
      $rootScope.passReports = [];
    };
    if ($window.localStorage['failedReports']) {
      $rootScope.failedReports = JSON.parse($window.localStorage['failedReports']);
    }
    else {
      $rootScope.failedReports = [];
    };
    // functions

    $scope.createNewProject = function () {
        $scope.newProject.name = $scope.tb_projectName;
        $scope.newProject._id = $scope.tb_projectName;
        $scope.newProject.reportUrl = "report/" + $scope.tb_projectName + ".json",
        $scope.newProject.company = $scope.tb_company;
        $scope.newProject.Status = jQuery("#select_Category option:selected").text();
        jQuery("#projectInfoView").css("display", "none");
        jQuery("#previewProjectView").css("display", "inline-block");
        $scope.stepNum = 2;
    };
    $scope.saveThisProjectToServer = function () {
      $scope.newProject.Comment = "none";
      var dataString = {
          data: $scope.newProject
      };
      $scope.newProject.jsonString = JSON.stringify(dataString);

      if ($scope.newProject.Status == "ongoing") {
          $rootScope.ongoingReports.push($scope.newProject);
          $window.localStorage['ongoingReports'] = JSON.stringify($rootScope.ongoingReports);
      };
      if ($scope.newProject.Status == "pass") {
          $rootScope.passReports.push($scope.newProject);
          $window.localStorage['passReports'] = JSON.stringify($rootScope.passReports);
      };
      if ($scope.newProject.Status == "failed") {
          $rootScope.failedReports.push($scope.newProject);
          $window.localStorage['failedReports'] = JSON.stringify($rootScope.failedReports);
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
          fields: $scope.selectedDatabase.fields,
          items: [],
          groups: []
      };
      console.log("Report saved successfully");
      jQuery("#previewProjectView").hide();
      jQuery("#projectInfoView").show();
      $scope.showHomePannel();
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
    $scope.removeThisItemFromNewProject = function (groupName, index) {
        var cf = confirm("Delete this Item?");
        if (cf == true) {
          for (i = 0; i <= $scope.newProject.groups.length - 1; i++) {
              if ($scope.newProject.groups[i].name == groupName) {
                  $scope.newProject.groups[i].items.splice(index, 1);
                  break;
              };
          };
        };
    };
    $scope.removeThisItemFromSelectedProject = function (groupName, index) {

      var cf = confirm("Delete this Item?");
      if (cf == true) {
        for (i = 0; i <= $scope.selectedProject.groups.length - 1; i++) {
            if ($scope.selectedProject.groups[i].name == groupName) {
                $scope.selectedProject.groups[i].items.splice(index, 1);
                break;
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
    $scope.addItemToThisGroup = function (entryarr, groupindex, selectedGroupIndex, itemindex) {
      itemObj = entryarr;
      if ( $scope.currentlyInSelectedProjectView == true ) {  // this event is for updating projects
        if ( $scope.actionToInsertNewItemAboveThisItemAndThisItemHasNoGroup ) {
          $scope.selectedProject.items.splice($scope.indexOfItemToInsertAboveAndHasNoGroup, 0, JSON.parse(JSON.stringify(itemObj)));
          $scope.actionToInsertNewItemAboveThisItemAndThisItemHasNoGroup = false;
          $scope.indexOfItemToInsertAboveAndHasNoGroup = -1;
        }
        if ( $scope.actionToInsertNewItemAboveThisItemAndThisItemBelongToAGroup ) {
            for (i = 0; i < $scope.selectedProject.groups.length; i ++) {
              if ($scope.selectedProject.groups[i].name == $scope.nameOfTheGroupWhereNewItemInsertAbove) {
                $scope.selectedProject.groups[i].items.splice($scope.indexOfItemToInsertAboveAndBelongToAGroup, 0, JSON.parse(JSON.stringify(itemObj)));
              }
            };
          $scope.actionToInsertNewItemAboveThisItemAndThisItemBelongToAGroup = false;
          $scope.indexOfItemToInsertAboveAndBelongToAGroup = -1;
          $scope.nameOfTheGroupWhereNewItemInsertAbove = null;
        }
        else {
          if ( $scope.shouldInsertThisItemToSelectedProject ) {
            $scope.selectedProject.items.push(JSON.parse(JSON.stringify(itemObj)));
          }
          else {
            $scope.selectedProject.groups[selectedGroupIndex].items.push(JSON.parse(JSON.stringify(itemObj)));
          };
        };

      } else {                                          // this event is fired from create new project view
        if ( $scope.shouldInsertThisItemToProject ) {   // insert this item to items field
          $scope.newProject.items.push(JSON.parse(JSON.stringify(itemObj)));
        }
        else {
          $scope.newProject.groups[groupindex].items.push(JSON.parse(JSON.stringify(itemObj)));
        };
      }
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
    jQuery("#btn_createNewItem").on("click", function () {
        jQuery("#createNewItemInfoView").toggle();
    });
    jQuery("#btn_searchItem").on("click", function () {
        jQuery("#tableSearchItem").toggle();
    });
    $scope.updateAllReports = function() {
      $window.localStorage["ongoingReports"] = JSON.stringify($rootScope.ongoingReports);
      $window.localStorage["passReports"] = JSON.stringify($rootScope.passReports);
      $window.localStorage["failedReports"] = JSON.stringify($rootScope.failedReports);
    };
    $scope.updateAllDatabases = function() {
      $window.localStorage["databases"] = JSON.stringify($rootScope.databases);
    };
    $scope.sendThisReportToPassedProjectsSentByOngoing = function (reportObj, index) {
      $rootScope.ongoingReports.splice(index, 1);
      $rootScope.passReports.push(reportObj);
      $scope.updateAllReports();
    };
    $scope.sendThisReportToFailedProjectsSentByOngoing = function (reportObj, index) {
      $rootScope.ongoingReports.splice(index, 1);
      $rootScope.failedReports.push(reportObj);
      $scope.updateAllReports();
    };
    ///////
    $scope.sendThisReportToOngoingProjectsSentByPassed = function (reportObj, index) {
      $rootScope.passReports.splice(index, 1);
      $rootScope.ongoingReports.push(reportObj);
      $scope.updateAllReports();
    };
    $scope.sendThisReportToFailedProjectsSentByPassed = function (reportObj, index) {
      $rootScope.passReports.splice(index, 1);
      $rootScope.failedReports.push(reportObj);
      $scope.updateAllReports();
    };
    ///////
    $scope.sendThisReportToOngoingProjectsSentByFailed = function (reportObj, index) {
      $rootScope.failedReports.splice(index, 1);
      $rootScope.ongoingReports.push(reportObj);
      $scope.updateAllReports();
    };
    $scope.sendThisReportToPassedProjectsSentByFailed = function (reportObj, index) {
      $rootScope.failedReports.splice(index, 1);
      $rootScope.passReports.push(reportObj);
      $scope.updateAllReports();
    };
    ////////
    $scope.saveAndCloseThisReport = function () {
      $scope.currentlyInSelectedProjectView = false;
      var objToStringnify = {
        data: $scope.selectedProject
      };
      for (i = 0; i <= $rootScope.ongoingReports.length - 1; i++) {
        if ( $scope.selectedProject._id == $rootScope.ongoingReports[i]._id ) {
          $rootScope.ongoingReports[i].jsonString = JSON.stringify(objToStringnify);
          break;
        };
      };
      for (i = 0; i <= $rootScope.failedReports.length - 1; i++) {
        if ( $scope.selectedProject._id == $rootScope.failedReports[i]._id ) {
          $rootScope.failedReports[i].jsonString = JSON.stringify(objToStringnify);
          break;
        };
      };
      for (i = 0; i <= $rootScope.passReports.length - 1; i++) {
        if ( $scope.selectedProject._id == $rootScope.passReports[i]._id ) {
          $rootScope.passReports[i].jsonString = JSON.stringify(objToStringnify);
          break;
        };
      };
      jQuery("#nenDen").hide();
      jQuery("#popup").hide();
      jQuery(".left_col").show();
      jQuery("#popup2").hide();
      $scope.updateAllReports();
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

      var span = document.getElementsByClassName("close")[0];

      span.onclick = function() {
          modal.style.display = "none";
      };
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
      if ($scope.newProject.Status == "ongoing") {
          $rootScope.ongoingReports.push($scope.newProject);
      };
      if ($scope.newProject.Status == "pass") {
          $rootScope.passReports.push($scope.newProject);
      };
      if ($scope.newProject.Status == "failed") {
          $rootScope.failedReports.push($scope.newProject);
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
          fields: $scope.selectedDatabase.fields,
          items: [],
          groups: []
      };
      console.log("Report saved successfully");
      var modal = document.getElementById('myModal');
      modal.style.display = "none";
      $scope.updateAllReports();
    };
    // functions
    // when "save button" is clicked
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
      $window.localStorage['BOM_reportToPrint_string'] = report.jsonString;
      $window.open("#/print", "_blank");
    };





    // $rootScope.databases = [
    //   {
    //     name: "BME students",
    //     dbid: "db#312",
    //     date: getToDayFormatted(),
    //     fields: [
    //       {
    //         fieldname: "#ID",
    //         type: "text",
    //         currentData: "",
    //       },
    //       {
    //         fieldname: "Name",
    //         type: "text",
    //         currentData: "",
    //       },
    //       {
    //         fieldname: "Phone",
    //         type: "text",
    //         currentData: "",
    //       },
    //       {
    //         fieldname: "GPA",
    //         type: "text",
    //         currentData: "",
    //       },
    //     ],
    //     data: [
    //       ["BEBEIU13051"  ,"Pham, Khoi Nguyen"  ,"0914 118 896" ,"3.62"],
    //       ["BEBEIU14092"  ,"Nguyen, Minh Quan"  ,"0872 345 284" ,"3.54"],
    //       ["BABANM09233"  ,"Tran, Hoang Nam"    ,"0453 274 283" ,"3.87"],
    //       ["BTBTMA17212"  ,"Do, Duy Viet"       ,"0917 273 247" ,"3.91"],
    //       ["BABANM09233"  ,"Hoang, Phuong Bac"  ,"0172 482 465" ,"3.23"],
    //     ],
    //   }
    // ];
    // $scope.selectedDatabase = $rootScope.databases[0];
    $scope.showCreateNewDatabasePannel = function() {
      jQuery(".home_pannel").hide();
      jQuery("#createNewDatabasePannel").show();
    };
    $scope.showImportExportPannel = function() {
      jQuery(".home_pannel").hide();
      jQuery("#importExportPannel").show();
    };

    $scope.newDatabase = {
      name: "",
      dbid: "db#" + Math.floor((Math.random() * 10000)),
      date: getToDayFormatted(),
      fields: [],
      data: [],
    };
    $scope.newField = {
      fieldname: "",
      type: "text",
      currentData: "",
    };

    $scope.insertThisFieldToNewDatabase = function(fieldObj) {
      $scope.newDatabase.fields.push(fieldObj);
      $scope.newField = {
        fieldname: "",
        type: "text",
        currentData: "",
      };
    };
    $scope.createThisDatabase = function() {
      $rootScope.databases.push($scope.newDatabase);
      $scope.updateAllDatabases();
      $scope.newDatabase = {
        name: "",
        dbid: "db#" + Math.floor((Math.random() * 10000)),
        date: getToDayFormatted(),
        fields: [],
        data: [],
      };
      var index = $rootScope.databases.length - 1;
      $scope.chooseThisDatabase(index);
    };
    $scope.deleteThisFieldFromNewDatabase = function(index){
      $scope.newDatabase.fields.splice(index, 1);
    };
    $scope.checkLengthDatabases = function() {
      var len = $rootScope.databases.length;
      if (len >= 1) {
        return true;
      }
      else {
        return false;
      };
    };

    console.log($scope.selectedDatabase);
    $scope.chooseThisDatabase = function(index) {
      $scope.selectedDatabase = $rootScope.databases[index];
      console.log($scope.selectedDatabase);
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
          fields: $scope.selectedDatabase.fields,
          items: [],
          groups: []
      };
    };
    $scope.deleteThisDatabase = function(index) {
      $rootScope.databases.splice(index, 1);
      $scope.updateAllDatabases();
    };


    $scope.updateThisDatabase = function() {
      var db_id_to_find = $scope.selectedDatabase.dbid;
      for (i = 0; i < $rootScope.databases.length; i++) {
        if ($rootScope.databases[i].dbid == db_id_to_find) {
          $rootScope.databases[i] = $scope.selectedDatabase;
          $window.localStorage['databases'] = JSON.stringify($rootScope.databases);
          console.log("Update database " + db_id_to_find + " succeeded");
          console.log($rootScope.databases);
          break;
        };
      };
    };
    $scope.deleteThisEntryFromSelectedDatabase = function(index){
      console.log("Delete entry " + $scope.selectedDatabase.data[index][0] + " succeed");
      $scope.selectedDatabase.data.splice(index, 1);
      $scope.updateThisDatabase();
    };
    $scope.createNewItem = function () {
      var arr = [];
      for (i = 0; i < $scope.selectedDatabase.fields.length; i++) {
        arr.push($scope.selectedDatabase.fields[i].currentData);
        $scope.selectedDatabase.fields[i].currentData = "";
      };
      $scope.selectedDatabase.data.push(arr);
      $scope.updateThisDatabase();
    };
    $scope.updateThisEntry = function(entryind, valueind, value) {
      $scope.selectedDatabase.data[entryind][valueind] = value;
      $scope.updateThisDatabase();
    };
    $scope.updateThisEntryInNewProjectWithoutGroup = function(entryind, valueind, value) {
      $scope.newProject.items[entryind][valueind] = value;
    };
    $scope.updateThisGroupTitleInNewProject = function(groupind, value) {
      $scope.newProject.groups[groupind].name = value;
    };
    $scope.updateThisEntryInNewProjectWithGroup = function(groupind, entryind, valueind, value) {
      $scope.newProject.groups[groupind].items[entryind][valueind] = value;
    };
    $scope.updateThisEntryInSelectedProjectWithoutGroup = function(entryind, valueind, value) {
      $scope.selectedProject.items[entryind][valueind] = value;
    };
    $scope.updateThisGroupTitleInSelectedProject = function(groupind, value) {
      $scope.selectedProject.groups[groupind].name = value;
    };
    $scope.updateThisEntryInSelectedProjectWithGroup = function(groupind, entryind, valueind, value) {
      $scope.selectedProject.groups[groupind].items[entryind][valueind] = value;
    };
    // IMPORT/EXPORT PACKAGE
    $scope.createExportPackage = function() {
      var obj = {
        databases: $rootScope.databases,
        ongoingProjects: $rootScope.ongoingReports,
        passedProjects: $rootScope.passReports,
        failedProjects: $rootScope.failedReports
      };
      console.log(obj);
      $scope.exprortPackage = JSON.stringify(obj);
    };
    $scope.importThisPackage = function(datastring) {
      if (datastring) {
        try {
          var JSONData = JSON.parse(datastring);
          var obj = JSONData;
          console.log(obj);
          for (i = 0; i < obj.databases.length; i++) {
            $rootScope.databases.push(obj.databases[i]);
          };
          for (i = 0; i < obj.ongoingProjects.length; i++) {
            $rootScope.ongoingReports.push(obj.ongoingProjects[i]);
          };
          for (i = 0; i < obj.passedProjects.length; i++) {
            $rootScope.passReports.push(obj.passedProjects[i]);
          };
          for (i = 0; i < obj.failedProjects.length; i++) {
            $rootScope.failedReports.push(obj.failedProjects[i]);
          };
          $scope.updateAllReports();
          $scope.updateAllDatabases();
          $scope.importPackage = "";
          $scope.exprortPackage = "";
        } catch(e) {
          alert("The package is either not an object or not useable with this software. Please try to export data scheme using this software");
        };
      } else {
        alert("No package inserted!");
      };
    };
}])
.controller("printController", ["$scope", "$http", "$rootScope", "printService", "$window", function ($scope, $http, $rootScope, printService, $window) {
  $scope.company = {
    name: "Cassandra startup",
    address: "Office A1.513, International University, Linh Trung district, Vietnam",
    tel: "(08)-38766575",
    fax: "(08)-38595459",
    logo: "images/logo.png"
  };
  if ($window.localStorage['ongoingReports']) {
    $rootScope.ongoingReports = JSON.parse($window.localStorage['ongoingReports']);
  }
  else {
    $rootScope.ongoingReports = [];
  };
  if ($window.localStorage['passReports']) {
    $rootScope.passReports = JSON.parse($window.localStorage['passReports']);
  }
  else {
    $rootScope.passReports = [];
  };
  if ($window.localStorage['failedReports']) {
    $rootScope.failedReports = JSON.parse($window.localStorage['failedReports']);
  }
  else {
    $rootScope.failedReports = [];
  };
  $scope.pageTitle = "Print Template";
  $scope.projectString = $window.localStorage['BOM_reportToPrint_string'];
  $scope.project = JSON.parse($scope.projectString).data;
  if ($scope.project.additionalInfo == null) {
    $scope.project.additionalInfo = {
      to: {
        value: "Type someone here...",
        show: false
      },
      from: {
        value: $scope.company.name,
        show: false
      },
      date: {
        value: $scope.project.Lastmodified,
        show: false
      },
      quotation: {
        value: "This is the invoice for ...",
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
        value: "<ul><li>Implement cassandra hardwares: hubs and IoT devices into Hoang Phuong clinic's infrastructure</li><li>24/7 service support</li><li>Training the physician to use our software package</li></ul>",
        show: false
      },
      price: {
        value: "Total price: (exclude 10% VAT, Unit VNĐ)",
        show: false
      },
      paymentTerm: {
        value: "<ul><li>Dow payment 30% of the contact value after PO/contact signed and</li><li>70% balance to be discussed on payment schedule per completion progress</li></ul>",
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
    $scope.project.jsonString = JSON.stringify(objToStringnify);
    for (i = 0; i <= $rootScope.ongoingReports.length - 1; i++) {
      if ( $scope.project._id == $rootScope.ongoingReports[i]._id ) {
        $rootScope.ongoingReports[i] = $scope.project;
        break;
      };
    };
    for (i = 0; i <= $rootScope.failedReports.length - 1; i++) {
      if ( $scope.selectedProject._id == $rootScope.failedReports[i]._id ) {
        $rootScope.failedReports[i] = $scope.project;
        break;
      };
    };
    for (i = 0; i <= $rootScope.passReports.length - 1; i++) {
      if ( $scope.selectedProject._id == $rootScope.passReports[i]._id ) {
        $rootScope.passReports[i] = $scope.project;
        break;
      };
    };
    $scope.updateAllReports();
  };
  $scope.updateAllReports = function() {
    $window.localStorage["ongoingReports"] = JSON.stringify($rootScope.ongoingReports);
    $window.localStorage["passReports"] = JSON.stringify($rootScope.passReports);
    $window.localStorage["failedReports"] = JSON.stringify($rootScope.failedReports);
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
