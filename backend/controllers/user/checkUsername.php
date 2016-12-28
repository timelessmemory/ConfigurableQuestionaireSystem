<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $name = $_GET['name'];

	try {
	    $sql = "select * from user where name = :name";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params = [
	    	":name" => $name
	    ];

	    $preparedStatement->execute($params);

	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);

	    if (count($result) == 0) {
	    	echo json_encode(array("result" => false));;
	    } else {
	    	echo json_encode(array("result" => true));
	    }
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>