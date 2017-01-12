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

require "../../config/config.php";

$dsn = "mysql:host=" . MYSQL_HOST . ";dbname=" . DB_NAME;
$db = new PDO($dsn, USERNAME, PASSWORD);

include($module . '/' . $action . '.php');
?>