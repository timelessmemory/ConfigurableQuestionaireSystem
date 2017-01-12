<?php
	header('Content-Type: application/json');
    header("Content-Type:text/html;charset=UTF-8");
    
    $brand = $_POST['brand'];
    $keyword = $_POST['keyword'];
    $start = $_POST['start'];
    $offset = $_POST['offset'];

    $db->query('set names utf8;');

    if ($brand == "" && $keyword == "") {
        $sql = "select * from questionaire limit $start, $offset";
        $preparedStatement = $db->prepare($sql);
        $preparedStatement->execute();
    } else if ($brand != "" && $keyword == "") {
        $sql = "select * from questionaire where brand = :brand limit $start, $offset";

        $params = [
            ":brand" => $brand
        ];

        $preparedStatement = $db->prepare($sql);
        $preparedStatement->execute($params);
    } else if ($brand == "" && $keyword != "") {
        $sql = "select * from questionaire where subject like :keyword limit $start, $offset";
        $preparedStatement = $db->prepare($sql);
        $preparedStatement->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        $preparedStatement->execute();
    } else if ($brand != "" && $keyword != "") {
        $sql = "select * from questionaire where brand = :brand and subject like :keyword limit $start, $offset";
        $preparedStatement = $db->prepare($sql);
        $preparedStatement->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        $preparedStatement->bindValue(':brand', $brand, PDO::PARAM_STR);
        $preparedStatement->execute();
    }

    try {
        $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
?>