<?php
session_start();

$module = $_GET['module'];
$action = $_GET['action'];

if (!$_SESSION['username'] && $module == "user" && $action != "login") {
	echo json_encode(array("code" => 500, "result" => "not login"));
	return;
}

if ($module == "" || $action == "") {
	echo json_encode(array("code" => 500, "result" => "error params"));
	return;
}

include($module . '/' . $action . '.php');
?>