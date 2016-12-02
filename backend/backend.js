var app = angular.module("backend", ['ngRoute', 'ng.ueditor']);

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
    .when('/detail', {
      templateUrl : 'pages/detail.php',
      controller  : 'detailController'
    })
    .when('/edit', {
      templateUrl : 'pages/edit.php',
      controller  : 'editController'
    })
    .when('/create', {
      templateUrl : 'pages/create.php',
      controller  : 'createController'
    })
    .otherwise("/");
});

//clear route cache, reload
app.run(['$rootScope', '$window', '$location', '$templateCache', '$http', function ($rootScope, $window, $location, $templateCache, $http) {  
    var routeChangeSuccessOff = $rootScope.$on('$routeChangeSuccess', routeChangeSuccess);  

    function routeChangeSuccess(event, params) {
        // if (params.originalPath != "/" && window.localStorage.getItem("isLogin") != "true") {
        //   window.location.href = "#/";
        //   return;
        // }
        $templateCache.removeAll();
    }

    $rootScope.logout = function () {
      $http({
          url : 'controllers/logout.php',
          method : 'get'
      })
      .success(function() {
        window.localStorage.setItem("isLogin", false);
        window.location.href = "#/";
      })
      .error(function(error, header, config, status) {
          console.log(error);
          window.location.href = "#/list";
      });
    };
}]);

app
.controller("loginController", ['$scope', '$rootScope', '$http', '$timeout', function($scope, $rootScope, $http, $timeout) {
    $scope.username = '';
    $scope.password = '';

    $scope.login = function() {
      if ($scope.username != '' && $scope.password != '') {
        $http({
            url : 'controllers/login.php',
            method : 'POST',
            data : $.param({ "name" : $scope.username, "password" : $scope.password} ),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
            responseType : 'json'
        })
        .success(function(data, header, config) {
            var code = data.code;

            if (code == 500) {
              $('#hintDiv').show(500);
              $timeout(function() {
                $('#hintDiv').hide(2000);
              }, 1000);
              return;
            }
            window.localStorage.setItem("isLogin", true);
            window.location.href = "#/list";
        })
        .error(function(error, header, config) {
            window.location.href = "#/";
            console.log(error);
        });
      }
    };

    $scope.close = function() {
      $('#hintDiv').hide();
    };
}])

