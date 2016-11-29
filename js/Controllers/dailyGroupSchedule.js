angular.module('myApp.controllers')
    .controller('DailyGroupScheduleController', ['$scope', '$http', function($scope, $http) {

        $scope.date = new Date(2015, 9-1, 1);

        // show schedule
        this.show = false;
        this.group = null;
        this.close = function() {
            this.show = false;
        };

        // show loading
        this.loading = false;

		this.load = function() {
			if (this.group)
			{
				this.loadSchedule(this.group);
			}
		}


        this.loadSchedule = function(group) {
        	
        	this.group = group;

            this.loading = true;

            var dpDate = $('#scheduleDate').val();
            var parts = dpDate.split('.');

            $scope.date = dpDate;

            var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0];

            var responsePromise =
                $http.get("http://wiki.nayanova.edu/api.php?action=dailySchedule" +
                "&date=" + formattedDate +
                "&groupIds=" + group.StudentGroupId);

            var controller = this;

            responsePromise.success(function(data, status, headers, config) {

                for (var i = 0; i < data[0].Lessons.length; i++)
                {
                    data[0].Lessons[i]["Time"] = data[0].Lessons[i]["Time"].substring(0,5);
                }

                controller.studentGroupName = data[0].studentGroupName;
                controller.studentGroupId = data[0].studentGroupId;
                controller.Lessons = data[0].Lessons;
                controller.date = $scope.date;
                controller.show = true;

                controller.loading = false;

                $('*').animate({ scrollTop: 0 }, 'slow', 'swing');
            });
            responsePromise.error(function(data, status, headers, config) {
                alert("Connection failed!");
            });
        };
    }]);

