<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionId = $_POST["id"];
    $title = $_POST["title"];
    $isSingle = $_POST["isSingle"];
    
	try {
	    $sql = "update question set title = :title, isSingle = :isSingle where id = :questionId";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
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
	    	$isChange = $_POST["isChange"];

	    	if ($isChange == "true") {
	    		$pairs = $_POST["pairs"];
	    		
	    		foreach ($pairs as $pair) {
	    			$opId = $pair['id'];
	    			$skipIndex = $pair['skipIndex'];
	    		}
	    	} else {
	    		$origingp1 = $_POST["origingp1"];

	    		if (!is_null($origingp1)) {
	    			$gp1 = $_POST["gp1"];
			    	$origingp2 = $_POST["origingp2"];
			    	$gp2 = $_POST["gp2"];
			    	echo $gp1;
	    		}
	    	}
	    } else {
	    	$optionIds = $_POST["optionIds"];

	    	if (!is_null($optionIds)) {
	    		foreach ($optionIds as $optionId) {
	    			echo $optionId;
	    		}
	    	}
	    }

	    echo json_encode(array("code" => 200));
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>