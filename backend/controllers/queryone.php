<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];
    if ($questionaireId == '') {
    	echo json_encode(array("code" => 500));
    	return;
    }
    
	try {
	    $sql = "select * from question where questionaireId = :questionaireId";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":questionaireId" => $questionaireId
	    ];

	    $preparedStatement->execute($params);
	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);

	    foreach ($result as $index => $question) {
	    	$questionId = $question['id'];

	    	$sql = "select * from questionOption where questionId = :questionId";

		    $preparedStatement = $db->prepare($sql);

		    $params =[
		        ":questionId" => $questionId
		    ];

		    $preparedStatement->execute($params);

		    $options = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);

		    $result[$index]['options'] = $options;
	    }
	    
	    echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>