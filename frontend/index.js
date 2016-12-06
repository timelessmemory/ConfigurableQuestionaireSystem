console.log(location.search.substr(1));

var frontend = angular.module('frontend', []);

frontend.controller('mainController', function($scope) {
	$scope.name = "hello";
});