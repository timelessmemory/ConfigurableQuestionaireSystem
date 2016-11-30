<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
	try {
	    $sql = "select * from questionaire";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $preparedStatement->execute([]);

	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
	    
	    echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>