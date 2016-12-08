<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionId = $_POST["id"];
    $addOptions = $_POST["addOptions"];
    
	try {
		foreach ($addOptions as $option) {
			$content = $option['content'];
			$isHasNext = $option['isHasNext'] == "true" ? true : false;
			$isCustomized = $option['isCustomized'] == "true" ? true : false;
			$isSkip = $option['isSkip'] == "true" ? true : false;
			$isSkipOne = $option['isSkipOne'] == "true" ? true : false;
			$skipIndex = $option['skipIndex'];

			$sql = "insert into questionOption(content, isHasNext, isCustomized, isSkip, isSkipOne, skipIndex, questionId) values(:content, :isHasNext, :isCustomized, :isSkip, :isSkipOne, :skipIndex, :questionId)";

		    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
		    $db = new PDO($dsn, 'root', 'root');
		    $db->query('set names utf8;');

		    $preparedStatement = $db->prepare($sql);

		    $params =[
		        ":content" => $content,
		        ":isHasNext" => $isHasNext,
		        ":isCustomized" => $isCustomized,
		        ":isSkip" => $isSkip,
		        ":isSkipOne" => $isSkipOne,
		        ":skipIndex" => $skipIndex,
		        ":questionId" => $questionId,
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