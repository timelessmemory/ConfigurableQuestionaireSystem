<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $questionaireId = $_POST["id"];

    if ($questionaireId == '') {
    	echo json_encode(array("code" => 500));
    	return;
    }
    
	try {
		$sql = "select * from questionaire where id = :questionaireId";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":questionaireId" => $questionaireId
	    ];

	    $preparedStatement->execute($params);
	    $questionaire = $preparedStatement->fetch(PDO::FETCH_ASSOC);

	    if (!$questionaire) {
	    	echo json_encode(array("code" => 500));
	    	return;
	    }

	    $sql = "select * from question where questionaireId = :questionaireId";

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

	    $questionaire['questions'] = $result;
	    
	    echo json_encode(array("code" => 200, "result" => $questionaire));
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>