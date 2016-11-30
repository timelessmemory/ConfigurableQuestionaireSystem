var app = angular.module("backend", ['ngRoute']);

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
app.run(['$rootScope', '$window', '$location', '$templateCache', '$http', 'storeDataService', function ($rootScope, $window, $location, $templateCache, $http, storeDataService) {  
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
.controller("loginController", ['$scope', '$rootScope', '$http', 'storeDataService', '$timeout', function($scope, $rootScope, $http, storeDataService, $timeout) {
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

.controller("listController", ['$scope', '$http', '$document', '$timeout', 'storeDataService', function($scope, $http, $document, $timeout, storeDataService) {
    
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
          // angular.forEach($scope.questionaires, function(item) {
          //   if (item.id == num) {
          //     $scope.questionaire.id = num;
          //     $scope.questionaire.subject = item.subject;
          //     $scope.questionaire.description = item.description;
          //     $scope.questionaire.createTime = item.createTime;
          //     break;
          //   }
          // });

          // $scope.questionaire.questions = data;
          // console.log($scope.questionaire)
          // storeDataService.setQuestionaire($scope.questionaire);
          //  window.location.href = "#/detail";
      })
      .error(function(error, header, config, status) {
          console.log(error);
          window.location.href = "#/list";
      });
    };

    $scope.edit = function(num) {
      // query by id
      $http({
          url : '../basic/web/index.php?r=user/query-detail',
          method : 'post',
          data : $.param({ "id" : num }),
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
          responseType : 'json'
      })
      .success(function(data, header, config, status) {
          $scope.user = data.user;
          storeDataService.setUser($scope.user);
           window.location.href = "#/edit";
      })
      .error(function(error, header, config, status) {
          console.log(error);
          window.location.href = "#/list";
      });
    };

    $scope.delete = function(num) {
      //delete 
      $http({
          url : '../basic/web/index.php?r=user/delete-one',
          method : 'post',
          data : $.param({ "id" : num }),
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
      })
      .success(function(data, header, config, status) {
          $('#myModal').modal('hide');
          // queryAll($http, storeDataService, $scope );
          window.location.href = "#/list";
      })
      .error(function(error, header, config, status) {
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
        // queryAll($http, storeDataService, $scope );
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

.controller("detailController", ['$scope', 'storeDataService', function($scope, storeDataService) {
    $scope.user = storeDataService.getUser();

    $scope.edit = function() {

      window.location.href = "#/edit";
    };

    $scope.cancel = function() {
      window.location.href = "#/list";
    };
}])

.controller("editController", ['$scope', '$http', 'storeDataService', function($scope, $http, storeDataService) {

    $scope.user = storeDataService.getUser();

    $scope.save = function() {
      if ($scope.user.name == '' || $scope.user.name == undefined 
        || $scope.user.password == '' || $scope.user.password == undefined 
        || $scope.user.description == '' ||$scope.user.description == undefined) 
      return;
      //save
      $http({
          url : '../basic/web/index.php?r=user/save-one',
          method : 'post',
          data : $.param({ "id" : $scope.user._id.$id, 'name' : $scope.user.name, 'password' : $scope.user.password, 'description' : $scope.user.description}),
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
      })
      .success(function(data, header, config, status) {
          // queryAll($http, storeDataService, $scope );
          window.location.href = "#/list";
      })
      .error(function(error, header, config, status) {
          console.log(error);
          window.location.href = "#/list";
      });
    };

    $scope.cancel = function() {
      window.location.href = "#/detail";
    };
}])

.controller("createController", ['$scope', '$http', 'storeDataService', '$rootScope', function($scope, $http, storeDataService, $rootScope) {
    
    UE.getEditor('editor').ready(function() {
        this.focus();
        // alert(this.getContent())
        // alert(this.hasContents())
    });

    $scope.showHead = true;
    $scope.isSubmit = false;

    $scope.questionaire = {
      'subject' : '', 
      'description' : ''
    };

    $scope.questions = [{
      title : 'hello',
      isSingle : true,
      options : [{
        isNext : true,
        isCustOmized: false,
        content : 'A'
      }]
    }];

    $scope.currentIndex = 0;

    $scope.switchCheckBox = function($event, value) {
      // console.log(value)
      if (value) {
        $($event.target).addClass("checked");
      } else {
        $($event.target).removeClass("checked");
      }
    }

    $scope.addOption = function() {
      if (isOptionEmpty()) {
        $scope.tip = "选项不得为空!";
        tipWork();
        return;
      }

      $scope.questions[$scope.currentIndex].options.push({
        isNext : true,
        isCustOmized: false,
        content : ''
      });
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

    $scope.lastQues =function() {
      if (validateCurrentQuestion()) {
        $scope.tip = "请先完成当前问题!";
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

      ++$scope.currentIndex;

      if ($scope.currentIndex == $scope.questions.length) {
        $scope.questions.push({
          title : '',
          isSingle : true,
          options : [{
            isNext : true,
            isCustOmized: false,
            content : ''
          }]
        });
      }
    }

    $scope.createQuestion = function() {
      var description = UE.getEditor('editor').getContent();

      if ($scope.questionaire.subject == "" || description == "") {
        $scope.tip = "问卷主题或说明不得为空!";
        tipWork();
        return;
      }

      $scope.questionaire.description = description;
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
        // queryAll($http, storeDataService, $scope );
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
}]);

app.factory('storeDataService', function() {
  var questionaire = {'subject' : '', 'description' : '', 'createTime' : '', 'questions' : []};

  return {
    setQuestionaire : function(newQuestionaire) {
      questionaire = newQuestionaire;
    },
    getQuestionaire : function() {
      return questionaire;
    }
  };
});

// function queryAll(http, storeDataService, scope) {
//   http({
//       url : 'controllers/query.php',
//       method : 'get',
//       headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
//       responseType : 'json'
//   })
//   .success(function(data, header, config) {
//     storeDataService.setQuestionaireList(data);
//     console.log(data)
//     scope.questionaires = storeDataService.getQuestionaireList();
//   })
//   .error(function(error, header, config) {
//     console.log(error);
//   });
// }

function tipWork() {
  $('#hintDiv').show(500);

  setTimeout(function() {
    $('#hintDiv').hide(2000);
  }, 1000);
}