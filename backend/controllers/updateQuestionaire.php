<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];
    $subject = $_POST["subject"];
    $isProvicy = $_POST["isProvicy"];
    $required_fst = $_POST["required_fst"];
    $agree_fst = $_POST["agree_fst"];
    $required_snd = $_POST["required_snd"];
    $agree_snd = $_POST["agree_snd"];
    $description = $_POST["description"];
    
	try {
	    $sql = "update questionaire set subject = :subject, description = :description, isProvicy = :isProvicy, required_fst = :required_fst, agree_fst = :agree_fst, required_snd = :required_snd, agree_snd = :agree_snd where id = :questionaireId";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":questionaireId" => $questionaireId,
	        ":subject" => $subject,
	        ":isProvicy" => $isProvicy,
	        ":required_fst" => $required_fst,
	        ":agree_fst" => $agree_fst,
	        ":required_snd" => $required_snd,
	        ":agree_snd" => $agree_snd,
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