angular.module('myApp.controllers')
    .controller('TeacherScheduleController', ['$scope', '$http', function($scope, $http) {

        // show schedule
        this.show = false;
        this.close = function() {
            this.show = false;
        };

        // show loading
        this.loading = false;

        this.loadSchedule = function(teacher) {

            this.loading = true;
            var controller = this;

            var responsePromise =
                $http.get("http://wiki.nayanova.edu/api.php?action=TeacherSchedule" +
                "&teacherId=" + teacher.TeacherId);


            responsePromise.success(function(data, status, headers, config) {

                var dowArray = ["", "Понедельник","Вторник","Среда","Четверг","Пятница","Суббота","Воскресенье"];
                for (i = 0; i < data.length; i++) {
                    for (j = 0; j < data[i].length; j++) {
                        data[i][j].Lesson.dow = dowArray[data[i][j].Lesson.dow];
                        data[i][j].Lesson.Time = data[i][j].Lesson.Time.substring(0,5);
                        data[i][j].AudWeeks = [];

                        var keys = Object.keys(data[i][j].AuditoriumWeeks);
                        if (keys.length == 1)
                        {
                            data[i][j].AudWeeks.push(keys[0]);
                        }
                        else {
                            for (var k = 0; k < keys.length; k++) {
                                var val = data[i][j].AuditoriumWeeks[keys[k]];
                                data[i][j].AudWeeks.push(val.String + ' - ' + keys[k]);
                            }
                        }
                    }
                }

                console.log(data);

                controller.list = data;
                controller.show = true;

                controller.loading = false;

                $('*').animate({ scrollTop: 0 }, 'slow', 'swing');
            });
            responsePromise.error(function(data, status, headers, config) {
                alert("Connection failed!");
            });
        };
    }]);

