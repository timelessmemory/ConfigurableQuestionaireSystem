<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];
    $mobile = $_POST["mobile"];
    
	try {
		$sql = "select * from answer where questionaireId = :questionaireId and mobile = :mobile";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":questionaireId" => $questionaireId,
	        ":mobile" => $mobile
	    ];

	    $preparedStatement->execute($params);
	    $answer = $preparedStatement->fetch(PDO::FETCH_ASSOC);

	    if (!$answer) {
	    	echo json_encode(array("result" => false));
	    } else {
	    	echo json_encode(array("result" => true));
	    }
	   
    } catch (Exception $e) {
        echo json_encode(array("result" => false));
    }
?>