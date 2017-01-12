<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];
    $subject = $_POST["subject"];
    $brand = $_POST["brand"];
    $isProvicy = $_POST["isProvicy"] == "true" ? true : false;
    $required_fst = $_POST["required_fst"] == "true" ? true : false;
    $agree_fst = $_POST["agree_fst"];
    $required_snd = $_POST["required_snd"] == "true" ? true : false;
    $agree_snd = $_POST["agree_snd"];
    $description = $_POST["description"];
    
	try {
	    $sql = "update questionaire set subject = :subject, description = :description, brand = :brand, isProvicy = :isProvicy, required_fst = :required_fst, agree_fst = :agree_fst, required_snd = :required_snd, agree_snd = :agree_snd where id = :questionaireId";

	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params = [
	        ":questionaireId" => $questionaireId,
	        ":subject" => $subject,
	        ":brand" => $brand,
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