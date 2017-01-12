<?php
	require "../../config/config.php";

	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];
    $mobile = $_POST["mobile"];
    
	try {
		$sql = "select * from answer where questionaireId = :questionaireId and mobile = :mobile";

	    $dsn = "mysql:host=" . MYSQL_HOST . ";dbname=" . DB_NAME;
	    $db = new PDO($dsn, USERNAME, PASSWORD);
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