.controller("listController", ['$scope', '$http', '$document', '$timeout', function($scope, $http, $document, $timeout) {
    
    $scope.questionaireId = 0;
    $scope.questionaires = [];
    $scope.questionaire = {};
    $scope.keyword = '';

    $http({
      url : 'controllers/query.php',
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

    $scope.detail = function(num) {
      // query by id
      $http({
          url : 'controllers/queryone.php',
          method : 'post',
          data : $.param({ "id" : num }),
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
          responseType : 'json'
      })
      .success(function(data, header, config, status) {
          window.location.href = "#/detail";
      })
      .error(function(error, header, config, status) {
          console.log(error);
          window.location.href = "#/list";
      });
    };

    $scope.edit = function(num) {
      window.localStorage.setItem("questionaireId", num);
      window.location.href = "#/edit";
    };

    $scope.delete = function(num) {
      //delete 
      $http({
          url : 'controllers/deleteone.php',
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
              url : 'controllers/query.php',
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
        //search
         $http({
          url : '../basic/web/index.php?r=user/search-condition',
          method : 'post',
          data : $.param({ "keyword" : $scope.keyword}),
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
          })
          .success(function(data, header, config, status) {
              $scope.users = data.users;
              $scope.keyword = '';
          })
          .error(function(error, header, config, status) {
              console.log(error);
              window.location.href = "#/list";
          });
      } else {
        window.location.href = "#/list";
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
}])

.controller("detailController", ['$scope', function($scope) {
    $scope.edit = function(num) {

      window.location.href = "#/edit";
    };

    $scope.cancel = function() {
      window.location.href = "#/list";
    };
}])

.controller("createController", ['$scope', '$http', '$rootScope', function($scope, $http, $rootScope) {
    $scope.config = {
        toolbars: [
            ['fullscreen', 'source', 'undo', 'redo'],
            ['bold', 'fontfamily', 'fontsize', 'link', 'unlink', 'justifyleft', 'justifyright', 'justifycenter', 'justifyjustify', 'horizontal', 'indent', 'italic', 'underline', 'fontborder', 'simpleupload', 'insertvideo', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', 'preview']
        ],
        autoHeightEnabled: true,
        autoFloatEnabled: true
    }

    $scope.ready = function(editor){
        editor.focus();
    }

    $scope.showHead = true;
    $scope.isSubmit = false;
    $scope.currentIndex = 0;

    $scope.questionaire = {
      'subject' : '', 
      'description' : ''
    };

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

    function isPositiveInteger(s) {
       var reg = /^[0-9]+$/ ;
       return reg.test(s);
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

    $scope.lastQues =function() {
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
          return $scope.questions[max].group
        }, function() {
          angular.forEach($scope.questions[max].options, function(op) {
            if (op.isSkipOne) {
              op.skipIndex = $scope.questions[max].group.gp1;
            } else {
              op.skipIndex = $scope.questions[max].group.gp2;
            }
          })
        }, true)
      }
    }

    $scope.createQuestion = function() {

      if ($scope.questionaire.subject == "" || $scope.questionaire.description == "") {
        $scope.tip = "问卷主题或说明不得为空!";
        tipWork();
        return;
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

      if ($scope.questionaire.subject == "" || $scope.questionaire.description == "") {
        $scope.isSubmit = false;
        return;
      }
      
      $scope.questionaire.questions = $scope.questions;

      $http({
          url : 'controllers/create.php',
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
      $('#hintDiv').hide();
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

    $scope.ready = function(editor){
        editor.focus();
    }

    var num = window.localStorage.getItem("questionaireId");

    $http({
        url : 'controllers/queryone.php',
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

        angular.forEach($scope.questionaire.questions, function(it) {
          angular.forEach(it.options, function(op) {
            op.isSkip = op.isSkip == '1' ? true : false;
            op.isHasNext = op.isHasNext == '1' ? true : false;
            op.isCustomized = op.isCustomized == '1' ? true : false;
          });

          it.isSingle = it.isSingle == '1' ? true : false;
          it.isSetSkip = it.options[0].isSkip;
          
          var gp1 = "", gp2 = "";

          if (it.isSetSkip) {
            angular.forEach(it.options, function(op) {
              if (gp1 == "") {
                gp1 = op.skipIndex;
              }

              if (gp1 != op.skipIndex) {
                gp2 = op.skipIndex;
              }
            });
          }

          it.group = {
            'gp1' : gp1,
            'gp2' : gp2
          }

          if (it.isSetSkip) {
            angular.forEach(it.options, function(op) {
              op.isSkipOne = op.skipIndex == gp1 ? true : false;
            })
          } else {
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

    $scope.addOption = function() {

    }

    $scope.addQuestion = function() {
      
    }

    $scope.saveQuestionaire = function(questionaireId) {
      console.log(questionaireId);
      // httpService.post('controllers/updateQuestionaire.php', {"id" : questionaireId}, function(data, header, config) {
      //     window.location.href = "#/list";
      // }, function(error, header, config) {
      //     console.log(error);
      //     window.location.href = "#/list";
      // })
    };

    $scope.saveQuestion = function(questionId) {
      console.log(questionId)
      // $http({
      //     url : 'controllers/updateQuestion.php',
      //     method : 'post',
      //     data : $.param({"id" : questionId}),
      //     headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
      // })
      // .success(function(data, header, config) {
      //     window.location.href = "#/list";
      // })
      // .error(function(error, header, config) {
      //     console.log(error);
      //     window.location.href = "#/list";
      // });
    };

    $scope.saveQuestionOption = function(optionId) {
      console.log(optionId)
      // $http({
      //     url : 'controllers/updateQuestionOption.php',
      //     method : 'post',
      //     data : $.param({"id" : optionId}),
      //     headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
      // })
      // .success(function(data, header, config) {
      //     window.location.href = "#/list";
      // })
      // .error(function(error, header, config) {
      //     console.log(error);
      //     window.location.href = "#/list";
      // });
    };

    $scope.deleteQuestion = function(questionId) {

    }

    $scope.deleteQuestionOption = function(optionId) {

    }

    $scope.switchIsSetSkip = function(item) {
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
      $('#hintDiv').hide();
    };

    $scope.cancel = function() {
      window.location.href = "#/list";
    };
}]);

app.factory('httpService', ['$http', function($http) {
  return {
    get : function(url, params, successCallback, errorCallback) {
        $http({
            url : url,
            method : 'get',
            data : $.param(params),
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

function tipWork() {
  $('#hintDiv').show(500);

  setTimeout(function() {
    $('#hintDiv').hide(2000);
  }, 1000);
}