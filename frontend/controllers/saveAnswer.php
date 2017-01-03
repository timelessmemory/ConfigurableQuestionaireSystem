<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $data = $_POST["data"];
    $id = $_POST["id"];
    
	try {
		$sql = "insert into answer(questionaireId, content, answerTime) values(:questionaireId, :content, :answerTime)";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":content" => $data,
	        ":questionaireId" => $id,
	        ":answerTime" => date('Y-m-d H:i:s', time())
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