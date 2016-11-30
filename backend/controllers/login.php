<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $name = $_POST["name"];
    $password = $_POST["password"];

    if ($name == '' or $password == '') {
    	echo json_encode(array("code" => 500));
    	return;
    }
    
	try {
	    $sql = "select name from user where name = :name and password = :password";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":name" => $name,
	        ":password" => $password,
	    ];

	    $preparedStatement->execute($params);
	    $result = $preparedStatement->fetch(PDO::FETCH_ASSOC);

	    if ($result == "") {
	    	echo json_encode(array("code" => 500));
	    } else {
	    	session_start();
	    	$_SESSION['username'] = $name;
	    	echo json_encode(array("code" => 200));
	    }
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>