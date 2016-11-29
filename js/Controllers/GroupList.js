angular.module('myApp.controllers')
    .controller('GroupListController', ['$http', function($http) {

    var responsePromise = $http.get("http://wiki.nayanova.edu/api.php?action=list&listtype=mainStudentGroups");

    var controller = this;

    responsePromise.success(function(data, status, headers, config) {
        controller.list = data;
    });
    responsePromise.error(function(data, status, headers, config) {
        alert("Connection failed!");
    });
}]);
