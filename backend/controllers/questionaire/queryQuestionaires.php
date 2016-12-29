<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $brand = $_GET['brand'];

    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
    $db = new PDO($dsn, 'root', 'root');
    $db->query('set names utf8;');

    if ($brand) {
    	$sql = "select * from questionaire where brand = :brand";

    	$params = [
    		":brand" => $brand
    	];

    	$preparedStatement = $db->prepare($sql);

	    $preparedStatement->execute($params);
    } else {
    	$sql = "select * from questionaire";

    	$preparedStatement = $db->prepare($sql);

	    $preparedStatement->execute([]);
    }

	try {
	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
	    
	    echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>