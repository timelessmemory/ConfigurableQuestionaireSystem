<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];
    $addQuestions = $_POST["addQuestions"];
    
	try {
	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');
	    
	    foreach ($addQuestions as $question) {
	    	$title = $question['title'];
	    	$isSingle = $question['isSingle'] == 'true' ? true : false;
	    	$options = $question['options'];

	    	$sql = "insert into question(questionaireId, title, isSingle) values(:questionaireId, :title, :isSingle)";
	    	
	    	$preparedStatement = $db->prepare($sql);
	    	
	    	$params =[
		        ":questionaireId" => $questionaireId,
		        ":title" => $title,
		        ":isSingle" => $isSingle
		    ];

		    $result = $preparedStatement->execute($params);

		    if (!$result) {
		    	echo json_encode(array("code" => 500));
		    	return;
		    }

		    $questionId = $db->lastInsertId();

		    foreach ($options as $option) {
		    	$isNext = $option['isHasNext'] == 'true' ? true : false;
		    	$isCustOmized = $option['isCustomized'] == 'true' ? true : false;
		    	$isSkip = $option['isSkip'] == 'true' ? true : false;
		    	$skipIndex = $option['skipIndex'];
		    	$content = $option['content'];

		    	$sql = "insert into questionOption(questionId, content, isHasNext, isCustomized, isSkip, skipIndex) values(:questionId, :content, :isHasNext, :isCustomized, :isSkip, :skipIndex)";
		    	
		    	$preparedStatement = $db->prepare($sql);
		    	
		    	$params =[
			        ":questionId" => $questionId,
			        ":content" => $content,
			        ":isHasNext" => $isNext,
			        ":isCustomized" => $isCustOmized,
			        ":isSkip" => $isSkip,
			        ":skipIndex" => $skipIndex

			    ];

			    $result = $preparedStatement->execute($params);

			    if (!$result) {
			    	echo json_encode(array("code" => 500));
			    	return;
			    }
		    }
	    }

	    echo json_encode(array("code" => 200));
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>