System.register(["@angular/core", "../services/data.service", "./KeyValuePairsPipe"], function(exports_1, context_1) {
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
    var core_1, data_service_1, KeyValuePairsPipe_1;
    var Cmp1Component;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (data_service_1_1) {
                data_service_1 = data_service_1_1;
            },
            function (KeyValuePairsPipe_1_1) {
                KeyValuePairsPipe_1 = KeyValuePairsPipe_1_1;
            }],
        execute: function() {
            Cmp1Component = (function () {
                function Cmp1Component(_httpService) {
                    this._httpService = _httpService;
                }
                Cmp1Component.prototype.ngOnInit = function () {
                    this.getMainGroups();
                };
                Cmp1Component.prototype.getMainGroups = function () {
                    var _this = this;
                    this._httpService.getMainStudentGroups()
                        .subscribe(function (response) { return _this.studentGroups = response; }, function (error) { return console.log(error); });
                };
                Cmp1Component.prototype.getGroupExams = function (groupId) {
                    var _this = this;
                    this._httpService.getGroupExams(groupId)
                        .subscribe(function (response) { return _this.groupsExamsResponse = response; }, function (error) { return console.log(error); });
                };
                Cmp1Component = __decorate([
                    core_1.Component({
                        selector: 'cmp1',
                        pipes: [KeyValuePairsPipe_1.KeyValuePairsPipe],
                        template: "\n<div class=\"testBlock\">\n    <h2>\u0412\u044B\u0431\u0435\u0440\u0438 \u0433\u0440\u0443\u043F\u043F\u0443 \u0434\u043B\u044F \u043F\u0440\u043E\u0441\u043C\u043E\u0442\u0440\u0430 \u0440\u0430\u0441\u043F\u0438\u0441\u0430\u043D\u0438\u044F \u0441\u0435\u0441\u0441\u0438\u0438</h2>\n    <!--<button (click)=\"getMainGroups()\">Get Groups</button>    -->\n    <!--<p>Response: </p>-->\n    <ul id=\"groupList\">\n        <li *ngFor=\"let group of studentGroups\">\n            <a href=\"#\" (click)=\"getGroupExams(group.StudentGroupId)\">{{group.Name}}</a>\n        </li>\n    </ul>\n</div>\n\n<div class=\"testBlock\">\n    <h2>\u042D\u043A\u0437\u0430\u043C\u0435\u043D\u044B</h2>\n    <!--<input type=\"text\" id=\"groupId\" #groupId>-->\n    <!--<button (click)=\"getGroupExams(groupId.value)\">Get Group Exams</button>   -->\n    <!--<p>Response: </p>-->\n    <div *ngFor=\"let groupExams of groupsExamsResponse | kvp\">        \n        <div *ngFor=\"let exam of groupExams.value.Exams\">\n            <h2>{{exam.DisciplineName}}</h2>\n            <table class=\"examsTable\">\n                <tr>                    \n                    <td colspan=\"2\">{{exam.StudentGroupName}}</td>\n                </tr>\n                <tr>\n                    <td colspan=\"2\">{{exam.TeacherFIO}}</td>\n                </tr>\n                <tr>\n                    <td colspan=\"2\">\u041A\u043E\u043Dc\u0443\u043B\u044C\u0442\u0430\u0446\u0438\u044F</td>\n                </tr>\n                <tr>\n                    <td>{{exam.ConsultationDateTime}}</td>\n                    <td>{{exam.ConsultationAuditoriumName}}</td>\n                </tr>\n                <tr>\n                    <td colspan=\"2\">\u042D\u043A\u0437\u0430\u043C\u0435\u043D</td>\n                </tr>\n                <tr>\n                    <td>{{exam.ExamDateTime}}</td>                \n                    <td>{{exam.ExamAuditoriumName}}</td>\n                </tr>\n            </table>\n        </div>        \n    </div>\n</div>\n\n\n",
                        providers: [data_service_1.ScheduleDataService]
                    }), 
                    __metadata('design:paramtypes', [data_service_1.ScheduleDataService])
                ], Cmp1Component);
                return Cmp1Component;
            }());
            exports_1("Cmp1Component", Cmp1Component);
        }
    }
});

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRlc3QvY21wMS5jb21wb25lbnQudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7WUEwREE7Z0JBR0ksdUJBQW9CLFlBQWlDO29CQUFqQyxpQkFBWSxHQUFaLFlBQVksQ0FBcUI7Z0JBQUcsQ0FBQztnQkFHekQsZ0NBQVEsR0FBUjtvQkFDSSxJQUFJLENBQUMsYUFBYSxFQUFFLENBQUM7Z0JBQ3pCLENBQUM7Z0JBRUQscUNBQWEsR0FBYjtvQkFBQSxpQkFNQztvQkFMRyxJQUFJLENBQUMsWUFBWSxDQUFDLG9CQUFvQixFQUFFO3lCQUNuQyxTQUFTLENBQ04sVUFBQSxRQUFRLElBQUksT0FBQSxLQUFJLENBQUMsYUFBYSxHQUFHLFFBQVEsRUFBN0IsQ0FBNkIsRUFDekMsVUFBQSxLQUFLLElBQUksT0FBQSxPQUFPLENBQUMsR0FBRyxDQUFDLEtBQUssQ0FBQyxFQUFsQixDQUFrQixDQUM5QixDQUFDO2dCQUNWLENBQUM7Z0JBRUQscUNBQWEsR0FBYixVQUFjLE9BQWU7b0JBQTdCLGlCQU1DO29CQUxHLElBQUksQ0FBQyxZQUFZLENBQUMsYUFBYSxDQUFDLE9BQU8sQ0FBQzt5QkFDbkMsU0FBUyxDQUNOLFVBQUEsUUFBUSxJQUFJLE9BQUEsS0FBSSxDQUFDLG1CQUFtQixHQUFHLFFBQVEsRUFBbkMsQ0FBbUMsRUFDL0MsVUFBQSxLQUFLLElBQUksT0FBQSxPQUFPLENBQUMsR0FBRyxDQUFDLEtBQUssQ0FBQyxFQUFsQixDQUFrQixDQUM5QixDQUFDO2dCQUNWLENBQUM7Z0JBN0VMO29CQUFDLGdCQUFTLENBQUM7d0JBQ1AsUUFBUSxFQUFFLE1BQU07d0JBQ2hCLEtBQUssRUFBRSxDQUFDLHFDQUFpQixDQUFDO3dCQUMxQixRQUFRLEVBQUUscS9EQStDYjt3QkFDRyxTQUFTLEVBQUUsQ0FBQyxrQ0FBbUIsQ0FBQztxQkFDbkMsQ0FBQzs7aUNBQUE7Z0JBMEJGLG9CQUFDO1lBQUQsQ0F6QkEsQUF5QkMsSUFBQTtZQXpCRCx5Q0F5QkMsQ0FBQSIsImZpbGUiOiJ0ZXN0L2NtcDEuY29tcG9uZW50LmpzIiwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHtDb21wb25lbnQsIE9uSW5pdH0gZnJvbSBcIkBhbmd1bGFyL2NvcmVcIjtcclxuaW1wb3J0IHtTY2hlZHVsZURhdGFTZXJ2aWNlfSBmcm9tIFwiLi4vc2VydmljZXMvZGF0YS5zZXJ2aWNlXCI7XHJcbmltcG9ydCB7U3R1ZGVudEdyb3VwfSBmcm9tIFwiLi4vRG9tYWluVHlwZXMvTWFpbi9TdHVkZW50R3JvdXBcIjtcclxuaW1wb3J0IHtFeGFtfSBmcm9tIFwiLi4vRG9tYWluVHlwZXMvTWFpbi9FeGFtXCI7XHJcbmltcG9ydCB7S2V5VmFsdWVQYWlyc1BpcGV9IGZyb20gXCIuL0tleVZhbHVlUGFpcnNQaXBlXCI7XHJcbkBDb21wb25lbnQoe1xyXG4gICAgc2VsZWN0b3I6ICdjbXAxJyxcclxuICAgIHBpcGVzOiBbS2V5VmFsdWVQYWlyc1BpcGVdLFxyXG4gICAgdGVtcGxhdGU6IGBcclxuPGRpdiBjbGFzcz1cInRlc3RCbG9ja1wiPlxyXG4gICAgPGgyPtCS0YvQsdC10YDQuCDQs9GA0YPQv9C/0YMg0LTQu9GPINC/0YDQvtGB0LzQvtGC0YDQsCDRgNCw0YHQv9C40YHQsNC90LjRjyDRgdC10YHRgdC40Lg8L2gyPlxyXG4gICAgPCEtLTxidXR0b24gKGNsaWNrKT1cImdldE1haW5Hcm91cHMoKVwiPkdldCBHcm91cHM8L2J1dHRvbj4gICAgLS0+XHJcbiAgICA8IS0tPHA+UmVzcG9uc2U6IDwvcD4tLT5cclxuICAgIDx1bCBpZD1cImdyb3VwTGlzdFwiPlxyXG4gICAgICAgIDxsaSAqbmdGb3I9XCJsZXQgZ3JvdXAgb2Ygc3R1ZGVudEdyb3Vwc1wiPlxyXG4gICAgICAgICAgICA8YSBocmVmPVwiI1wiIChjbGljayk9XCJnZXRHcm91cEV4YW1zKGdyb3VwLlN0dWRlbnRHcm91cElkKVwiPnt7Z3JvdXAuTmFtZX19PC9hPlxyXG4gICAgICAgIDwvbGk+XHJcbiAgICA8L3VsPlxyXG48L2Rpdj5cclxuXHJcbjxkaXYgY2xhc3M9XCJ0ZXN0QmxvY2tcIj5cclxuICAgIDxoMj7QrdC60LfQsNC80LXQvdGLPC9oMj5cclxuICAgIDwhLS08aW5wdXQgdHlwZT1cInRleHRcIiBpZD1cImdyb3VwSWRcIiAjZ3JvdXBJZD4tLT5cclxuICAgIDwhLS08YnV0dG9uIChjbGljayk9XCJnZXRHcm91cEV4YW1zKGdyb3VwSWQudmFsdWUpXCI+R2V0IEdyb3VwIEV4YW1zPC9idXR0b24+ICAgLS0+XHJcbiAgICA8IS0tPHA+UmVzcG9uc2U6IDwvcD4tLT5cclxuICAgIDxkaXYgKm5nRm9yPVwibGV0IGdyb3VwRXhhbXMgb2YgZ3JvdXBzRXhhbXNSZXNwb25zZSB8IGt2cFwiPiAgICAgICAgXHJcbiAgICAgICAgPGRpdiAqbmdGb3I9XCJsZXQgZXhhbSBvZiBncm91cEV4YW1zLnZhbHVlLkV4YW1zXCI+XHJcbiAgICAgICAgICAgIDxoMj57e2V4YW0uRGlzY2lwbGluZU5hbWV9fTwvaDI+XHJcbiAgICAgICAgICAgIDx0YWJsZSBjbGFzcz1cImV4YW1zVGFibGVcIj5cclxuICAgICAgICAgICAgICAgIDx0cj4gICAgICAgICAgICAgICAgICAgIFxyXG4gICAgICAgICAgICAgICAgICAgIDx0ZCBjb2xzcGFuPVwiMlwiPnt7ZXhhbS5TdHVkZW50R3JvdXBOYW1lfX08L3RkPlxyXG4gICAgICAgICAgICAgICAgPC90cj5cclxuICAgICAgICAgICAgICAgIDx0cj5cclxuICAgICAgICAgICAgICAgICAgICA8dGQgY29sc3Bhbj1cIjJcIj57e2V4YW0uVGVhY2hlckZJT319PC90ZD5cclxuICAgICAgICAgICAgICAgIDwvdHI+XHJcbiAgICAgICAgICAgICAgICA8dHI+XHJcbiAgICAgICAgICAgICAgICAgICAgPHRkIGNvbHNwYW49XCIyXCI+0JrQvtC9Y9GD0LvRjNGC0LDRhtC40Y88L3RkPlxyXG4gICAgICAgICAgICAgICAgPC90cj5cclxuICAgICAgICAgICAgICAgIDx0cj5cclxuICAgICAgICAgICAgICAgICAgICA8dGQ+e3tleGFtLkNvbnN1bHRhdGlvbkRhdGVUaW1lfX08L3RkPlxyXG4gICAgICAgICAgICAgICAgICAgIDx0ZD57e2V4YW0uQ29uc3VsdGF0aW9uQXVkaXRvcml1bU5hbWV9fTwvdGQ+XHJcbiAgICAgICAgICAgICAgICA8L3RyPlxyXG4gICAgICAgICAgICAgICAgPHRyPlxyXG4gICAgICAgICAgICAgICAgICAgIDx0ZCBjb2xzcGFuPVwiMlwiPtCt0LrQt9Cw0LzQtdC9PC90ZD5cclxuICAgICAgICAgICAgICAgIDwvdHI+XHJcbiAgICAgICAgICAgICAgICA8dHI+XHJcbiAgICAgICAgICAgICAgICAgICAgPHRkPnt7ZXhhbS5FeGFtRGF0ZVRpbWV9fTwvdGQ+ICAgICAgICAgICAgICAgIFxyXG4gICAgICAgICAgICAgICAgICAgIDx0ZD57e2V4YW0uRXhhbUF1ZGl0b3JpdW1OYW1lfX08L3RkPlxyXG4gICAgICAgICAgICAgICAgPC90cj5cclxuICAgICAgICAgICAgPC90YWJsZT5cclxuICAgICAgICA8L2Rpdj4gICAgICAgIFxyXG4gICAgPC9kaXY+XHJcbjwvZGl2PlxyXG5cclxuXHJcbmAsXHJcbiAgICBwcm92aWRlcnM6IFtTY2hlZHVsZURhdGFTZXJ2aWNlXVxyXG59KVxyXG5leHBvcnQgY2xhc3MgQ21wMUNvbXBvbmVudCBpbXBsZW1lbnRzIE9uSW5pdHsgICAgXHJcbiAgICBzdHVkZW50R3JvdXBzOiBTdHVkZW50R3JvdXBbXTtcclxuICAgIGdyb3Vwc0V4YW1zUmVzcG9uc2U6IEV4YW1bXTtcclxuICAgIGNvbnN0cnVjdG9yKHByaXZhdGUgX2h0dHBTZXJ2aWNlOiBTY2hlZHVsZURhdGFTZXJ2aWNlKSB7fVxyXG5cclxuXHJcbiAgICBuZ09uSW5pdCgpOmFueSB7XHJcbiAgICAgICAgdGhpcy5nZXRNYWluR3JvdXBzKCk7XHJcbiAgICB9XHJcblxyXG4gICAgZ2V0TWFpbkdyb3VwcygpIHtcclxuICAgICAgICB0aGlzLl9odHRwU2VydmljZS5nZXRNYWluU3R1ZGVudEdyb3VwcygpXHJcbiAgICAgICAgICAgIC5zdWJzY3JpYmUoXHJcbiAgICAgICAgICAgICAgICByZXNwb25zZSA9PiB0aGlzLnN0dWRlbnRHcm91cHMgPSByZXNwb25zZSxcclxuICAgICAgICAgICAgICAgIGVycm9yID0+IGNvbnNvbGUubG9nKGVycm9yKVxyXG4gICAgICAgICAgICApO1xyXG4gICAgfVxyXG5cclxuICAgIGdldEdyb3VwRXhhbXMoZ3JvdXBJZDogc3RyaW5nKSB7XHJcbiAgICAgICAgdGhpcy5faHR0cFNlcnZpY2UuZ2V0R3JvdXBFeGFtcyhncm91cElkKVxyXG4gICAgICAgICAgICAuc3Vic2NyaWJlKFxyXG4gICAgICAgICAgICAgICAgcmVzcG9uc2UgPT4gdGhpcy5ncm91cHNFeGFtc1Jlc3BvbnNlID0gcmVzcG9uc2UsXHJcbiAgICAgICAgICAgICAgICBlcnJvciA9PiBjb25zb2xlLmxvZyhlcnJvcilcclxuICAgICAgICAgICAgKTtcclxuICAgIH1cclxufSJdLCJzb3VyY2VSb290IjoiL3NvdXJjZS8ifQ==
