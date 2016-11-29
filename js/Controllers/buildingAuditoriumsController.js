angular.module('myApp.controllers')

.controller('buildingAuditoriumsController', ['$scope', '$http', '$sce' , function($scope, $http, $sce) {

	$scope.date = new Date(2015, 9-1, 1);

    // show schedule
    this.show = false;
    this.close = function() {
        this.show = false;
    };

    // show loading
    this.loading = false;

    this.loadSchedule = function(buildingId) {

        this.loading = true;
        
        var dpDate = $("#buildingDate").val();
        var parts = dpDate.split('.');

        $scope.date = dpDate;

        var formattedDate = "\"" + parts[2] + '-' + parts[1] + '-' + parts[0] + "\"";

        var responsePromise =
            $http.get("http://wiki.nayanova.edu/_php/includes/Auditoriums.php" +            
            "?building=" + buildingId +
            "&date=" + formattedDate);

        var controller = this;

        responsePromise.success(function(data, status, headers, config) {

			$scope.result = $sce.trustAsHtml(data);
            
            controller.show = true;

            controller.loading = false;

            $('*').animate({ scrollTop: 0 }, 'slow', 'swing');
        });
        responsePromise.error(function(data, status, headers, config) {
            alert("Connection failed!");
        });
    };
}]);

