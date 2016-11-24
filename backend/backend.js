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

//clear route cache, reload user information
app.run(['$rootScope', '$window', '$location', '$templateCache', function ($rootScope, $window, $location, $templateCache) {  
    var routeChangeSuccessOff = $rootScope.$on('$routeChangeSuccess', routeChangeSuccess);  

    function routeChangeSuccess(event, f,t) {
        console.log(f.originalPath)
        $templateCache.removeAll();
    }  
}]); 

app
.controller("loginController", ['$scope', '$rootScope', '$http', 'storeDataService', function($scope, $rootScope, $http, storeDataService) {
    $scope.username = '';
    $scope.password = '';

    $scope.login = function() {
      if ($scope.username != '' && $scope.password != '') {
        // verify login
        $http({
            url : '../basic/web/index.php?r=user/verify-login',
            method : 'POST',
            data : $.param({ "name" : $scope.username, "password" : $scope.password} ),
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
            responseType : 'json'
        })
        .success(function(data, header, config, status) {
            var isPass = data.result;

            if (!isPass) {
              //hint
              $('#hintDiv').show();
              return;
            }

            queryAll($http, storeDataService, $scope);
        })
        .error(function(error, header, config, status) {
            window.location.href = "#/";
            console.log(error);
        });
      }
    };

    $scope.close = function() {
      $('#hintDiv').hide();
    };

    $rootScope.logout = function () {
      $http({
          url : '../basic/web/index.php?r=user/logout',
          method : 'get'
      })
      .error(function(error, header, config, status) {
          console.log(error);
          window.location.href = "#/list";
      });
    };
}])

.controller("listController", ['$scope', '$http', '$document', '$timeout', 'storeDataService', function($scope, $http, $document, $timeout, storeDataService) {
    $scope.users = storeDataService.getUserList();
    $scope.titles = storeDataService.getTableTitle();
    $scope.userId = 0;
    $scope.user = {};
    $scope.keyword = '';
    $scope.isCheckeds = [];

    $document.bind("keypress", function(event) {
        if (event.keyCode == 13) {
            $scope.search();
        }
    });

    $scope.detail = function(num) {
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
           window.location.href = "#/detail";
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
          queryAll($http, storeDataService, $scope );
      })
      .error(function(error, header, config, status) {
          console.log(error);
          window.location.href = "#/list";
      });
    };

    $scope.sendUserId = function(userId) {
      $scope.userId = userId;
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
        queryAll($http, storeDataService, $scope );
      }
    };

    //delay realTime search
    var timeout;
    $scope.$watch('searchWord', function(newVal) {
      if (newVal) {
        if (timeout) $timeout.cancel(timeout);
        timeout = $timeout(function() {
          $scope.realTimeRearch();
        }, 350);
      }
    });

    $scope.realTimeRearch = function() {
      if ($scope.searchWord != '') {
        //search
         $http({
          url : '../basic/web/index.php?r=user/search-condition',
          method : 'post',
          data : $.param({ "keyword" : $scope.searchWord}),
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
          })
          .success(function(data, header, config, status) {
              $scope.users = data.users;
          })
          .error(function(error, header, config, status) {
              console.log(error);
              window.location.href = "#/list";
          });
      } else {
        queryAll($http, storeDataService, $scope );
      }
    };
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
          queryAll($http, storeDataService, $scope );
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

.controller("createController", ['$scope', '$http', 'storeDataService', function($scope, $http, storeDataService) {
   $scope.user = {'name' : '', 'password' : '', 'description' : ''};

    $scope.cancel = function() {
      window.location.href = "#/list";
    };

    $scope.create = function() {
      if ($scope.user.name == '' || $scope.user.password == '' || $scope.user.description == '') return;
      //insert
      $http({
          url : '../basic/web/index.php?r=user/create-one',
          method : 'post',
          data : $.param({'name' : $scope.user.name, 'password' : $scope.user.password, 'description' : $scope.user.description}),
          headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
      })
      .success(function(data, header, config, status) {
          queryAll($http, storeDataService, $scope );
      })
      .error(function(error, header, config, status) {
          console.log(error);
          window.location.href = "#/list";
      });
    };
}]);

app.factory('storeDataService', function() {
  var userList = [];
  var title = [];
  var user = {'name' : '', 'password' : '', 'description' : ''};

  return {
    setUserList: function(newUsername) { 
      userList = newUsername; 
    },
    getUserList: function() { 
      return userList; 
    },
    getTableTitle: function() {
      if (userList.length > 0) {
         var item = userList[0];
         for(var i in item) {
           title.push(i);
         }
      }
      return title;
    },
    setUser : function(newUser) {
      user = newUser;
    },
    getUser : function() {
      return user;
    }
  };
});

function queryAll(http, storeDataService, scope) {
  http({
      url : '../basic/web/index.php?r=user/query-list',
      method : 'get',
      headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
      responseType : 'json'
  })
  .success(function(data, header, config, status) {
    storeDataService.setUserList(data.users);

    scope.users = storeDataService.getUserList();

    window.location.href = "#/list";
  })
  .error(function(error, header, config, status) {
    console.log(error);
    window.location.href = "#/";
  });
}