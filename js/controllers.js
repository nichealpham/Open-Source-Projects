var app = angular.module("app")

.controller("newProjectController", ["$scope", "$http", function ($scope, $http) {
    var jq = $.noConflict();
    $scope.stepNum = 1;
    $scope.shouldInsertThisItemToProject = false;
    $scope.shouldInsertThisItemToSelectedProject = false;
    $scope.selectedGroupIndex = -1;
    $scope.actionToInsertNewItemAboveThisItemAndThisItemHasNoGroup = false;
    $scope.actionToInsertNewItemAboveThisItemAndThisItemBelongToAGroup = false;
    $scope.indexOfItemToInsertAboveAndHasNoGroup = -1;
    $scope.indexOfItemToInsertAboveAndBelongToAGroup = -1;
    $scope.nameOfTheGroupWhereNewItemInsertAbove = null;
    jq("#btn_displayCreateNewProject").on("click", function () {
        jq("#currentSelectedItemsView").css("display", "inline-block");
        jq("#savedGroupsView").css("display", "none");
    });
    jq("#btn_displayMySavedGroups").on("click", function () {
        jq("#currentSelectedItemsView").css("display", "none");
        jq("#savedGroupsView").css("display", "inline-block");
    });
    $scope.goToCheckOutPage = function () {
      jq("#previewProjectView").css("display", "none");
      jq("#checkOutView").css("display", "inline-block");
    };
    jq("#goBackToPreviewProjectPage").on("click", function () {
        jq("#previewProjectView").css("display", "inline-block");
        jq("#checkOutView").css("display", "none");
    });
    jq("#btn_indertItemToProject").on("click", function () {
      jq("#nenDen").css("display", "inline-block");
      jq(".left_col").hide();
      jq("#popup").css("display", "inline-block");
      $scope.shouldInsertThisItemToProject = true;
    });
    jq("#btn_indertItemToSelectedProject").on("click", function () {
      jq("#nenDen").css("display", "inline-block");
      jq(".left_col").hide();
      jq("#popup2").hide();
      jq("#popup").css("display", "inline-block");
      $scope.shouldInsertThisItemToSelectedProject = true;
    });
    jq(".btnComment").on("click", function () {
      jq(".formComment").hide();
      jq(this).next().toggle();
    });
    jq("#nenDen").on("click", function () {
      if ( $scope.currentlyInSelectedProjectView == false ) {
        jq("#nenDen").css("display", "none");
        jq("#popup").css("display", "none");
        jq("#popup2").css("display", "none");
        jq(".left_col").show();
      } else {
        jq("#popup").hide();
        jq("#popup2").show();
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
      jq("#databasePannel").hide();
      jq("#newProjectPannel").hide();
      jq("#homePannel").show();
    };
    $scope.showNewProjectPannel = function () {
      jq("#databasePannel").hide();
      jq("#newProjectPannel").show();
      jq("#homePannel").hide();
    };
    $scope.showDatabasePannel = function () {
      jq("#databasePannel").show();
      jq("#newProjectPannel").hide();
      jq("#homePannel").hide();
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

 //   $http.get("bomService.asmx/getAllReport").then(function (response) {
 //     $scope.reports = response.data;
 //  });
    $http.get("bomService.asmx/getAllOngoingReport").then(function (response) {
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
    $http.get("bomService.asmx/getAllPassReport").then(function (response) {
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
      $http.post('bomService.asmx/updateReportComment', { "_id": reportObj._id, "Comment": reportObj.Comment }).then(function () {
        console.log("Comment changed successfully");
      });
    };
    $http.get("bomService.asmx/getAllFaliedlReport").then(function (response) {
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
    // end of get report
    // start of get dataItem
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
    $http.get("bomService.asmx/getAllItem").then(function (response) {
      if (response.data.length > 0) {
        $scope.items = response.data;               // use for inserting new item to group
        for (i = 0; i <= $scope.items.length - 1; i++) {
          $scope.items[i].saved = true;
        };
      }
      else {
        $scope.items = [];
      };
    //  console.log($scope.items);
    });
    $scope.deleteThisOngoingReport = function (id, index) {
        console.log("Delete action fired!");
        $http.post("bomService.asmx/deleteReportByID", { "_id": id }).then(function () {
            $scope.ongoingReports.splice(index, 1);
        });
    };
    $scope.deleteThisPassReport = function (id, index) {
        console.log("Delete action fired!");
        $http.post("bomService.asmx/deleteReportByID", { "_id": id }).then(function () {
            $scope.passReports.splice(index, 1);
        });
    };
    $scope.deleteThisFailedReport = function (id, index) {
        console.log("Delete action fired!");
        $http.post("bomService.asmx/deleteReportByID", { "_id": id }).then(function () {
            $scope.failedReports.splice(index, 1);
        });
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
      jq("#nenDen").css("display", "inline-block");
      jq(".left_col").hide();
      jq("#popup2").css("display", "inline-block");
    };
    $scope.currentCategory = {
        name: "",
        items: []
    };
    // http.get from server, store the saved groups in this scope
    $scope.savedGroups = [];
    $scope.groupsInView = [];

    // functions

    $scope.createNewProject = function () {
        console.log("button clicked");
        $scope.newProject.name = $scope.tb_projectName;
        $scope.newProject._id = $scope.tb_projectName;
        $scope.newProject.reportUrl = "report/" + $scope.tb_projectName + ".json",
        $scope.newProject.company = $scope.tb_company;
        $scope.newProject.Status = jq("#select_Category option:selected").text();
        jq("#projectInfoView").css("display", "none");
  //      jq("#addItemView").css("display", "inline-block");
        jq("#previewProjectView").css("display", "inline-block");
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
        $http.post("bomService.asmx/addNewReport", { "_id": $scope.newProject._id, "reportUrl": $scope.newProject.reportUrl, "Status": $scope.newProject.Status, "Comment": $scope.newProject.Comment, "Createdby": $scope.newProject.Createdby, "Lastmodified": $scope.newProject.Lastmodified, "jsonString": $scope.newProject.jsonString }).then(function () {
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
        $scope.newProject.groups.splice(index, 1);
    };
    $scope.removeThisGroupFromSelectedProject = function (index) {
        $scope.selectedProject.groups.splice(index, 1);
    };
    $scope.removeThisGroupFromSelectedProject = function (index) {
      $scope.selectedProject.groups.splice(index, 1);
    };
    $scope.removeThisItemFromNewProject = function (groupName, index, item) {
        for (i = 0; i <= $scope.newProject.groups.length - 1; i++) {
            if ($scope.newProject.groups[i].name == groupName) {
                $scope.newProject.groups[i].items.splice(index, 1);
            };
        };
//        $scope.items.splice(item._id, 0, item);
    };
    $scope.removeThisItemFromSelectedProject = function (groupName, index, item) {
      for (i = 0; i <= $scope.selectedProject.groups.length - 1; i++) {
          if ($scope.selectedProject.groups[i].name == groupName) {
              $scope.selectedProject.groups[i].items.splice(index, 1);
          };
      };
    };
    $scope.removeThisItemFromNewProjectItemsList = function (index) {
      $scope.newProject.items.splice(index, 1);
    };
    $scope.removeThisItemFromSelectedProjectItemsList = function (index) {
      $scope.selectedProject.items.splice(index, 1);
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
        $scope.currentCategory.items.push(item);
    };
    $scope.updateGroupName = function (index, value) {
        $scope.newProject.groups[index].name = value;
    };
    $scope.removeItemFromCurrentGroup = function (index) {
        $scope.currentCategory.items.splice(index, 1);
    };
    $scope.saveCurrentCaterogy = function () {
        $scope.currentCategory.name = $scope.tb_currentGroupName;
        $scope.savedGroups.push($scope.currentCategory);
        $scope.newProject.groups.push($scope.currentCategory);
     //   $scope.groupsInView = [];
     //   for (i = 0; i <= $scope.savedGroups.length - 1; i++) {
     //       $scope.groupsInView.push($scope.savedGroups[i]);
     //   };
        console.log("Success");
        console.log($scope.currentCategory.name);
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
      jq("#nenDen").css("display", "inline-block");
      jq(".left_col").hide();
      jq("#popup").css("display", "inline-block");
    };
    $scope.addNewItemToThisSelectedGroup = function (index) {
      $scope.selectedGroupIndex = index;
      $scope.shouldInsertThisItemToSelectedProject = false;
      jq("#nenDen").css("display", "inline-block");
      jq(".left_col").hide();
      jq("#popup").show();
      jq("#popup2").hide();
    };

    $scope.insertNewItemAboveThisItemAndThisItemHasNoGroup = function (itemIndex) {
      jq("#nenDen").css("display", "inline-block");
      jq(".left_col").hide();
      jq("#popup").show();
      jq("#popup2").hide();
      $scope.actionToInsertNewItemAboveThisItemAndThisItemHasNoGroup = true;  // initiated with false
      $scope.indexOfItemToInsertAboveAndHasNoGroup = itemIndex;               // initiated with -1
    };
    $scope.insertNewItemAboveThisItemAndThisItemBelongToAGroup = function (groupName, itemIndex) {
      jq("#nenDen").css("display", "inline-block");
      jq(".left_col").hide();
      jq("#popup").show();
      jq("#popup2").hide();
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
            itemObj.totalMoney = parseInt(itemObj.quantity) * parseInt(itemObj.UnitMaterial) + parseInt(itemObj.UnitLabor);
            $scope.selectedProject.items.push(JSON.parse(JSON.stringify(itemObj)));
          }
          else {
            itemObj.quantity = 1;
            itemObj.saved = true;
            itemObj.totalMoney = parseInt(itemObj.quantity) * parseInt(itemObj.UnitMaterial) + parseInt(itemObj.UnitLabor);
            $scope.selectedProject.groups[selectedGroupIndex].items.push(JSON.parse(JSON.stringify(itemObj)));
          };
        };

      } else {                                          // this event is fired from create new project view
        if ( $scope.shouldInsertThisItemToProject ) {   // insert this item to items field
          itemObj.quantity = 1;
          itemObj.saved = true;
          itemObj.totalMoney = parseInt(itemObj.quantity) * parseInt(itemObj.UnitMaterial) + parseInt(itemObj.UnitLabor);
          $scope.newProject.items.push(JSON.parse(JSON.stringify(itemObj)));
        }
        else {
          itemObj.quantity = 1;
          itemObj.saved = true;
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
        jq("#btn_goToNextStage").css("display", "inline-block");
    };
    $scope.previewProject = function () {
        jq("#addItemView").css("display", "none");
        jq("#previewProjectView").css("display", "inline-block");
        $scope.stepNum = 3;
    };
    $scope.goBackToAddItemPage = function () {
        jq("#addItemView").css("display", "inline-block");
        jq("#previewProjectView").css("display", "none");
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
    jq("#btn_createNewItem").on("click", function () {
        jq("#createNewItemInfoView").toggle();
    });
    jq("#btn_searchItem").on("click", function () {
        jq("#tableSearchItem").toggle();
    });
    $scope.sendThisReportToPassedProjectsSentByOngoing = function (reportObj, index) {
      $scope.ongoingReports.splice(index, 1);
      $scope.passReports.push(reportObj);
      $http.post('bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "pass" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    $scope.sendThisReportToFailedProjectsSentByOngoing = function (reportObj, index) {
      $scope.ongoingReports.splice(index, 1);
      $scope.failedReports.push(reportObj);
      $http.post('bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "failed" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    ///////
    $scope.sendThisReportToOngoingProjectsSentByPassed = function (reportObj, index) {
      $scope.passReports.splice(index, 1);
      $scope.ongoingReports.push(reportObj);
      $http.post('bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "ongoing" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    $scope.sendThisReportToFailedProjectsSentByPassed = function (reportObj, index) {
      $scope.passReports.splice(index, 1);
      $scope.failedReports.push(reportObj);
      $http.post('bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "failed" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    ///////
    $scope.sendThisReportToOngoingProjectsSentByFailed = function (reportObj, index) {
      $scope.failedReports.splice(index, 1);
      $scope.ongoingReports.push(reportObj);
      $http.post('bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "ongoing" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    $scope.sendThisReportToPassedProjectsSentByFailed = function (reportObj, index) {
      $scope.failedReports.splice(index, 1);
      $scope.passReports.push(reportObj);
      $http.post('bomService.asmx/updateReportStatus', { "_id": reportObj._id, "Status": "pass" }).then(function (response) {
        console.log("Status updated successfully");
      });
    };
    ////////
    $scope.saveAndCloseThisReport = function () {
      $scope.currentlyInSelectedProjectView = false;
      var objToStringnify = {
        data: $scope.selectedProject
      };
      console.log(objToStringnify);
      console.log(JSON.stringify(objToStringnify));
      $http.post('bomService.asmx/updateReportJsonString', { "_id": $scope.selectedProject._id, "jsonString": JSON.stringify(objToStringnify) }).then(function (response) {
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
        jq("#nenDen").hide();
        jq("#popup").hide();
        jq(".left_col").show();
        jq("#popup2").hide();
      });
    };
    // local variables
//    $scope.items = [
//        { id: "1", name: "PIPE S-40 BE ASTM A106 Gr.B ASME B36.10 SEAMLESS ( 6 METER LENGTH )", size: "3'", pipClass: "CS1", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "2", name: "PIPE S-80 BE ASTM A106 GRADE B ASME B36.10 SEAMLESS HOT DIPPED GALV  ( 6 METER LENGTH )", size: "5'", pipClass: "CS3", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "3", name: "PIPE S-80 NPT ASTM A106 GRADE B ASME B36.10 SEAMLESS HOT DIPPED GALV  ( 6 METER LENGTH )", size: "8'", pipClass: "CS2", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "4", name: "45 EL LR S-40 BW A234 WPB ASME B16.9", size: "1'", pipClass: "CS1", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "5", name: "CON.REDUCER S-40 BW A234 WPB ASME B16.9", size: "4'", pipClass: "CS4", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "6", name: "TEE REDUCER S-40xS-40 BW A234 WPB ASME B16.9", size: "2'", pipClass: "CS1", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "7", name: "SWAGED NIPPLE CS ASTM A105  # 3000 NPT ", size: "3'", pipClass: "CS1", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "8", name: "REDUCING COUPLING CS GALV ASTM A105 ANSI B16.11  # 3000 NPT ", size: "9'", pipClass: "CS6", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "9", name: "UNION CS, ASTM A105, GALV,F-F # 3000 NPT", size: "15'", pipClass: "CS1", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "10", name: "BLIND FLANGE ASTM A105 ASME-CL150 RF PER ASME B16.5", size: "3'", pipClass: "CS5", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "11", name: "BALL VALVE FULL BORE,ASME B16.10,BODY ASTM A216WCB,TRIM SS316+PTFE  FLG END ASME B16.5 150# RF", size: "6'", pipClass: "CS7", unit: "pcs", tabs: [], price: 100, laborCost: 35 },
//        { id: "12", name: "STUD BOLT A193 GR.B7 WITH 2 HEXNUTS A194 Gr.2H ", size: "8'", pipClass: "CS8", unit: "pcs", tabs: [], price: 100, laborCost: 35 }
//    ];


    $scope.createNewItem = function () {
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
        $http.post("bomService.asmx/addNewItem", { "_id": item._id, "Description": item.Description, "Spec": item.Spec, "Origin": item.Origin, "Category": item.Category, "Unit": item.Unit, "UnitLabor": item.UnitLabor, "UnitMaterial": item.UnitMaterial, "TimeModified": item.TimeModified })
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
    $scope.deleteThisItem = function (index, itemIDString) {
        $scope.items.splice(index, 1);
        $http.post("bomService.asmx/deleteItemByID", { "_id": itemIDString }).then(function () {
          console.log("Item Deleted");
        });
    };
    // functions
    // when "save button" is clicked
    $scope.updateThisItem = function (item) {
      $http.post("bomService.asmx/updateItemByID", { "_id": item._id, "Description": item.Description, "Spec": item.Spec, "Origin": item.Origin, "Category": item.Category, "Unit": item.Unit, "UnitLabor": item.UnitLabor, "UnitMaterial": item.UnitMaterial, "TimeModified": item.TimeModified })
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

    $scope.exportToPDF = function () {
      // view to export is
      // div id="checkOutView"
      var doc = new jsPDF();
      var elementHandler = {
        '#ignorePDF': function (element, renderer) {
          return true;
        }
      };
      var source = jQuery("#checkOutView");
      doc.fromHTML(
          source,
          15,
          15,
          {
            'width': 180,'elementHandlers': elementHandler
          });

      doc.output("dataurlnewwindow");

    };



}]);
