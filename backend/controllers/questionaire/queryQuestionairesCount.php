<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $brand = $_POST['brand'];
    $keyword = $_POST['keyword'];

    $db->query('set names utf8;');

    if ($brand == "" && $keyword == "") {
    	$sql = "select count(*) as total from questionaire";
    	$preparedStatement = $db->prepare($sql);
	    $preparedStatement->execute([]);
    } else if ($brand != "" && $keyword == "") {
    	$sql = "select count(*) as total from questionaire where brand = :brand";

        $params = [
            ":brand" => $brand
        ];

    	$preparedStatement = $db->prepare($sql);
	    $preparedStatement->execute($params);
    } else if ($brand == "" && $keyword != "") {
        $sql = "select count(*) as total from questionaire where subject like :keyword";
        $preparedStatement = $db->prepare($sql);
        $preparedStatement->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        $preparedStatement->execute();
    } else if ($brand != "" && $keyword != "") {
        $sql = "select count(*) as total from questionaire where brand = :brand and subject like :keyword";
        $preparedStatement = $db->prepare($sql);
        $preparedStatement->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        $preparedStatement->bindValue(':brand', $brand, PDO::PARAM_STR);
        $preparedStatement->execute();
    }

	try {
	    $result = $preparedStatement->fetch(PDO::FETCH_ASSOC);
	    
	    echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>