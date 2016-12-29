var app = angular.module("backend", ['ngRoute', 'ng.ueditor', 'ngclipboard', 'angular-md5']);

app.config(function($routeProvider) {
    $routeProvider
    .when('/', {
      templateUrl : 'pages/login.html',
      controller  : 'loginController'
    })
    .when('/list', {
      templateUrl : 'pages/list.php',
      controller  : 'listController'
    })
    .when('/edit', {
      templateUrl : 'pages/edit.php',
      controller  : 'editController'
    })
    .when('/create', {
      templateUrl : 'pages/create.php',
      controller  : 'createController'
    })
    .when('/role', {
      templateUrl : 'pages/role.php',
      controller  : 'roleController'
    })
    .otherwise("/");
});

//clear route cache, reload
app.run(['$rootScope', '$window', '$location', '$templateCache', '$http', function ($rootScope, $window, $location, $templateCache, $http) {  
    var routeChangeSuccessOff = $rootScope.$on('$routeChangeSuccess', routeChangeSuccess);  

    function routeChangeSuccess(event, params) {
        if (params.originalPath != "/" && window.localStorage.getItem("isLogin") != "true") {
          window.location.href = "#/";
          return;
        }
        $templateCache.removeAll();
    }

    $rootScope.logout = function () {
      $http({
          url : 'controllers/index.php?module=user&action=logout',
          method : 'get'
      })
      .success(function() {
        window.localStorage.removeItem("isLogin");
        window.localStorage.removeItem("userId");
        window.location.href = "#/";
      })
      .error(function(error, header, config, status) {
          console.log(error);
          window.location.href = "#/list";
      });
    };
}]);

app
.controller("loginController", ['$scope', '$rootScope', '$http', '$timeout', 'md5', function($scope, $rootScope, $http, $timeout, md5) {
    $scope.username = '';
    $scope.password = '';

    if (window.localStorage.getItem('isLogin') == 'true') {
      window.location.href = "#/list";
      return;
    }

    $scope.login = function() {
      if ($scope.username != '' && $scope.password != '') {
        $http({
            url : 'controllers/index.php?module=user&action=login',
            method : 'POST',
            data : $.param({ "name" : $scope.username, "password" : md5.createHash($scope.password)} ),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
            responseType : 'json'
        })
        .success(function(data, header, config) {
            var code = data.code;

            if (code == 500) {
              $('#hintDiv').fadeIn(500);
              $timeout(function() {
                $('#hintDiv').fadeOut(2000);
              }, 1000);
              return;
            }
            window.localStorage.setItem("isLogin", true);
            window.localStorage.setItem("userId", data.id);
            window.location.href = "#/list";
        })
        .error(function(error, header, config) {
            window.location.href = "#/";
            console.log(error);
        });
      }
    };

    $scope.close = function() {
      $('#hintDiv').fadeOut();
    };
}])

.controller("listController", ['$scope', '$http', '$document', '$timeout', 'httpService', function($scope, $http, $document, $timeout, httpService) {
    
    $scope.questionaireId = 0;
    $scope.questionaires = [];
    $scope.questionaire = {};
    $scope.keyword = '';
    $scope.domain = window.location.host;

    var queryparams = {
      id : window.localStorage.getItem("userId")
    }

    httpService.get('controllers/index.php?module=user&action=getRole', queryparams, function(data, header, config) {
        if (data.code == 200) {

           $scope.currentRole = data.role;

           if (data.role == "system_admin") {
                $http({
                  url : 'controllers/index.php?module=questionaire&action=queryQuestionaires',
                  method : 'get',
                  headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
                  responseType : 'json'
                })
                .success(function(data, header, config) {
                  $scope.questionaires = data;
                })
                .error(function(error, header, config) {
                  console.log(error);
                });

                $document.bind("keypress", function(event) {
                    if (event.keyCode == 13) {
                        $scope.search();
                    }
                });
           } else {
                $http({
                  url : 'controllers/index.php?module=questionaire&action=queryQuestionaires&brand=' + data.brand,
                  method : 'get',
                  headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
                  responseType : 'json'
                })
                .success(function(data, header, config) {
                  $scope.questionaires = data;
                })
                .error(function(error, header, config) {
                  console.log(error);
                });

                $document.bind("keypress", function(event) {
                    if (event.keyCode == 13) {
                        $scope.search();
                    }
                });
           }
        }
    }, function(error, header, config) {
        console.log(error);
    })

    $scope.edit = function(num) {
      window.localStorage.setItem("questionaireId", num);
      window.location.href = "#/edit";
    };

    $scope.delete = function(num) {
      //delete 
      $http({
          url : 'controllers/index.php?module=questionaire&action=deleteQuestionaire',
          method : 'post',
          data : $.param({ "id" : num }),
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
      })
      .success(function(data, header, config) {
          $('#myModal').modal('hide');

          if (data.code == 200) {
            $scope.tip = "删除成功!";
            tipWork();

            $http({
              url : 'controllers/index.php?module=questionaire&action=queryQuestionaires',
              method : 'get',
              headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
              responseType : 'json'
            })
            .success(function(data, header, config) {
              $scope.questionaires = data;
            })
            .error(function(error, header, config) {
              console.log(error);
            });
          } else {
            $scope.tip = "删除失败，稍后再试!";
            tipWork();
          }
      })
      .error(function(error, header, config) {
          console.log(error);
          window.location.href = "#/list";
      });
    };

    $scope.sendId = function(id) {
      $scope.questionaireId = id;
    };

    $scope.search = function() {
      if ($scope.keyword != '') {
          $http({
            url : 'controllers/index.php?module=questionaire&action=searchQuestionaire',
            method : 'post',
            data : $.param({ "keyword" : $scope.keyword}),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
          })
          .success(function(data, header, config) {
              $scope.questionaires = data;
              $scope.keyword = '';
          })
          .error(function(error, header, config) {
              console.log(error);
              window.location.href = "#/list";
          });
      } else {
        $http({
          url : 'controllers/index.php?module=questionaire&action=queryQuestionaires',
          method : 'get',
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
          responseType : 'json'
        })
        .success(function(data, header, config) {
          $scope.questionaires = data;
        })
        .error(function(error, header, config) {
          console.log(error);
        });
      }
    };

    //delay realTime search
    // var timeout;
    // $scope.$watch('searchWord', function(newVal) {
    //   if (newVal) {
    //     if (timeout) $timeout.cancel(timeout);
    //     timeout = $timeout(function() {
    //       $scope.search();
    //     }, 350);
    //   }
    // });

    $scope.download = function(id) {
      window.location.href = "controllers/index.php?module=question&action=download&id=" + id;
    }
}])

