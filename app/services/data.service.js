System.register(["@angular/core", "@angular/http", 'rxjs/Rx'], function(exports_1, context_1) {
    "use strict";
    var __moduleName = context_1 && context_1.id;
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, http_1;
    var ScheduleDataService;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (http_1_1) {
                http_1 = http_1_1;
            },
            function (_1) {}],
        execute: function() {
            ScheduleDataService = (function () {
                function ScheduleDataService(_http) {
                    this._http = _http;
                }
                ScheduleDataService.prototype.getMainStudentGroups = function () {
                    return this._http
                        .get('http://wiki.nayanova.edu/api.php?action=list&listtype=mainStudentGroups')
                        .map(function (res) { return res.json(); });
                };
                ScheduleDataService.prototype.getGroupExams = function (groupId) {
                    return this._http
                        .get('http://wiki.nayanova.edu/api.php?action=groupExams&groupId=' + groupId)
                        .map(function (res) { return res.json(); });
                };
                ScheduleDataService = __decorate([
                    core_1.Injectable(), 
                    __metadata('design:paramtypes', [http_1.Http])
                ], ScheduleDataService);
                return ScheduleDataService;
            }());
            exports_1("ScheduleDataService", ScheduleDataService);
        }
    }
});

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNlcnZpY2VzL2RhdGEuc2VydmljZS50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7WUFNQTtnQkFDSSw2QkFBb0IsS0FBVztvQkFBWCxVQUFLLEdBQUwsS0FBSyxDQUFNO2dCQUFHLENBQUM7Z0JBRW5DLGtEQUFvQixHQUFwQjtvQkFDSSxNQUFNLENBQUMsSUFBSSxDQUFDLEtBQUs7eUJBQ1osR0FBRyxDQUFDLHlFQUF5RSxDQUFDO3lCQUM5RSxHQUFHLENBQUMsVUFBQSxHQUFHLElBQUksT0FBQSxHQUFHLENBQUMsSUFBSSxFQUFFLEVBQVYsQ0FBVSxDQUFDLENBQUM7Z0JBQ2hDLENBQUM7Z0JBRUQsMkNBQWEsR0FBYixVQUFjLE9BQWU7b0JBQ3pCLE1BQU0sQ0FBQyxJQUFJLENBQUMsS0FBSzt5QkFDWixHQUFHLENBQUMsNkRBQTZELEdBQUcsT0FBTyxDQUFDO3lCQUM1RSxHQUFHLENBQUMsVUFBQSxHQUFHLElBQUksT0FBQSxHQUFHLENBQUMsSUFBSSxFQUFFLEVBQVYsQ0FBVSxDQUFDLENBQUM7Z0JBQ2hDLENBQUM7Z0JBZEw7b0JBQUMsaUJBQVUsRUFBRTs7dUNBQUE7Z0JBZWIsMEJBQUM7WUFBRCxDQWRBLEFBY0MsSUFBQTtZQWRELHFEQWNDLENBQUEiLCJmaWxlIjoic2VydmljZXMvZGF0YS5zZXJ2aWNlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHtJbmplY3RhYmxlfSBmcm9tIFwiQGFuZ3VsYXIvY29yZVwiO1xyXG5pbXBvcnQge0h0dHB9IGZyb20gXCJAYW5ndWxhci9odHRwXCI7XHJcbmltcG9ydCB7T2JzZXJ2YWJsZX0gZnJvbSBcInJ4anMvUnhcIjtcclxuaW1wb3J0ICdyeGpzL1J4JztcclxuXHJcbkBJbmplY3RhYmxlKClcclxuZXhwb3J0IGNsYXNzIFNjaGVkdWxlRGF0YVNlcnZpY2Uge1xyXG4gICAgY29uc3RydWN0b3IocHJpdmF0ZSBfaHR0cDogSHR0cCkge31cclxuXHJcbiAgICBnZXRNYWluU3R1ZGVudEdyb3VwcygpOiBPYnNlcnZhYmxlPGFueT4geyAgICAgICAgXHJcbiAgICAgICAgcmV0dXJuIHRoaXMuX2h0dHBcclxuICAgICAgICAgICAgLmdldCgnaHR0cDovL3dpa2kubmF5YW5vdmEuZWR1L2FwaS5waHA/YWN0aW9uPWxpc3QmbGlzdHR5cGU9bWFpblN0dWRlbnRHcm91cHMnKVxyXG4gICAgICAgICAgICAubWFwKHJlcyA9PiByZXMuanNvbigpKTtcclxuICAgIH1cclxuXHJcbiAgICBnZXRHcm91cEV4YW1zKGdyb3VwSWQ6IHN0cmluZyk6T2JzZXJ2YWJsZTxhbnk+IHtcclxuICAgICAgICByZXR1cm4gdGhpcy5faHR0cFxyXG4gICAgICAgICAgICAuZ2V0KCdodHRwOi8vd2lraS5uYXlhbm92YS5lZHUvYXBpLnBocD9hY3Rpb249Z3JvdXBFeGFtcyZncm91cElkPScgKyBncm91cElkKVxyXG4gICAgICAgICAgICAubWFwKHJlcyA9PiByZXMuanNvbigpKTtcclxuICAgIH1cclxufSJdLCJzb3VyY2VSb290IjoiL3NvdXJjZS8ifQ==
