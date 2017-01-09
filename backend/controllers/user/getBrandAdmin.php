<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $brand = $_GET['brand'];

    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
    $db = new PDO($dsn, 'root', 'root');
    $db->query('set names utf8;');

    if ($brand == "") {
    	$sql = "select * from user where role = 'brand_admin'";

	    $preparedStatement = $db->prepare($sql);

	    $preparedStatement->execute([]);
    } else {
    	$sql = "select * from user where role = 'brand_admin' and brand like :brand";
    	
    	$preparedStatement = $db->prepare($sql);

        $preparedStatement->bindValue(':brand', '%' . $brand . '%', PDO::PARAM_STR);

	    $preparedStatement->execute();
    }
	
	try {
	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
	    
	    echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>