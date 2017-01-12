<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $id = $_GET["id"];

	try {
	    $sql = "select role, brand from user where id = :id";

	    $db->query('set names utf8;');

	    $params = [
	    	":id" => $id
	    ];

	    $preparedStatement = $db->prepare($sql);

	    $preparedStatement->execute($params);

	    $result = $preparedStatement->fetch(PDO::FETCH_ASSOC);
	    
	    echo json_encode(array("code" => 200, "role" => $result['role'], "brand" => $result['brand']));
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>