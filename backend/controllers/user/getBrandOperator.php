<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $brand = $_GET['brand'];
    $flag = $_GET['flag'];
    $name = $_GET['name'];

    $db->query('set names utf8;');

    if ($brand == "") {
    	$sql = "select * from user where role = 'brand_operator'";

    	$preparedStatement = $db->prepare($sql);

	    $preparedStatement->execute([]);
    } else if ($brand != "" && $name == "" && $flag == "") {
    	$sql = "select * from user where role = 'brand_operator' and brand = :brand";

    	$params = [
    		":brand" => $brand
    	];
    	
    	$preparedStatement = $db->prepare($sql);

	    $preparedStatement->execute($params);
    } else if ($brand != "" && $name == "" && $flag != "") {
        $sql = "select * from user where role = 'brand_operator' and brand like :brand";
        $preparedStatement = $db->prepare($sql);
        $preparedStatement->bindValue(':brand', '%' . $brand . '%', PDO::PARAM_STR);
        $preparedStatement->execute();
    } else if ($brand != "" && $name != "") {
        $sql = "select * from user where role = 'brand_operator' and brand = :brand and name like :name";
        $preparedStatement = $db->prepare($sql);
        $preparedStatement->bindValue(':brand', $brand, PDO::PARAM_STR);
        $preparedStatement->bindValue(':name', '%' . $name . '%', PDO::PARAM_STR);
        $preparedStatement->execute();
    }

	try {
	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
	    
	    echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>