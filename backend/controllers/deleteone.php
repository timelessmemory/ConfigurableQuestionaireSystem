<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];

    if ($questionaireId == '') {
    	echo json_encode(array("code" => 500));
    	return;
    }
    
	try {
	    $sql = "delete from question where questionaireId = :questionaireId";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":questionaireId" => $questionaireId
	    ];

	    $result = $preparedStatement->execute($params);
	    
	    echo $result;
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>