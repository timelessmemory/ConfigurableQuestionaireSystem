<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");

    $keyword = $_POST["keyword"];

    if ($keyword == '') {
    	echo json_encode(array("code" => 500));
    	return;
    }
    
	try {
	    $sql = "select * from questionaire where subject like :keyword";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $preparedStatement->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);

	    $preparedStatement->execute();

	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
	    
	    echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>