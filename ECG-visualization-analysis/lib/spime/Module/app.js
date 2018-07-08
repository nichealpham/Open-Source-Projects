var app = angular.module("app", []);
app.controller("main", function ($scope, $http) {
    $scope.pageTitle = "Main App";
    $scope.admin = [
        {
            "name": "Pham Khoi Nguyen",
            "title": "admin",
            "shortName": "Nguyen Pham",
            "occupation": "Website Developer"
        }
    ];
    $scope.newNoti = 3;
    $scope.newTask = 2;
    $scope.newMail = 4;
    $scope.currentMailType = "inbox";
    $scope.getThisMailType = function (mailType) {
        if (mailType == "inbox") {
            return $scope.newMails;
        };
        if (mailType == "outbox") {
            return $scope.outMails;
        };
    };
    /// function checktoolbox chua chay dc
    var checkToolBox = function () {    
        if ($scope.newNoti == 0) {
            $("#chip_newNoti").css("display", "none");
        }
        else {
            $("#chip_newNoti").css("display", "block");
        }
    };
    $scope.clickedMail = {
        "title": "",
        "fromAvarta": "",
        "date": "",
        "content": "",
        "from": ""
    };
    // $http.get("SpimeService.asmx/getNewMail").then(function (response) {
    //     $scope.newMails = response.data;
    // });
    // $http.get("SpimeService.asmx/getOutMail").then(function (response) {
    //     $scope.outMails = response.data;
    // })
    $scope.viewThisMail = function (obj) {
        var currObj = obj;
        angular.element("#mail_content_lbcontent").$sce.trustAsHtml(currObj.content);
        anuglra.element("#mail_content_lbfrom").trustAsHtml(currObj.from + " - " + currObj.date);
        anuglra.element("#mail_content_lbtitle").trustAsHtml(currObj.title);
    };
    $scope.clickThisMail = function (object) {
        $scope.clickedMail.content = object.content;
        $scope.clickedMail.fromAvarta = object.fromAvarta;
        $scope.clickedMail.date = object.date;
        $scope.clickedMail.title = object.title;
        $scope.clickedMail.from = object.from;
    };
    $scope.newMails = [
        {
            "title": "Spime Version 2.0 announcement",
            "from": "Nguyen Pham",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/nguyen.png",
            "to": "Nguyen Pham",
            "date": "14/12/2015",
            "content": "Dear our treasured candidates,<br/>Spime Core Team is currently developing the second module of the website. We hope that we could make it in time in April 24th.<br/>If you find our project interesting please help us improve the products by participating in.<br/>Here are some requirements:<br/>     1.    Good at working independently<br/>     2.    Love Angular js and Css technique<br/>     3.    Willing to work without MONEY<br/>If you still want to help us accomplish our goal in spite of the hardship, we would be very pround to have you side by side as a core team member.<br/>Thank you very much for reading this letter. Spime team wishes you a new nice day of work.<br/>Khoi Nguyen"
        },
        {
            "title": "Academic revise 12/2015",
            "from": "Nguyen Anh",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/anh2.png",
            "to": "Nguyen Pham",
            "date": "11/12/2015",
            "content": "Dear Nguyen,<br/>Please send me the revised version of your academic transcript. I need it for contacting your university.<br/>Thank you,<br/>Nguyen Anh"
        },
        {
            "title": "Military Education",
            "from": "Kieu Khanh",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/phuong2.png",
            "to": "Nguyen Pham",
            "date": "8/12/2015",
            "content": "Hi Nguyen,<br/>Miss Hien announce that we should go top the Student OSA room to take our certification for completing military education.<br/>Best,<br/>Khanh"
        },
        {
            "title": "Congratz on winning the Startup event",
            "from": "Huy Pham",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/huy2.png",
            "to": "Nguyen Pham",
            "date": "1/12/2015",
            "content": "We win it !<br/>Will yopu consider celebrating? This time we gonna invite Miss An and Mr Trung for sure.<br/>Huy"
        },
        {
            "title": "About Research 3A project",
            "from": "Trung Le",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/nguyenminhthanh.png",
            "to": "Nguyen Pham",
            "date": "28/11/2015",
            "content": "Dear Nguyen,<br/>I would like you to complete the student research log for the Research 3A Project. Please work on it untill September 28th so that we could have time to revise.<br/>Best,<br/>"
        },
        {
            "title": "Business Plan revised",
            "from": "Thinh Nguyen",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/nguyenphihung.png",
            "to": "Nguyen Pham",
            "date": "13/12/2015",
            "content": ""
        },
        {
            "title": "Pictures of the event",
            "from": "Thu Pham",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/thu2.png",
            "to": "Nguyen Pham",
            "date": "14/11/2015",
            "content": ""
        },
        {
            "title": "Group Work on Monday",
            "from": "Nguyen Pham",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/trung2.png",
            "to": "Nguyen Pham",
            "date": "11/12/2015",
            "content": ""
        }
    ];
    $scope.outMails = [
        {
            "title": "Spime Version 2.0 announcement",
            "from": "Nguyen Pham",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/nguyen.png",
            "to": "Nguyen Pham",
            "date": "14/12/2015",
            "content": "Dear our treasured candidates,<br/>Spime Core Team is currently developing the second module of the website. We hope that we could make it in time in April 24th.<br/>If you find our project interesting please help us improve the products by participating in.<br/>Here are some requirements:<br/>     1.    Good at working independently<br/>     2.    Love Angular js and Css technique<br/>     3.    Willing to work without MONEY<br/>If you still want to help us accomplish our goal in spite of the hardship, we would be very pround to have you side by side as a core team member.<br/>Thank you very much for reading this letter. Spime team wishes you a new nice day of work.<br/>Khoi Nguyen"
        },
        {
            "title": "Amato Gozila",
            "from": "Nguyen Pham",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/phuong2.png",
            "to": "Nguyen Pham",
            "date": "11/12/2015",
            "content": "Dear Nguyen,<br/>Please send me the revised version of your academic transcript. I need it for contacting your university.<br/>Thank you,<br/>Nguyen Anh"
        },
        {
            "title": "Children of The North",
            "from": "Kieu Khanh",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/anh2.png",
            "to": "Nguyen Pham",
            "date": "8/12/2015",
            "content": "Hi Nguyen,<br/>Miss Hien announce that we should go top the Student OSA room to take our certification for completing military education.<br/>Best,<br/>Khanh"
        },
        {
            "title": "Hulu Hula",
            "from": "Huy Pham",
            "fromID": "BEBEIU13051",
            "fromAvarta": "user/avarta/trung2.png",
            "to": "Nguyen Pham",
            "date": "1/12/2015",
            "content": "We win it !<br/>Will yopu consider celebrating? This time we gonna invite Miss An and Mr Trung for sure.<br/>Huy"
        }
    ];
    $scope.getLength = function (array) {
        return array.length;
    };
    $scope.TestHtml = "Hello World<br/>How are you today?";
    $scope.bindInbox = function () {
        $scope.currentMailType = "inbox";
    };
    $scope.bindOutbox = function () {
        $scope.currentMailType = "outbox";
    };
});
app.filter("sanitize", ['$sce', function ($sce) {
    return function (htmlCode) {
        return $sce.trustAsHtml(htmlCode);
    };
}]);