.controller("createController", ['$scope', '$http', '$rootScope', 'httpService', function($scope, $http, $rootScope, httpService) {
    $scope.config = {
        toolbars: [
            ['fullscreen', 'source', 'undo', 'redo'],
            ['bold', 'fontfamily', 'fontsize', 'link', 'unlink', 'justifyleft', 'justifyright', 'justifycenter', 'justifyjustify', 'horizontal', 'indent', 'italic', 'underline', 'fontborder', 'simpleupload', 'insertvideo', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', 'preview']
        ],
        autoHeightEnabled: true,
        autoFloatEnabled: true
    }

    $scope.questionaire = {
      'subject' : '', 
      'brand' : '',
      'description' : '',
      'isProvicy' : false,
      'required_fst' : false,
      'agree_fst' : '',
      'required_snd' : false,
      'agree_snd' : ''
    };

    var queryparams = {
      id : window.localStorage.getItem("userId")
    }

    $scope.currentRole = "";

    httpService.get('controllers/index.php?module=user&action=getRole', queryparams, function(data, header, config) {
        if (data.code == 200) {
          $scope.currentRole = data.role;

          if (data.role != "system_admin") {
            $scope.questionaire.brand = data.brand;
          }
        }
    }, function(error, header, config) {
      console.log(error);
    })

    $scope.ready = function(editor){
        editor.focus();
    }

    $scope.showHead = true;
    $scope.isSubmit = false;
    $scope.currentIndex = 0;

    var initQuestion = {
      title : '',
      isSingle : true,
      isSetSkip : false,
      group : {
        gp1 : '',
        gp2 : ''
      }
    };

    var initOption = {
      isNext : true,
      isCustOmized : false,
      isSkip : initQuestion.isSetSkip,
      isSkipOne : true,
      content : ""
    };

    initOption.isSkipOne == true ? initOption.skipIndex = initQuestion.group.gp1 : initOption.skipIndex = initQuestion.group.gp2;

    initQuestion.options = [initOption]

    $scope.questions = [initQuestion];

    $scope.$watch('questions[0].group', function() {
      angular.forEach($scope.questions[0].options, function(op) {
        if (op.isSkipOne) {
          op.skipIndex = $scope.questions[0].group.gp1;
        } else {
          op.skipIndex = $scope.questions[0].group.gp2;
        }
      })
    }, true)

    $scope.switchIsSetSkip = function(item) {
      angular.forEach(item.options, function(op) {
        op.isSkip = item.isSetSkip;
      })
    }

    $scope.switchSkipIndex = function($event, item, group) {
      if (item.isSkipOne) {
        item.skipIndex =  group.gp1;
      } else {
        item.skipIndex =  group.gp2;
      }
    }

    $scope.addOption = function() {
      if (isOptionEmpty()) {
        $scope.tip = "选项不得为空!";
        tipWork();
        return;
      }

      var currentQuestion = $scope.questions[$scope.currentIndex];

      var addOption = {
        isNext : true,
        isCustOmized: false,
        content : '',
        isSkip : currentQuestion.isSetSkip,
        isSkipOne : true, 
      };

      addOption.isSkipOne == true ? addOption.skipIndex = currentQuestion.group.gp1 : addOption.skipIndex = currentQuestion.group.gp2;

      currentQuestion.options.push(addOption);
    }

    function validateCurrentQuestion() {
      if ($scope.questions[$scope.currentIndex].title == "" || isOptionEmpty()) {
        return true;
      } else {
        return false;
      }
    }

    function isOptionEmpty() {
      var currentQuestion = $scope.questions[$scope.currentIndex];
      var maxIndex = currentQuestion.options.length - 1;

      if (!currentQuestion.options[maxIndex].isCustOmized && currentQuestion.options[maxIndex].content == '') {
        return true;
      } else {
        return false;
      }
    }

    function validateGroup() {
      if (!$scope.questions[$scope.currentIndex].isSetSkip) {
        return false;
      }

      var group = $scope.questions[$scope.currentIndex].group;

      if (!isPositiveInteger(group.gp1) || !isPositiveInteger(group.gp2) || parseInt(group.gp1) <= ($scope.currentIndex + 1) || parseInt(group.gp2) <= ($scope.currentIndex + 1) || group.gp1 == group.gp2) {
        return true;
      } else {
        return false;
      }
    }

    $scope.deleteQuestion = function() {
      if ($scope.questions.length == 0 || $scope.questions.length == 1 && $scope.currentIndex == 0) {
        $scope.tip = "问卷问题不得为空!";
        tipWork();
        return;
      }

      var pos = $scope.currentIndex;

      if ($scope.currentIndex > 0) {
        $scope.currentIndex--;
      }
      $scope.questions.splice(pos, 1);
    }

    $scope.deleteOption = function(options, index) {
      if (options.length == 0 || options.length == 1 && index == 0) {
        $scope.tip = "问题至少需要一个选项!";
        tipWork();
        return;
      }

      options.splice(index, 1);
    }

    $scope.lastQues = function() {
      if (validateCurrentQuestion()) {
        $scope.tip = "请先完成当前问题!";
        tipWork();
        return;
      }

      if (validateGroup()) {
        $scope.tip = "索引不合法!";
        tipWork();
        return;
      }

      --$scope.currentIndex;
    }

    $scope.nextQues =function() {
      if (validateCurrentQuestion()) {
        $scope.tip = "请先完成当前问题!";
        tipWork();
        return;
      }

      if (validateGroup()) {
        $scope.tip = "索引不合法!";
        tipWork();
        return;
      }

      ++$scope.currentIndex;

      if ($scope.currentIndex == $scope.questions.length) {

        var addQuestion = {
          title : '',
          isSingle : true,
          isSetSkip : false,
          group : {
            gp1 : '',
            gp2 : ''
          }
        };

        var addOption = {
          isNext : true,
          isCustOmized : false,
          isSkip : addQuestion.isSetSkip,
          isSkipOne : true,
          content : ""
        };

        addOption.isSkipOne == true ? addOption.skipIndex = addQuestion.group.gp1 : addOption.skipIndex = addQuestion.group.gp2;

        addQuestion.options = [addOption]

        $scope.questions.push(addQuestion);
        
        var max = $scope.questions.length - 1;

        $scope.$watch(function() {
          if ($scope.questions[max] != null) {
            return $scope.questions[max].group
          }
        }, function() {
          if ($scope.questions[max] != null) {
            angular.forEach($scope.questions[max].options, function(op) {
              if (op.isSkipOne) {
                op.skipIndex = $scope.questions[max].group.gp1;
              } else {
                op.skipIndex = $scope.questions[max].group.gp2;
              }
            })
          }
        }, true)
      }
    }

    $scope.createQuestion = function() {

      if ($scope.questionaire.subject == "" || $scope.questionaire.subject == undefined) {
        $scope.tip = "问卷主题不得为空!";
        tipWork();
        return;
      }

      if ($scope.questionaire.isProvicy && $scope.questionaire.description == "") {
        $scope.tip = "问卷说明不得为空!";
        tipWork();
        return;
      } else {
        $scope.questionaire.required_fst = false;
        $scope.questionaire.agree_fst = "";
        $scope.questionaire.required_snd = false;
        $scope.questionaire.agree_snd = "";
      }

      $scope.showHead = false;
    }

    $scope.finishCreate = function() {
      $scope.isSubmit = true;

      if (validateCurrentQuestion()) {
        $scope.isSubmit = false;
        $scope.tip = "请先完成当前问题!";
        tipWork();
        return;
      }

      if (validateGroup()) {
        $scope.tip = "索引不合法!";
        tipWork();
        return;
      }

      if ($scope.questionaire.subject == "" || $scope.questionaire.subject == undefined) {
        $scope.isSubmit = false;
        return;
      }
      
      $scope.questionaire.questions = $scope.questions;

      $http({
          url : 'controllers/index.php?module=questionaire&action=createQuestionaire',
          method : 'post',
          data : $.param($scope.questionaire),
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
      })
      .success(function(data, header, config) {
        if (data.code == 200) {
          window.location.href = "#/list";
        } else {
          $scope.tip = "创建失败，稍后再试";
          $scope.isSubmit = false;
          tipWork();
        }
      })
      .error(function(error, header, config) {
          $scope.isSubmit = false;
          $scope.tip = error;
          tipWork();
      });
    }

    $scope.close = function() {
      $('#hintDiv').fadeOut();
    };
}])

.controller("editController", ['$scope', '$http', 'httpService', function($scope, $http, httpService) {
    $scope.config = {
        toolbars: [
            ['fullscreen', 'source', 'undo', 'redo'],
            ['bold', 'fontfamily', 'fontsize', 'link', 'unlink', 'justifyleft', 'justifyright', 'justifycenter', 'justifyjustify', 'horizontal', 'indent', 'italic', 'underline', 'fontborder', 'simpleupload', 'insertvideo', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', 'preview']
        ],
        autoHeightEnabled: true,
        autoFloatEnabled: true
    }

    $scope.isSubmit = false;

    $scope.ready = function(editor){
        editor.focus();
    }

    var num = window.localStorage.getItem("questionaireId");

    $http({
        url : 'controllers/index.php?module=questionaire&action=queryQuestionaire',
        method : 'post',
        data : $.param({ "id" : num }),
        headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
        responseType : 'json'
    })
    .success(function(data, header, config) {
        if (data.code == 500) {
          $scope.tip = "请求数据错误!";
          tipWork();
          return;
        }
        
        $scope.questionaire = data.result;
        $scope.questionaire.addQuestions = [];

        angular.forEach($scope.questionaire.questions, function(it) {
          it.addOptions = [];

          $scope.$watch(function() {
            return it.group
          }, function() {
            angular.forEach(it.options, function(op) {
              if (op.isSkipOne) {
                op.skipIndex = it.group.gp1;
              } else {
                op.skipIndex = it.group.gp2;
              }
            })

            angular.forEach(it.addOptions, function(op) {
              if (op.isSkipOne) {
                op.skipIndex = it.group.gp1;
              } else {
                op.skipIndex = it.group.gp2;
              }
            })
          }, true)

          angular.forEach(it.options, function(op) {
            op.isSkip = op.isSkip == '1' ? true : false;
            op.isSkipOne = op.isSkipOne == '1' ? true : false;
            op.isHasNext = op.isHasNext == '1' ? true : false;
            op.isCustomized = op.isCustomized == '1' ? true : false;
          });

          it.isSingle = it.isSingle == '1' ? true : false;
          it.isSetSkip = it.options[0].isSkip;
          it.originIsSetSkip = it.options[0].isSkip;
          
          var gp1 = "", gp2 = "";

          if (it.isSetSkip) {
            angular.forEach(it.options, function(op) {
              if (op.isSkipOne) {
                gp1 = op.skipIndex;
              }

              if (!op.isSkipOne) {
                gp2 = op.skipIndex;
              }
            });
          }

          it.group = {
            'gp1' : gp1,
            'gp2' : gp2
          }

          if (!it.isSetSkip) {
            angular.forEach(it.options, function(op) {
              op.isSkipOne = true;
            })
          }
        });
    })
    .error(function(error, header, config, status) {
        console.log(error);
        window.location.href = "#/list";
    });

    $scope.sendQuestionId = function(questionId) {
      $scope.deleteQuestionId = questionId;
    }

    $scope.sendQuestionOptionId = function(optionId) {
      $scope.deleteQuestionOptionId = optionId;
    }

    $scope.addOption = function(question) {
      if ((length = question.addOptions.length) != 0) {
        if (!question.addOptions[length - 1].isCustomized && question.addOptions[length - 1].content == '') {
          $scope.tip = "选项不得为空!";
          tipWork();
          return;
        }
      }

      var addOption = {
        isHasNext : true,
        isCustomized: false,
        content : '',
        isSkip : question.isSetSkip,
        isSkipOne : true, 
      };

      addOption.isSkipOne == true ? addOption.skipIndex = question.group.gp1 : addOption.skipIndex = question.group.gp2;

      question.addOptions.push(addOption)
    }

    $scope.addOptionAddQuestion = function(question) {
      if ((length = question.options.length) != 0) {
        if (!question.options[length - 1].isCustomized && question.options[length - 1].content == '') {
          $scope.tip = "选项不得为空!";
          tipWork();
          return;
        }
      }
      var addOption = {
        isHasNext : true,
        isCustomized: false,
        content : '',
        isSkip : question.isSetSkip,
        isSkipOne : true, 
      };

      addOption.isSkipOne == true ? addOption.skipIndex = question.group.gp1 : addOption.skipIndex = question.group.gp2;

      question.options.push(addOption)
    }

    $scope.deleteOption = function(options, index, question) {
      if (options.length == 0 || options.length == 1 && index == 0 &&  question.options.length == 0) {
        $scope.tip = "问题至少需要一个选项!";
        tipWork();
        return;
      }
      console.log(index)
      console.log(options)
      options.splice(index, 1);
    }

    $scope.deleteOptionAdd = function(options, index) {
      if (options.length == 0 || options.length == 1 && index == 0) {
        $scope.tip = "问题至少需要一个选项!";
        tipWork();
        return;
      }
      console.log(index)
      console.log(options)
      options.splice(index, 1);
    }

    $scope.saveAddQuestionOptions = function(question, index) {
      if ((length = question.addOptions.length) != 0) {
        if (!question.addOptions[length - 1].isCustomized && question.addOptions[length - 1].content == '') {
          $scope.tip = "选项不得为空!";
          tipWork();
          return;
        }
      }

      if (question.isSetSkip) {
        var group = question.group;

        if (!isPositiveInteger(group.gp1) || !isPositiveInteger(group.gp2) || parseInt(group.gp1) <= index || parseInt(group.gp2) <= index || group.gp1 == group.gp2) {
          $scope.tip = "索引不合法!";
          tipWork();
          return;
        }
      }

      $scope.isSubmit = true;

      var data = {"id" : question.id, "addOptions" : question.addOptions}

      httpService.post('controllers/index.php?module=question&action=addQuestionOption', data, function(data, header, config) {
          if (data.code == 200) {
            $scope.tip = "更新成功!";
            tipWork(function() {
              window.location.href = "#/list";
            });
          } else {
            $scope.tip = "更新失败，请稍后再试!";
            tipWork();
            $scope.isSubmit = false;
          }
      }, function(error, header, config) {
          console.log(error);
          window.location.href = "#/list";
      })
    }

    $scope.addQuestion = function(questionaire) {
        if ((length = questionaire.addQuestions.length) != 0) {
          var maxQues = questionaire.addQuestions[length - 1];

          if (maxQues.isSetSkip) {
            var group = maxQues.group;
            var index = questionaire.questions.length + length;

            if (!isPositiveInteger(group.gp1) || !isPositiveInteger(group.gp2) || parseInt(group.gp1) <= index || parseInt(group.gp2) <= index || group.gp1 == group.gp2) {
              $scope.tip = "索引不合法!";
              tipWork();
              return;
            }
          }

          var maxLength = maxQues.options.length;
          var maxOption = maxQues.options[maxLength - 1];

          if (maxQues.title == "" || (!maxOption.isCustomized && maxOption.content == "")) {
            $scope.tip = "问题或选项不得为空!";
            tipWork();
            return;
          }
        }

        var addQuestion = {
          title : '',
          isSingle : true,
          isSetSkip : false,
          group : {
            gp1 : '',
            gp2 : ''
          }
        };

        var addOption = {
          isHasNext : true,
          isCustomized : false,
          isSkip : addQuestion.isSetSkip,
          isSkipOne : true,
          content : ""
        };

        addOption.isSkipOne == true ? addOption.skipIndex = addQuestion.group.gp1 : addOption.skipIndex = addQuestion.group.gp2;

        addQuestion.options = [addOption]

        $scope.questionaire.addQuestions.push(addQuestion);
        
        var max = $scope.questionaire.addQuestions.length - 1;

        $scope.$watch(function() {
          if ($scope.questionaire.addQuestions[max] != null) {
            return $scope.questionaire.addQuestions[max].group
          }
        }, function() {
          if ($scope.questionaire.addQuestions[max] != null) {
            angular.forEach($scope.questionaire.addQuestions[max].options, function(op) {
              if (op.isSkipOne) {
                op.skipIndex = $scope.questionaire.addQuestions[max].group.gp1;
              } else {
                op.skipIndex = $scope.questionaire.addQuestions[max].group.gp2;
              }
            })
          }
        }, true)
    }

    $scope.saveAddQuestion = function(questionaire) {
      if ((length = questionaire.addQuestions.length) != 0) {
        var maxQues = questionaire.addQuestions[length - 1];

        if (maxQues.isSetSkip) {
          var group = maxQues.group;
          var index = questionaire.questions.length + length;

          if (!isPositiveInteger(group.gp1) || !isPositiveInteger(group.gp2) || parseInt(group.gp1) <= index || parseInt(group.gp2) <= index || group.gp1 == group.gp2) {
            $scope.tip = "索引不合法!";
            tipWork();
            return;
          }
        }

        var maxLength = maxQues.options.length;
        var maxOption = maxQues.options[maxLength - 1];

        if ((!maxOption.isCustomized && maxOption.content == "") || maxQues.title == "") {
          $scope.tip = "问题或选项不得为空!";
          tipWork();
          return;
        }
      }

      $scope.isSubmit = true;

      var data = {
        "id" : questionaire.id,
        "addQuestions" : questionaire.addQuestions 
      }

      httpService.post('controllers/index.php?module=question&action=addQuestion', data, function(data, header, config) {
          if (data.code == 200) {
            $scope.tip = "更新成功!";
            tipWork(function() {
              window.location.href = "#/list";
            });
          } else {
            $scope.tip = "更新失败，请稍后再试!";
            tipWork();
            $scope.isSubmit = false;
          }
      }, function(error, header, config) {
          console.log(error);
          window.location.href = "#/list";
      })
    }

    $scope.saveQuestionaire = function(questionaireId) {

      if ($scope.questionaire.subject == "" || $scope.questionaire.subject == undefined) {
        return;
      }

      if ($scope.questionaire.isProvicy && $scope.questionaire.description == "") {
        return;
      } else {
        $scope.questionaire.required_fst = false;
        $scope.questionaire.agree_fst = "";
        $scope.questionaire.required_snd = false;
        $scope.questionaire.agree_snd = "";
      }

      $scope.isSubmit = true;

      var data = {
        "id" : questionaireId, 
        "subject" : $scope.questionaire.subject, 
        "description" : $scope.questionaire.description, 
        "isProvicy" : $scope.questionaire.isProvicy,
        'required_fst' : $scope.questionaire.required_fst,
        'agree_fst' : $scope.questionaire.agree_fst,
        'required_snd' : $scope.questionaire.required_snd,
        'agree_snd' : $scope.questionaire.agree_snd
      }

      httpService.post('controllers/index.php?module=questionaire&action=updateQuestionaire', data, function(data, header, config) {
          if (data.code == 200) {
            $scope.tip = "更新成功!";
            tipWork(function() {
              window.location.href = "#/list";
            });
          } else {
            $scope.tip = "更新失败，请稍后再试!";
            tipWork();
            $scope.isSubmit = false;
          }
      }, function(error, header, config) {
          console.log(error);
          window.location.href = "#/list";
      })
    };

    //原先设置了跳题索引现要进行索引更新可以以原先索引的值为条件进行批量更新
    //原先没有进行跳题设置，现改为可以跳题，如需进行批量更新则需要根据每个选项的跳题group进行更新
    $scope.saveQuestion = function(questionId, question, index) {
      if (question.title == "") {
        return;
      }

      $scope.isSubmit = true;

      var data = {
        "id" : questionId,
        "title" : question.title,
        "isSingle" : question.isSingle
      };

      data.isSetSkip = question.isSetSkip;

      if (question.isSetSkip) {
        var group = question.group;

        if (!isPositiveInteger(group.gp1) || !isPositiveInteger(group.gp2) || parseInt(group.gp1) <= index || parseInt(group.gp2) <= index || group.gp1 == group.gp2) {
          $scope.tip = "索引不合法!";
          tipWork();
          $scope.isSubmit = false;
          return;
        }

        var pairs = [];

        angular.forEach(question.options, function(op) {
          pairs.push({"id" : op.id, 'isSkipOne' : op.isSkipOne, "skipIndex" : op.skipIndex})
        });

        data.pairs = pairs;

      } else {
        if (question.originIsSetSkip) {
          var optionUpdateIds = [];
          angular.forEach(question.options, function(op) {
            optionUpdateIds.push(op.id)
          });
          data.optionIds = optionUpdateIds;
        }
      }

      httpService.post('controllers/index.php?module=question&action=updateQuestion', data, function(data, header, config) {
          if (data.code == 200) {
            $scope.tip = "更新成功!";
            tipWork(function() {
              window.location.href = "#/list";
            });
          } else {
            $scope.tip = "更新失败，请稍后再试!";
            tipWork();
            $scope.isSubmit = false;
          }
      }, function(error, header, config) {
          console.log(error);
          window.location.href = "#/list";
      })
    };

    $scope.saveQuestionOption = function(optionId, item) {

      if (item.content == "") {
        return;
      }

      $scope.isSubmit = true;

      var data = {
        "id" : optionId,
        "content" : item.content,
        "isHasNext" : item.isHasNext,
        "isCustomized" : item.isCustomized
      };

      httpService.post('controllers/index.php?module=question&action=updateQuestionOption', data, function(data, header, config) {
          if (data.code == 200) {
            $scope.tip = "更新成功!";
            tipWork(function() {
              window.location.href = "#/list";
            });
          } else {
            $scope.tip = "更新失败，请稍后再试!";
            tipWork();
            $scope.isSubmit = false;
          }
      }, function(error, header, config) {
          console.log(error);
          window.location.href = "#/list";
      })
    };

    $scope.deleteQuestion = function(questionId) {
      httpService.post('controllers/index.php?module=question&action=deleteQuestion', {"id" : questionId}, function(data, header, config) {
          $('#questionModal').modal('hide');

          if (data.code == 200) {
            $scope.tip = "删除成功!";
            tipWork(function() {
              window.location.href = "#/list";
            });
          } else {
            $scope.tip = "删除失败，请稍后再试!";
            tipWork();
          }
      }, function(error, header, config) {
          $('#questionModal').modal('hide');
          console.log(error);
          window.location.href = "#/list";
      })
    }

    $scope.deleteQuestionOption = function(optionId) {
      httpService.post('controllers/index.php?module=question&action=deleteQuestionOption', {"id" : optionId}, function(data, header, config) {
          $('#optionModal').modal('hide');

          if (data.code == 200) {
            $scope.tip = "删除成功!";
            tipWork(function() {
              window.location.href = "#/list";
            });
          } else {
            $scope.tip = "删除失败，请稍后再试!";
            tipWork();
          }
      }, function(error, header, config) {
          $('#optionModal').modal('hide');
          console.log(error);
          window.location.href = "#/list";
      })
    }

    $scope.deleteQuestionAdd = function(pos) {
      $scope.questionaire.addQuestions.splice(pos, 1);
    }

    $scope.switchIsSetSkip = function(item) {
      angular.forEach(item.options, function(op) {
        op.isSkip = item.isSetSkip;
      })

      angular.forEach(item.addOptions, function(op) {
        op.isSkip = item.isSetSkip;
      })
    }

    $scope.switchIsSetSkipAdd = function(item) {
      angular.forEach(item.options, function(op) {
        op.isSkip = item.isSetSkip;
      })
    }

    $scope.switchSkipIndex = function(item, group) {
      if (item.isSkipOne) {
        item.skipIndex =  group.gp1;
      } else {
        item.skipIndex =  group.gp2;
      }
    }

    $scope.close = function() {
      $('#hintDiv').fadeOut();
    };

    $scope.cancel = function() {
      window.location.href = "#/list";
    };
}])

.controller('roleController', ['$scope', 'md5', 'httpService', function($scope, md5, httpService) {
  $scope.isShowAdmin = false;
  var deleteAdminId;
  var deleteOperatorId;
  var deleteOperatorIndex;
  $scope.currentRole = ""
  var currentBrand = "";

  var queryparams = {
    id : window.localStorage.getItem("userId")
  }

  httpService.get('controllers/index.php?module=user&action=getRole', queryparams, function(data, header, config) {
      if (data.code == 200) {
         $scope.currentRole = data.role;

         if (data.role == "system_admin") {
            $scope.isShowAdmin = true;

            httpService.get('controllers/index.php?module=user&action=getBrandAdmin', {}, function(data, header, config) {
                if (data.code != 500) {
                    $scope.admins = data;
                }
            }, function(error, header, config) {
                console.log(error);
            })

            httpService.get('controllers/index.php?module=user&action=getBrandOperator', {}, function(data, header, config) {
                if (data.code != 500) {
                    $scope.operators = data;
                }
            }, function(error, header, config) {
                console.log(error);
            })
         } else if (data.role == "brand_admin") {
            currentBrand = data.brand;
            httpService.get('controllers/index.php?module=user&action=getBrandOperator', {brand : data.brand}, function(data, header, config) {
                if (data.code != 500) {
                    $scope.operators = data;
                }
            }, function(error, header, config) {
                console.log(error);
            })
         }
      }
  }, function(error, header, config) {
      console.log(error);
  })

  $scope.addAdmins = []

  $scope.addAdmin = function() {
    $scope.addAdmins.push({
      brand : '',
      name : '',
      password : ''
    })
  }

  $scope.saveAddAdmin = function(ad, index) {
      if (ad.brand =="" || ad.name == "" || ad.password == "") {
        $scope.tip = "请填写所有必填项!";
        tipWork();
        return;
      }

      validateUserNameExist(ad.name, function(data, header, config) {
        if (data.result) {
          $scope.tip = "用户名已存在!";
          tipWork();
          return;
        } else {
          ad.password = md5.createHash(ad.password)

          httpService.post('controllers/index.php?module=user&action=saveAddAdmin', ad, function(data, header, config) {

              if (data.code == 200) {
                ad.id = data.id;
                ad.createTime = data.createTime;
                $scope.admins.push(ad);
                $scope.addAdmins.splice(index);
              }
          }, function(error, header, config) {
              console.log(error);
          })
        }
      })
  }  

  $scope.saveEditAdmin = function(admin) {
    saveEdit(admin);
  }

  $scope.showDeleteAdminDialog = function(admin) {
    admin.editMode = false;
    deleteAdminId = admin.id;
  }

  $scope.deleteAdmin = function() {
    httpService.get('controllers/index.php?module=user&action=deleteUser', {id : deleteAdminId}, function(data, header, config) {

        if (data.code == 200) {
          $('#myModal').modal('hide');

          httpService.get('controllers/index.php?module=user&action=getBrandAdmin', {}, function(data, header, config) {
              if (data.code != 500) {
                  $scope.admins = data;
              }
          }, function(error, header, config) {
              console.log(error);
          })
        }
    }, function(error, header, config) {
        $('#myModal').modal('hide');
        console.log(error);
    })
  }

  $scope.enterEditMode = function(admin) {
    admin.editBrand = admin.brand;
    admin.editName = admin.name;
    admin.editPassword = admin.password;
    admin.editMode = true;
  }

  $scope.cancelAddAdmin = function(index) {
    $scope.addAdmins.splice(index, 1);
  } 

  $scope.cancelEditAdmin = function(admin) {
    admin.editMode = false;
  }

  $scope.switchAdmin = function() {
    $scope.isShowAdmin = true;
  }

  $scope.switchOperator = function() {
    $scope.isShowAdmin = false;
  }

  function validateUserNameExist(name, successCallback) {
    httpService.get('controllers/index.php?module=user&action=checkUsername', {name : name}, successCallback, function(error, header, config) {
        console.log(error);
    })
  }

  $scope.addOperators = []

  $scope.addOperator = function() {
    if ($scope.currentRole == "system_admin") {
      $scope.addOperators.push({
        brand : '',
        name : '',
        password : ''
      })
    } else {
      $scope.addOperators.push({
        brand : currentBrand,
        name : '',
        password : ''
      })
    }
  }

  $scope.cancelAddOperator = function(index) {
    $scope.addOperators.splice(index, 1);
  } 

  $scope.saveAddOperator = function(ad, index) {
    if (ad.brand =="" || ad.name == "" || ad.password == "") {
      $scope.tip = "请填写所有必填项!";
      tipWork();
      return;
    }

    validateUserNameExist(ad.name, function(data, header, config) {
      if (data.result) {
        $scope.tip = "用户名已存在!";
        tipWork();
        return;
      } else {
        ad.password = md5.createHash(ad.password)

        httpService.post('controllers/index.php?module=user&action=saveAddOperator', ad, function(data, header, config) {

            if (data.code == 200) {
              ad.id = data.id;
              ad.createTime = data.createTime;
              $scope.operators.push(ad);
              $scope.addOperators.splice(index);
            }
        }, function(error, header, config) {
            console.log(error);
        })
      }
    })
  } 

  $scope.enterOperatorEditMode = function(operator) {
    operator.editBrand = operator.brand;
    operator.editName = operator.name;
    operator.editPassword = operator.password;
    operator.editMode = true;
  }

  $scope.cancelEditOperator = function(operator) {
    operator.editMode = false;
  }

  $scope.showDeleteOperatorDialog = function(operator, index) {
    operator.editMode = false;
    deleteOperatorId = operator.id;
    deleteOperatorIndex = index;
  }

  $scope.deleteOperator = function() {
    httpService.get('controllers/index.php?module=user&action=deleteUser', {id : deleteOperatorId}, function(data, header, config) {

        if (data.code == 200) {
          $('#operatorModal').modal('hide');

          $scope.operators.splice(deleteOperatorIndex, 1);
        }
    }, function(error, header, config) {
        $('#myModal').modal('hide');
        console.log(error);
    })
  }

  $scope.saveEditOperator = function(operator) {
      saveEdit(operator);
  }

  function saveEdit(metaData) {
    if (metaData.brand == metaData.editBrand && metaData.name == metaData.editName && metaData.password == md5.createHash(metaData.editPassword)) {
      $scope.tip = "更新成功!";
      tipWork();
      return;
    }

    var updateData = {
      id : metaData.id,
      brand : metaData.editBrand,
      name : metaData.editName,
      password : md5.createHash(metaData.editPassword)
    }

    if (metaData.name != metaData.editName) {
      validateUserNameExist(metaData.editName, function(data, header, config) {
        if (data.result) {
          $scope.tip = "用户名已存在!";
          tipWork();
          return;
        } else {
          httpService.post('controllers/index.php?module=user&action=updateUser', updateData, function(data, header, config) {

              if (data.code == 200) {
                metaData.brand = metaData.editBrand;
                metaData.name = metaData.editName;
                metaData.password = md5.createHash(metaData.editPassword)
                metaData.editMode = false;
                $scope.tip = "更新成功!";
                tipWork();
              }
          }, function(error, header, config) {
              console.log(error);
          })
        }
      })
    } else {
      httpService.post('controllers/index.php?module=user&action=updateUser', updateData, function(data, header, config) {

          if (data.code == 200) {
            metaData.brand = metaData.editBrand;
            metaData.name = metaData.editName;
            metaData.password = md5.createHash(metaData.editPassword)
            metaData.editMode = false;
            $scope.tip = "更新成功!";
            tipWork();
          }
      }, function(error, header, config) {
          console.log(error);
      })
    }
  }

}]);

app.factory('httpService', ['$http', function($http) {
  return {
    get : function(url, params, successCallback, errorCallback) {
        $http({
            url : url + "&" + $.param(params),
            method : 'get',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
            responseType : 'json'
        })
        .success(successCallback)
        .error(errorCallback);
    },
    post : function(url, params, successCallback, errorCallback) {
      $http({
            url : url,
            method : 'post',
            data : $.param(params),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
            responseType : 'json'
      })
      .success(successCallback)
      .error(errorCallback);
    }
  }
}]);

app.filter("to_html", ['$sce', function($sce) {
  return function(input) {
    return $sce.trustAsHtml(input);
  }
}]);

function tipWork(callback) {
  $('#hintDiv').fadeIn(500);

  setTimeout(function() {
    $('#hintDiv').fadeOut(2000, callback);
  }, 1000);
}

function isPositiveInteger(s) {
   var reg = /^[0-9]+$/ ;
   return reg.test(s);
}