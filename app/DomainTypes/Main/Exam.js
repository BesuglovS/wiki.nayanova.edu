System.register([], function(exports_1, context_1) {
    "use strict";
    var __moduleName = context_1 && context_1.id;
    var Exam;
    return {
        setters:[],
        execute: function() {
            /**
             * Created by bs on 11.06.2016.
             */
            Exam = (function () {
                function Exam(DisciplineId, ConsultationDateTime, ConsultationAuditoriumId, ExamDateTime, ExamAuditoriumId, ConsultationAuditoriumName, ExamAuditoriumName, DisciplineName, TeacherFIO) {
                    this.DisciplineId = DisciplineId;
                    this.ConsultationDateTime = ConsultationDateTime;
                    this.ConsultationAuditoriumId = ConsultationAuditoriumId;
                    this.ExamDateTime = ExamDateTime;
                    this.ExamAuditoriumId = ExamAuditoriumId;
                    this.ConsultationAuditoriumName = ConsultationAuditoriumName;
                    this.ExamAuditoriumName = ExamAuditoriumName;
                    this.DisciplineName = DisciplineName;
                    this.TeacherFIO = TeacherFIO;
                }
                return Exam;
            }());
            exports_1("Exam", Exam);
        }
    }
});

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkRvbWFpblR5cGVzL01haW4vRXhhbS50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7O1lBQUE7O2VBRUc7WUFDSDtnQkFXSSxjQUFZLFlBQW1CLEVBQUUsb0JBQTJCLEVBQ2hELHdCQUErQixFQUFFLFlBQW1CLEVBQ3BELGdCQUF1QixFQUFFLDBCQUFpQyxFQUMxRCxrQkFBeUIsRUFBRSxjQUFxQixFQUNoRCxVQUFpQjtvQkFDekIsSUFBSSxDQUFDLFlBQVksR0FBRyxZQUFZLENBQUM7b0JBQ2pDLElBQUksQ0FBQyxvQkFBb0IsR0FBRyxvQkFBb0IsQ0FBQztvQkFDakQsSUFBSSxDQUFDLHdCQUF3QixHQUFHLHdCQUF3QixDQUFDO29CQUN6RCxJQUFJLENBQUMsWUFBWSxHQUFHLFlBQVksQ0FBQztvQkFDakMsSUFBSSxDQUFDLGdCQUFnQixHQUFHLGdCQUFnQixDQUFDO29CQUN6QyxJQUFJLENBQUMsMEJBQTBCLEdBQUcsMEJBQTBCLENBQUM7b0JBQzdELElBQUksQ0FBQyxrQkFBa0IsR0FBRyxrQkFBa0IsQ0FBQztvQkFDN0MsSUFBSSxDQUFDLGNBQWMsR0FBRyxjQUFjLENBQUM7b0JBQ3JDLElBQUksQ0FBQyxVQUFVLEdBQUcsVUFBVSxDQUFDO2dCQUNqQyxDQUFDO2dCQUNMLFdBQUM7WUFBRCxDQTFCQSxBQTBCQyxJQUFBO1lBMUJELHVCQTBCQyxDQUFBIiwiZmlsZSI6IkRvbWFpblR5cGVzL01haW4vRXhhbS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxyXG4gKiBDcmVhdGVkIGJ5IGJzIG9uIDExLjA2LjIwMTYuXHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgRXhhbSB7XHJcbiAgICBEaXNjaXBsaW5lSWQ6IG51bWJlcjtcclxuICAgIENvbnN1bHRhdGlvbkRhdGVUaW1lOiBzdHJpbmc7XHJcbiAgICBDb25zdWx0YXRpb25BdWRpdG9yaXVtSWQ6IG51bWJlcjtcclxuICAgIEV4YW1EYXRlVGltZTogc3RyaW5nO1xyXG4gICAgRXhhbUF1ZGl0b3JpdW1JZDogbnVtYmVyO1xyXG4gICAgQ29uc3VsdGF0aW9uQXVkaXRvcml1bU5hbWU6IHN0cmluZztcclxuICAgIEV4YW1BdWRpdG9yaXVtTmFtZTogc3RyaW5nO1xyXG4gICAgRGlzY2lwbGluZU5hbWU6IHN0cmluZztcclxuICAgIFRlYWNoZXJGSU86IHN0cmluZztcclxuXHJcbiAgICBjb25zdHJ1Y3RvcihEaXNjaXBsaW5lSWQ6bnVtYmVyLCBDb25zdWx0YXRpb25EYXRlVGltZTpzdHJpbmcsIFxyXG4gICAgICAgICAgICAgICAgQ29uc3VsdGF0aW9uQXVkaXRvcml1bUlkOm51bWJlciwgRXhhbURhdGVUaW1lOnN0cmluZywgXHJcbiAgICAgICAgICAgICAgICBFeGFtQXVkaXRvcml1bUlkOm51bWJlciwgQ29uc3VsdGF0aW9uQXVkaXRvcml1bU5hbWU6c3RyaW5nLCBcclxuICAgICAgICAgICAgICAgIEV4YW1BdWRpdG9yaXVtTmFtZTpzdHJpbmcsIERpc2NpcGxpbmVOYW1lOnN0cmluZywgXHJcbiAgICAgICAgICAgICAgICBUZWFjaGVyRklPOnN0cmluZykge1xyXG4gICAgICAgIHRoaXMuRGlzY2lwbGluZUlkID0gRGlzY2lwbGluZUlkO1xyXG4gICAgICAgIHRoaXMuQ29uc3VsdGF0aW9uRGF0ZVRpbWUgPSBDb25zdWx0YXRpb25EYXRlVGltZTtcclxuICAgICAgICB0aGlzLkNvbnN1bHRhdGlvbkF1ZGl0b3JpdW1JZCA9IENvbnN1bHRhdGlvbkF1ZGl0b3JpdW1JZDtcclxuICAgICAgICB0aGlzLkV4YW1EYXRlVGltZSA9IEV4YW1EYXRlVGltZTtcclxuICAgICAgICB0aGlzLkV4YW1BdWRpdG9yaXVtSWQgPSBFeGFtQXVkaXRvcml1bUlkO1xyXG4gICAgICAgIHRoaXMuQ29uc3VsdGF0aW9uQXVkaXRvcml1bU5hbWUgPSBDb25zdWx0YXRpb25BdWRpdG9yaXVtTmFtZTtcclxuICAgICAgICB0aGlzLkV4YW1BdWRpdG9yaXVtTmFtZSA9IEV4YW1BdWRpdG9yaXVtTmFtZTtcclxuICAgICAgICB0aGlzLkRpc2NpcGxpbmVOYW1lID0gRGlzY2lwbGluZU5hbWU7XHJcbiAgICAgICAgdGhpcy5UZWFjaGVyRklPID0gVGVhY2hlckZJTztcclxuICAgIH1cclxufSJdLCJzb3VyY2VSb290IjoiL3NvdXJjZS8ifQ==
