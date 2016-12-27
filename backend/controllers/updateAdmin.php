<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $id = $_POST["id"];
    $brand = $_POST["brand"];
    $name = $_POST["name"];
    $password = $_POST["password"];
    
	try {
	    $sql = "update user set brand = :brand, name = :name, password = :password where id = :id";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":id" => $id,
	        ":brand" => $brand,
	        ":name" => $name,
	        ":password" => $password
	    ];

	    $result = $preparedStatement->execute($params);
	    
	    if (!$result) {
	    	echo json_encode(array("code" => 500));
	    	return;
	    }

	    echo json_encode(array("code" => 200));
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>