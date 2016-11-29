angular.module('myApp.controllers')
.controller('WeekScheduleController', ['$scope', '$http', function($scope, $http) {
	
	this.load = function() {
		if (this.group !== null)
		{
			this.loadSchedule(this.group);
		}
	};
	
	this.loadSchedule = function(group) {
		
		this.group = group;

        this.loading = true;
        // show schedule
        this.show = false;
        this.close = function() {
            this.show = false;
        };

        var week = $scope.selectedWeek;
        
        var responsePromise =
            $http.get("http://wiki.nayanova.edu/api.php?action=weekSchedule" +
            "&week=" + week +
            "&groupId=" + group.StudentGroupId);

        var controller = this;

        responsePromise.success(function(data, status, headers, config) {
        	
        	var dowNames = ["","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота","Воскресенье"];
        	
        	var result = new Array();
        	for (var i = 1; i <= 7; i++)
        	{
        		result[i] = new Array();
        		result[i]["Count"] = 0;
        		result[i]["Lessons"] = new Array();
        		result[i]["dowName"] = dowNames[i];        		
        	}

            for (var i = 0; i < data.length; i++)
            {
                data[i]["Time"] = data[i]["Time"].substring(0,5);
                
                var dateSplit = data[i]["date"].split("-");
                data[i]["niceDate"] = dateSplit[2] + "." + dateSplit[1] + "." + dateSplit[0];
                
                result[data[i]["dow"]]["date"] = data[i]["niceDate"];
                                
                result[data[i]["dow"]]["Lessons"].push(data[i]);
                
                result[data[i]["dow"]]["Count"] = result[data[i]["dow"]]["Count"] + 1;
            }
            
            console.log(result);
            
            controller.result = result;

            //controller.Lessons = data;            
            controller.show = true;

            controller.loading = false;

            $('*').animate({ scrollTop: 0 }, 'slow', 'swing');
        });
        responsePromise.error(function(data, status, headers, config) {
            alert("Connection failed!");
        });
    };
	
	var controller = this;
        
    controller.weeks = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18];
    
    var responsePromise =
            $http.get("http://wiki.nayanova.edu/api.php?action=list&listtype=configOptions");
            
    responsePromise.success(function(data, status, headers, config) {    	
    	for (var i=0; i < data.length; i++) {
		  if (data[i]["Key"] == "Semester Starts")
		  {
		  	var ss = data[i]["Value"].split("-");
		  	
		  	var ssDate = new Date(ss[0], ss[1]-1, ss[2]);		  	
		  	var today = new Date();
		  	
		  	var one_day=1000*60*60*24;
		  	var diff = today.getTime() - ssDate.getTime();
		  	
		  	var diffInDays = Math.floor(diff/one_day);
		  	
		  	var curWeek = Math.floor(diffInDays / 7) + 1;
		  			  	
		  	$scope.selectedWeek = curWeek;
		  }
		};    				
    });
    
    
}]);
