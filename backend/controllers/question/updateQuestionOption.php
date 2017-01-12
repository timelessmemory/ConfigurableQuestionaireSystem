<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $optionId = $_POST["id"];
    $content = $_POST["content"];
    $isHasNext = $_POST["isHasNext"] == "true" ? true : false;
    $isCustomized = $_POST["isCustomized"] == "true" ? true : false;
    
	try {
	    $sql = "update questionOption set content = :content, isHasNext = :isHasNext, isCustomized = :isCustomized where id = :optionId";

	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":content" => $content,
	        ":isHasNext" => $isHasNext,
	        ":isCustomized" => $isCustomized,
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