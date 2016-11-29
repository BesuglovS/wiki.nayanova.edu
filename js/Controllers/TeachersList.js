angular.module('myApp.controllers')
    .controller('TeachersListController', ['$http', function($http) {

        var responsePromise = $http.get("http://wiki.nayanova.edu/api.php?action=list&listtype=teachers");

        var controller = this;

        responsePromise.success(function(data, status, headers, config) {
            controller.list = data;
        });
        responsePromise.error(function(data, status, headers, config) {
            alert("Connection failed!");
        });
    }]);