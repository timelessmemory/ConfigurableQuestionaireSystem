<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $subject = $_POST["subject"];
    $description = $_POST["description"];
    $isProvicy = $_POST["isProvicy"];
    $required_fst = $_POST["required_fst"];
    $agree_fst = $_POST["agree_fst"];
    $required_snd = $_POST["required_snd"];
    $agree_snd = $_POST["agree_snd"];
    $questions = $_POST["questions"];
    
	try {
		$questionaireId = 0;
	    $sql = "insert into questionaire(subject, description, isProvicy, agree_fst, required_fst, agree_snd, required_snd, createTime) values(:subject, :description, :isProvicy, :agree_fst, :required_fst, :agree_snd, :required_snd, :createTime)";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":subject" => $subject,
	        ":description" => $description,
	        ":isProvicy" => $isProvicy,
	        ":required_fst" => $required_fst,
	        ":agree_fst" => $agree_fst,
	        ":required_snd" => $required_snd,
	        ":agree_snd" => $agree_snd,
	        ":createTime" => date('y-m-d H:i:s', time())
	    ];

	    $result = $preparedStatement->execute($params);
	    
	    if (!$result) {
	    	echo json_encode(array("code" => 500));
	    	return;
	    }

	    $questionaireId = $db->lastInsertId();
	    
	    foreach ($questions as $question) {
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
		    	$isNext = $option['isNext'] == 'true' ? true : false;
		    	$isCustOmized = $option['isCustOmized'] == 'true' ? true : false;
		    	$isSkip = $option['isSkip'] == 'true' ? true : false;
		    	$isSkipOne = $option['isSkipOne'] == 'true' ? true : false;
		    	$skipIndex = $option['skipIndex'];
		    	$content = $option['content'];

		    	$sql = "insert into questionOption(questionId, content, isHasNext, isCustomized, isSkip, isSkipOne, skipIndex) values(:questionId, :content, :isHasNext, :isCustomized, :isSkip, :isSkipOne, :skipIndex)";
		    	
		    	$preparedStatement = $db->prepare($sql);
		    	
		    	$params =[
			        ":questionId" => $questionId,
			        ":content" => $content,
			        ":isHasNext" => $isNext,
			        ":isCustomized" => $isCustOmized,
			        ":isSkip" => $isSkip,
			        ":isSkipOne" => $isSkipOne,
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