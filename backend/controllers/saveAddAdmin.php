<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $brand = $_POST["brand"];
    $name = $_POST["name"];
    $password = $_POST["password"];
   	
	try {

		$sql = "insert into user(role, brand, name, password, createTime) values('brand_admin', :brand, :name, :password, :createTime)";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);
	    $createTime = date('Y-m-d H:i:s', time());

	    $params =[
	        ":brand" => $brand,
	        ":name" => $name,
	        ":password" => $password,
	        ":createTime" => $createTime
	    ];

	    $result = $preparedStatement->execute($params);

	    if (!$result) {
	    	echo json_encode(array("code" => 500));
	    	return;
	    }

	    echo json_encode(array("code" => 200, "id" => $db->lastInsertId(), "createTime" => $createTime));
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>