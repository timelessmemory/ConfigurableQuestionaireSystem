var frontend = angular.module('frontend', []);

frontend
.controller('mainController', ['$scope', '$http', function($scope, $http) {
	var questionaireId = location.search.substr(1).substring(15);

	$scope.valid = true;
	$scope.isStart = false;
	$scope.isEnd = false;
	$scope.isSubmit = false;
	$scope.final = false;

	var backIndex = [];

	$scope.tmp = {
		isAgree : false
	}

	$scope.formData = {
		isAgree : false
	}

	var answers = []

	$scope.questions = [];

	$scope.qs = {
		answer : "",
		custSingle : ""
	}

	$scope.questionIndex = 0;

	var questionLastIndexs = [];

	var selecteds = [];

	$http({
        url : 'controllers/queryone.php',
        method : 'post',
        data : $.param({ "id" : questionaireId }),
        headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
        responseType : 'json'
    })
    .success(function(data, header, config) {
        if (data.code == 500) {
          $scope.valid = false;
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
    		selecteds.push(content);
    	} else {
    		var index = selecteds.indexOf(content);
    		selecteds.splice(index, 1);
    	}
    	// console.log(selecteds)
    }

    $scope.isCheck = function(content) {
    	if (selecteds.indexOf(content) > -1) {
    		return true;
    	}

    	return false;
    }

    $scope.next = function() {

    	if ($scope.questions[$scope.questionIndex].isSingle == '1') {
    		//single

    		if (backIndex.length != 0 && $scope.qs.custSingle == "" && $scope.qs.answer == "") {
    			questionLastIndexs.push($scope.questionIndex);
    			$scope.questionIndex = backIndex.pop();

    			//show answer
    			if ($scope.questions[$scope.questionIndex].isSingle != '1') {
		    		selecteds = getMultiAns($scope.questions[$scope.questionIndex].title, answers);

		    		angular.forEach(selecteds, function(select) {
		    			var isIn = false;
		    			angular.forEach($scope.questions[$scope.questionIndex].options, function(option) {
		    				if (option.content == select) {
		    					isIn = true;
		    				}
		    			})

		    			if (!isIn) {
		    				$scope.qs.answer = select;
		    			}
		    		})
		    	} else {
		    		singleAns = getMultiAns($scope.questions[$scope.questionIndex].title, answers);
		    		
		    		var isIn = false;
		    		
		    		angular.forEach($scope.questions[$scope.questionIndex].options, function(option) {
						if (option.content == singleAns) {
							isIn = true;
						}
					})

					if (!isIn) {
						$scope.qs.custSingle = singleAns;
						angular.forEach($scope.questions[$scope.questionIndex].options, function(item) {
				    		item.isSelect = false;
				    	})
					}
		    	}

    			return;
    		}

    		var option = "";
    		var singleAnswer = '';

    		if ($scope.qs.custSingle != "") {
    			singleAnswer = $scope.qs.custSingle;

    			angular.forEach($scope.questions[$scope.questionIndex].options, function(item) {
    				if (item.isCustomized == '1') {
    					option = item;
    				}
    			})
    		} else {
    			if ($scope.qs.answer == "") {
	    			$scope.tip = "请选择选项!";
	            	tipWork();
	            	return;
	    		}

	    		singleAnswer = $scope.qs.answer;

	    		angular.forEach($scope.questions[$scope.questionIndex].options, function(item) {
	    			if (item.content == singleAnswer) {
	    				option = item;
	    			}
	    		})
    		}

    		var questionTitle = $scope.questions[$scope.questionIndex].title;
    		var isExist = false;

    		angular.forEach(answers, function(item) {
    			if (item.question == questionTitle) {
    				item.answer = singleAnswer;
    				isExist = true;
    			}
    		})

    		if (!isExist) {
	    		var answer = {
	    			question : questionTitle,
	    			answer : singleAnswer
	    		}
	    		answers.push(answer);
    		}

    		if ($scope.questionIndex != $scope.questions.length - 1) {
    			
    			if (option.isSkip == '1') {

    				if (option.skipIndex > $scope.questions.length || option.skipIndex <= ($scope.questionIndex + 1)) {
    					$scope.tip = "问卷跳题设置不正确，请修正!";
		            	tipWork();
		            	return;
    				}

    				$scope.qs.answer = "";
    				$scope.qs.custSingle = "";

    				questionLastIndexs.push($scope.questionIndex);

    				$scope.questionIndex = option.skipIndex - 1;

	    		} else if (option.isHasNext == '1') {
	    			$scope.qs.answer = "";
    				$scope.qs.custSingle = "";
	    			
	    			questionLastIndexs.push($scope.questionIndex);

    				$scope.questionIndex++;
	    		} else {
	    			$scope.isEnd = true;
	    		}
    		} else {
    			$scope.isSubmit = true;
    		}
    	} else {
    		//multi-select

    		if (backIndex.length != 0 && selecteds.length == 0 && $scope.qs.answer == "") {
    			questionLastIndexs.push($scope.questionIndex);
    			$scope.questionIndex = backIndex.pop();

    			//show answer
    			if ($scope.questions[$scope.questionIndex].isSingle != '1') {
		    		selecteds = getMultiAns($scope.questions[$scope.questionIndex].title, answers);

		    		angular.forEach(selecteds, function(select) {
		    			var isIn = false;
		    			angular.forEach($scope.questions[$scope.questionIndex].options, function(option) {
		    				if (option.content == select) {
		    					isIn = true;
		    				}
		    			})

		    			if (!isIn) {
		    				$scope.qs.answer = select;
		    			}
		    		})
		    	} else {
		    		singleAns = getMultiAns($scope.questions[$scope.questionIndex].title, answers);
		    		
		    		var isIn = false;
		    		
		    		angular.forEach($scope.questions[$scope.questionIndex].options, function(option) {
						if (option.content == singleAns) {
							isIn = true;
						}
					})

					if (!isIn) {
						$scope.qs.custSingle = singleAns;
						angular.forEach($scope.questions[$scope.questionIndex].options, function(item) {
				    		item.isSelect = false;
				    	})
					}
		    	}

    			return;
    		}

    		if (selecteds.length == 0 && $scope.qs.answer == "") {
    			$scope.tip = "请选择选项!";
            	tipWork();
            	return;
    		}

    		var options = [];

    		if ($scope.qs.answer != "") {
    			angular.forEach($scope.questions[$scope.questionIndex].options, function(item) {
	    			if (item.isCustomized == "1") {
	    				options.push(item)
	    			}
	    		})
    		}

    		angular.forEach(selecteds, function(select) {
    			angular.forEach($scope.questions[$scope.questionIndex].options, function(item) {
	    			if (item.content == select) {
	    				options.push(item)
	    			}
	    		})
    		})

    		if ($scope.qs.answer != "") {
    			selecteds.push($scope.qs.answer)
    		}

    		var questionTitle = $scope.questions[$scope.questionIndex].title;
    		var isExist = false;

    		angular.forEach(answers, function(item) {
    			if (item.question == questionTitle) {
    				item.answer = selecteds;
    				isExist = true;
    			}
    		})

    		if (!isExist) {
	    		var answer = {
	    			question : questionTitle,
	    			answer : selecteds
	    		}

	    		answers.push(answer);
    		}

    		if ($scope.questionIndex != $scope.questions.length - 1) {
    			var skipIndex = 0, iscontinue = true;

    			angular.forEach(options, function(option) {
    				if (option.isSkip == '1') {
    					
    					if (option.isSkipOne == "1" && iscontinue) {
    						skipIndex = option.skipIndex;
    						iscontinue = false;
    					}

    					if (iscontinue) {
    						skipIndex = option.skipIndex;
    					}
    				}
    			})

    			if (skipIndex != 0) {
    				if (skipIndex > $scope.questions.length || skipIndex <= ($scope.questionIndex + 1)) {
    					$scope.tip = "问卷跳题设置不正确，请修正!";
		            	tipWork();
		            	return;
    				}
    				$scope.qs.answer = "";
    				selecteds = [];

    				questionLastIndexs.push($scope.questionIndex);

    				$scope.questionIndex = option.skipIndex - 1;
    				return;
    			}

    			var isHasNext = false;

    			angular.forEach(options, function(option) {
    				if (option.isHasNext == '1') {
    					isHasNext = true;
    				}
    			})

    			if (isHasNext) {
    				$scope.qs.answer = "";
    				selecteds = [];

	    			questionLastIndexs.push($scope.questionIndex);

    				$scope.questionIndex++;
    				return;
	    		}

	    		$scope.isEnd = true;
    		} else {
    			$scope.isSubmit = true;
    		}
    	}
    }

    function getMultiAns(questionTitle, answers) {
    	var ans = [];
    	angular.forEach(answers, function(answer) {
    		if (answer.question == questionTitle) {
    			ans = answer.answer;
    		}
    	})
    	return ans;
    }
 
    $scope.back = function() {
    	//clear data
    	if ($scope.qs.answer != "" || $scope.qs.custSingle != "") {
    		angular.forEach($scope.questions[$scope.questionIndex].options, function(item) {
	    		item.isSelect = false;
	    	})
    	}

    	$scope.qs.answer = "";
    	$scope.qs.custSingle = "";
    	selecteds = [];

    	backIndex.push($scope.questionIndex);
    	$scope.questionIndex = questionLastIndexs.pop();

    	//show answer
    	if ($scope.questions[$scope.questionIndex].isSingle != '1') {
    		selecteds = getMultiAns($scope.questions[$scope.questionIndex].title, answers);

    		angular.forEach(selecteds, function(select) {
    			var isIn = false;
    			angular.forEach($scope.questions[$scope.questionIndex].options, function(option) {
    				if (option.content == select) {
    					isIn = true;
    				}
    			})

    			if (!isIn) {
    				$scope.qs.answer = select;
    			}
    		})
    	} else {
    		singleAns = getMultiAns($scope.questions[$scope.questionIndex].title, answers);
    		
    		var isIn = false;
    		angular.forEach($scope.questions[$scope.questionIndex].options, function(option) {
				if (option.content == singleAns) {
					isIn = true;
				}
			})

			if (!isIn) {
				$scope.qs.custSingle = singleAns;
				angular.forEach($scope.questions[$scope.questionIndex].options, function(item) {
		    		item.isSelect = false;
		    	})
			}
    	}
    }

    $scope.submit = function() {
    	if ($scope.formData.name == "" || $scope.formData.mobile == "" || $scope.formData.email == "") {
    		$scope.tip = "请填写完整信息!";
            tipWork();
            return;
    	}

    	var regMobile = /^\d{8}$/;
        var regEmail = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;

        if (!regMobile.test($scope.formData.mobile)) {
            $scope.tip = "手机号格式不正确!";
            tipWork();
            return;
        }

        if (!regEmail.test($scope.formData.email)) {
            $scope.tip = "邮箱格式不正确!";
            tipWork();
            return;
        }

    	if (!$scope.formData.agree) {
    		$scope.tip = "请同意以上说明!";
            tipWork();
            return;
    	}

    	$scope.formData.answers = answers;

    	saveData($scope.questionaire.id, $scope.formData, function(data, header, config) {
	        if (data.code == 500) {
	          $scope.valid = false;
	          $scope.tip = "请求错误,请稍后再试!";
	          tipWork();
	          return; 
	        }
	        
	        $scope.isSubmit = false;
	        $scope.final = true;
	    });
    }

    $scope.submitBreak = function() {
    	$scope.formData.answers = answers;

    	saveData($scope.questionaire.id, $scope.formData, function(data, header, config) {
	        if (data.code == 500) {
	          $scope.valid = false;
	          $scope.tip = "请求错误,请稍后再试!";
	          tipWork();
	          return; 
	        }
	        
	        $scope.isEnd = false;
	        $scope.final = true;
	    });
    }

    $scope.return = function() {
    	$scope.isEnd = false;
    	$scope.isStart = true;
    }

    $scope.returnBack = function() {
    	$scope.isSubmit = false;
    	$scope.isStart = true;
    }

    $scope.close = function() {
      $('#hintDiv').fadeOut();
    };

    function saveData(id, data, successCallBack) {
    	$http({
	        url : 'controllers/saveAnswer.php',
	        method : 'post',
	        data : $.param({ "data" : data, "id" : id}),
	        headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
	        responseType : 'json'
	    })
	    .success(successCallBack)
	    .error(function(error, header, config) {
	        console.log(error);
	    });
    }
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