<?php
	require "../../config/config.php";
	
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $data = $_POST["answers"];
    $id = $_POST["id"];
    $mobile = $_POST["mobile"];
    $email = $_POST["email"];
    $agree = $_POST["agree"] == "true" ? true : false;
    
	try {
		$sql = "insert into answer(questionaireId, content, mobile, email, agree, answerTime) values(:questionaireId, :content, :mobile, :email, :agree, :answerTime)";

	    $dsn = "mysql:host=" . MYSQL_HOST . ";dbname=" . DB_NAME;
	    $db = new PDO($dsn, USERNAME, PASSWORD);
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":content" => $data,
	        ":questionaireId" => $id,
	        ":mobile" => $mobile,
	        ":email" => $email,
	        ":agree" => $agree,
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