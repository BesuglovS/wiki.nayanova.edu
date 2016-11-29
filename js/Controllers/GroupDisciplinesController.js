angular.module('myApp.controllers')
    .controller('GroupDisciplinesController', ['$scope', '$http', function($scope, $http) {

        // show schedule
        this.show = false;
        this.close = function() {
            this.show = false;
        };

        // show loading
        this.loading = false;

        this.loadSchedule = function(group) {

            this.loading = true;

            var responsePromise =
                $http.get("http://wiki.nayanova.edu/api.php?action=list&listtype=groupDisciplines" +
                "&groupId=" + group.StudentGroupId);

            var controller = this;

            responsePromise.success(function(data, status, headers, config) {

                for (var i = 0; i < data.length; i++) {
                    data[i].Attestation = Attestation[data[i].Attestation];
                }

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

