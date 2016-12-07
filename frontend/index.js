var frontend = angular.module('frontend', []);

frontend
.controller('mainController', ['$scope', '$http', function($scope, $http) {
	var questionaireId = location.search.substr(1).substring(15);

	$scope.isStart = true;
	$scope.isEnd = false;

	$scope.tmp = {
		isAgree : false
	}

	$scope.formData = {
		isAgree : false
	}

	var answers = []

	$scope.questions = [];

	$scope.qs = {
		answer : ""
	}

	$scope.questionIndex = 0;

	$scope.questionLastIndexs = [];

	$scope.selecteds = [];

	$http({
        url : 'controllers/queryone.php',
        method : 'post',
        data : $.param({ "id" : questionaireId }),
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
        $scope.questions = $scope.questionaire.questions;
    })
    .error(function(error, header, config, status) {
        console.log(error);
    });

    $scope.start = function() {
    	if (!$scope.formData.isAgree || !$scope.tmp.isAgree) {
    		$scope.tip = "请同意以上说明!";
            tipWork();
            return;
    	}

    	$scope.isStart = true;
    }

    $scope.isSelect = function(option, options) {
    	angular.forEach(options, function(item) {
    		item.isSelect = false;
    	})

    	option.isSelect = true;
    }

    $scope.operate = function(event, content) {
    	if (event.target.checked)  {
    		$scope.selecteds.push(content);
    	} else {
    		var index = $scope.selecteds.indexOf(content);
    		$scope.selecteds.splice(index, 1);
    	}
    	// console.log($scope.selecteds)
    }

    $scope.isCheck = function(content) {
    	if ($scope.selecteds.indexOf(content) > -1) {
    		return true;
    	}

    	return false;
    }

    $scope.next = function(option) {
    	if ($scope.questions[$scope.questionIndex].isSingle == '1') {
    		//single
    		if ($scope.qs.answer == "") {
    			$scope.tip = "请选择选项!";
            	tipWork();
            	return;
    		}

    		var questionTitle = $scope.questions[$scope.questionIndex].title;
    		var isExist = false;

    		angular.forEach(answers, function(item) {
    			if (item.question == questionTitle) {
    				item.answer = $scope.qs.answer;
    				isExist = true;
    			}
    		})

    		if (!isExist) {
	    		var answer = {
	    			question : questionTitle,
	    			answer : $scope.qs.answer
	    		}

	    		answers.push(answer);
    		}

    		$scope.qs.answer = "";

    		if ($scope.questionIndex != $scope.questions.length - 1) {
    			if (option.isSkip == '1') {

    				if (option.skipIndex > $scope.questions.length || option.skipIndex <= ($scope.questionIndex + 1)) {
    					$scope.tip = "问卷跳题设置不正确，请修正!";
		            	tipWork();
		            	return;
    				}

    				$scope.questionLastIndexs.push($scope.questionIndex);

    				$scope.questionIndex = option.skipIndex - 1;

	    		} else if (option.isHasNext == '1') {
	    			$scope.questionLastIndexs.push($scope.questionIndex);

    				$scope.questionIndex++;
	    		} else {
	    			$scope.isEnd = true;
	    		}
    		} else {
    			$scope.isEnd = true;
    		}
    	} else {
    		//multi-select
    		if ($scope.qs.answer != "") {
    			$scope.selecteds.push($scope.qs.answer)
    		}

    		if ($scope.selecteds.length == 0) {
    			$scope.tip = "请选择选项!";
            	tipWork();
            	return;
    		}

    	}
    }

    $scope.back = function() {
    	$scope.questionIndex = $scope.questionLastIndexs.pop();
    }

    $scope.close = function() {
      $('#hintDiv').fadeOut();
    };
}])

.filter("to_html", ['$sce', function($sce) {
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