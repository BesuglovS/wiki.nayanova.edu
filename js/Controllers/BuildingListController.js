angular.module('myApp.controllers')
    .controller('BuildingListController', ['$http', function($http) {

    var responsePromise = $http.get("http://wiki.nayanova.edu/api.php?action=list&listtype=buildings");

    var controller = this;

    responsePromise.success(function(data, status, headers, config) {
    	console.log(data);
        controller.list = data;
    });
    responsePromise.error(function(data, status, headers, config) {
        alert("Connection failed!");
    });
}]);
