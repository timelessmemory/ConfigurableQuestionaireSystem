<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];
    $subject = $_POST["subject"];
    $description = $_POST["description"];
    
	try {
	    $sql = "update questionaire set subject = :subject, description = :description where id = :questionaireId";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":questionaireId" => $questionaireId,
	        ":subject" => $subject,
	        ":description" => $description,
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