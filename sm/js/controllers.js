angular.module('starter.controllers', [])

.controller('groupScheduleCtrl', ['$scope', '$http', function ($scope, $http) {

    var dbPrefix = "s_"; // "" : "s_"

    $scope.updateSchedule = function () {        

        if ($scope.data.ScheduleWeek == 'undefined') {
            $scope.data.ScheduleWeek = 1;
        }

        $scope.dowAbbr = ["", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"];
        
        var week = $scope.data.ScheduleWeek;        
        var groupId = $scope.data.SelectedGroup.StudentGroupId;

        var weekScheduleUrl = 'http://wiki.nayanova.edu/api.php?action=weekSchedule&groupId=' + groupId + '&week=' + week + '&dbPrefix=' + dbPrefix;
        $http.get(weekScheduleUrl).
            then(function (response) {
                var result = new Array();

                for (i = 1; i <= 7; i++) {
                    result[i] = new Array();
                    result[i].Lessons = new Array();
                    result[i].doww = $scope.dowAbbr[i] + ' - ' + week;
                }

                if (response.data.length == 0) {
                    result.empty = true;
                }
                else {
                    result.empty = false;
                    response.data.forEach(function (element, index) {
                        var dateParts = element.date.split("-");
                        element.Date = dateParts[2] + '.' + dateParts[1] + '.' + dateParts[0];
                        element.Time = element.Time.substring(0, 5);

                        result[element.dow].date = element.Date;

                        result[element.dow].Lessons.push(element);
                    });                    
                }

                $scope.Schedule = result;
            }, function (response) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            }
        );
    }    
    

    // groupScheduleCtrl
    function getMonday(d) {
        d = new Date(d);
        var day = d.getDay(),
            diff = d.getDate() - day + (day == 0 ? -6 : 1); // adjust when day is sunday
        return new Date(d.setDate(diff));
    }


    $http.get('http://wiki.nayanova.edu/api.php?action=list&listtype=configOptions' + '&dbPrefix=' + dbPrefix).
        then(function (response) {
            var data = response.data;
            response.data.forEach(function (element, index) {
                if (element.Key == "Semester Starts")
                {
                    var ss = element.Value.split('-');

                    var ssDate = getMonday(new Date(ss[0], ss[1] - 1, ss[2]));
                    var now = new Date();

                    var oneDay = 24 * 60 * 60 * 1000;

                    var diffDays = Math.ceil(Math.abs((now.getTime() - ssDate.getTime()) / (oneDay)));

                    var week = Math.floor(diffDays / 7) + 1;

                    $scope.data.ScheduleWeek = week;
                }
            });

            $scope.updateSchedule();
        }, function (response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        }
    );

    $http.get('http://wiki.nayanova.edu/api.php?action=list&listtype=mainStudentGroups' + '&dbPrefix=' + dbPrefix).
        then(function (response) {
            if ($scope.data.ScheduleWeek == undefined) {
                $scope.data.ScheduleWeek = 1;
            }

            $scope.GroupList = response.data;
            if ($scope.GroupList.length > 0) {
                $scope.data.ScheduleDate = new Date();
                $scope.data.SelectedGroup = $scope.GroupList[0];
            }

            $scope.updateSchedule();
        }, function (response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        }
    );

    $scope.data = {};

    $scope.dowCount = 7;
    $scope.getNumber = function (num) {
        return new Array(num);
    }
    // groupScheduleCtrl    
}])

.controller('teacherScheduleCtrl', ['$scope', '$http', function ($scope, $http) {

    var dbPrefix = "s_"; // "" : "s_"

    $scope.updateSchedule = function () {
        if ($scope.data.ScheduleWeek == 'undefined') {
            $scope.data.ScheduleWeek = 1;
        }

        $scope.dowAbbr = ["", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"];

        var week = $scope.data.ScheduleWeek;
        var teacherId = $scope.data.SelectedTeacher.TeacherId;

        var weekScheduleUrl = 'http://wiki.nayanova.edu/api.php?action=TeacherWeekSchedule&teacherId=' + teacherId + '&week=' + week + '&dbPrefix=' + dbPrefix;

        $http.get(weekScheduleUrl).
            then(function (response) {
                var result = new Array();

                result.FIO = $scope.data.SelectedTeacher.FIO;

                for (i = 1; i <= 7; i++) {
                    result[i] = new Array();
                    result[i].Lessons = new Array();
                    result[i].doww = $scope.dowAbbr[i] + ' - ' + week;                    
                }

                if (response.data.length == 0) {
                    result.empty = true;
                }
                else {
                    result.empty = false;
                    response.data.forEach(function (element, index) {
                        var dateParts = element.Date.split("-");
                        element.Date = dateParts[2] + '.' + dateParts[1] + '.' + dateParts[0];
                        element.Time = element.Time.substring(0, 5);

                        result[element.dow].date = element.Date;

                        result[element.dow].Lessons.push(element);
                    });
                }
                $scope.Schedule = result;
            }, function (response) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            }
        );
    }

    // teacherScheduleCtrl
    function getMonday(d) {
        d = new Date(d);
        var day = d.getDay(),
            diff = d.getDate() - day + (day == 0 ? -6 : 1); // adjust when day is sunday
        return new Date(d.setDate(diff));
    }

    $http.get('http://wiki.nayanova.edu/api.php?action=list&listtype=configOptions' + '&dbPrefix=' + dbPrefix).
        then(function (response) {
            var data = response.data;
            response.data.forEach(function (element, index) {
                if (element.Key == "Semester Starts") {
                    var ss = element.Value.split('-');

                    var ssDate = getMonday(new Date(ss[0], ss[1] - 1, ss[2]));
                    var now = new Date();

                    var oneDay = 24 * 60 * 60 * 1000;

                    var diffDays = Math.ceil(Math.abs((now.getTime() - ssDate.getTime()) / (oneDay)));

                    var week = Math.floor(diffDays / 7) + 1;

                    $scope.data.ScheduleWeek = week;
                }
            });

            $scope.updateSchedule();
        }, function (response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        }
    );

    $http.get('http://wiki.nayanova.edu/api.php?action=list&listtype=teachers' + '&dbPrefix=' + dbPrefix).
        then(function (response) {
            if ($scope.data.ScheduleWeek == undefined) {
                $scope.data.ScheduleWeek = 1;
            }

            $scope.TeacherList = response.data;
            if ($scope.TeacherList.length > 0) {
                $scope.data.SelectedTeacher = $scope.TeacherList[0];
            }

            $scope.updateSchedule();
        }, function (response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        }
    );

    $scope.data = {};    

    $scope.dowCount = 7;
    $scope.getNumber = function (num) {
        return new Array(num);
    }
    // teacherScheduleCtrl
}]);
