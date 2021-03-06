<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionId = $_POST["id"];
    $title = $_POST["title"];
    $isSingle = $_POST["isSingle"] == "true" ? true : false;
    
	try {
	    $sql = "update question set title = :title, isSingle = :isSingle where id = :questionId";

	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":questionId" => $questionId,
	        ":title" => $title,
	        ":isSingle" => $isSingle,
	    ];

	    $result = $preparedStatement->execute($params);
	    
	    if (!$result) {
	    	echo json_encode(array("code" => 500));
	    	return;
	    }

	    $isSetSkip = $_POST["isSetSkip"];

	    if ($isSetSkip == "true") {
	    	
	    	$pairs = $_POST["pairs"];

	    	if (!is_null($pairs)) {
	    		foreach ($pairs as $pair) {
	    			$opId = $pair['id'];
	    			$skipIndex = $pair['skipIndex'];
	    			$sql = "";
	    			$params = [];

	    			if (!$isSingle) {
	    				$isSkipOne = $pair['isSkipOne'] == 'true' ? true : false;

	    				$sql = "update questionOption set skipIndex = :skipIndex, isSkip = 1, isSkipOne = :isSkipOne where id = :optionId";
	    				
	    				$params = [
					        ":skipIndex" => $skipIndex,
					        ":isSkipOne" => $isSkipOne,
					        ":optionId" => $opId
					    ];
	    			} else {
	    				$sql = "update questionOption set skipIndex = :skipIndex, isSkip = 1 where id = :optionId";

	    				$params = [
					        ":skipIndex" => $skipIndex,
					        ":optionId" => $opId
					    ];
	    			}
	    			
	    			$preparedStatement = $db->prepare($sql);

				    $result = $preparedStatement->execute($params);

				    if (!$result) {
				    	echo json_encode(array("code" => 500));
				    	return;
				    }
	    		}
	    	}
	    } else {
	    	$optionIds = $_POST["optionIds"];

	    	if (!is_null($optionIds)) {
	    		foreach ($optionIds as $optionId) {
	    			$sql = "update questionOption set isSkip = 0 where id = :optionId";
	    			$preparedStatement = $db->prepare($sql);

				    $params =[
				        ":optionId" => $optionId
				    ];

				    $result = $preparedStatement->execute($params);

				    if (!$result) {
				    	echo json_encode(array("code" => 500));
				    	return;
				    }
	    		}
	    	}
	    }

	    echo json_encode(array("code" => 200));
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>