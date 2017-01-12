<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];

    if ($questionaireId == '') {
    	echo json_encode(array("code" => 500));
    	return;
    }
    
	try {
		//delete questionaire
	    $sql = "delete from questionaire where id = :questionaireId";

	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":questionaireId" => $questionaireId
	    ];

	    $result = $preparedStatement->execute($params);
	    
	    if (!$result) {
	    	echo json_encode(array("code" => 500));
	    	return;
	    }

	    //query questionIds to delete question options
	    $sql = "select id from question where questionaireId = :questionaireId";

	    $preparedStatement = $db->prepare($sql);

	    $preparedStatement->execute($params);

	    $questionIds = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);

	    if (gettype($questionIds) != "array" || count($questionIds) <= 0) {
	    	echo json_encode(array("code" => 500));
	    	return;
	    }

	    //delete question title and params
	    $sql = "delete from question where questionaireId = :questionaireId";

	    $preparedStatement = $db->prepare($sql);

	    $result = $preparedStatement->execute($params);

	    if (!$result) {
	    	echo json_encode(array("code" => 500));
	    	return;
	    }

	    //delete options by questionId
	    foreach ($questionIds as $question) {
	    	$questionId = $question['id'];

	    	$sql = "delete from questionOption where questionId = :questionId";

	    	$preparedStatement = $db->prepare($sql);

	    	$params =[
		        ":questionId" => $questionId
		    ];

		    $result = $preparedStatement->execute($params);

		    if (!$result) {
		    	echo json_encode(array("code" => 500));
		    	return;
		    }
	    }

	    echo json_encode(array("code" => 200));
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>