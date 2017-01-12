<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $optionId = $_POST["id"];

    if ($optionId == '') {
    	echo json_encode(array("code" => 500));
    	return;
    }
    
	try {
	    $sql = "delete from questionOption where id = :optionId";

	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":optionId" => $optionId
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