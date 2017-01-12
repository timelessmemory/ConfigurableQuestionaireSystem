<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionId = $_POST["id"];

    if ($questionId == '') {
    	echo json_encode(array("code" => 500));
    	return;
    }
    
	try {
	    $db->query('set names utf8;');

	    $params =[
	        ":questionId" => $questionId
	    ];

	    $sql = "delete from question where id = :questionId";

	    $preparedStatement = $db->prepare($sql);

	    $result = $preparedStatement->execute($params);

	    if (!$result) {
	    	echo json_encode(array("code" => 500));
	    	return;
	    }

    	$sql = "delete from questionOption where questionId = :questionId";

    	$preparedStatement = $db->prepare($sql);

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