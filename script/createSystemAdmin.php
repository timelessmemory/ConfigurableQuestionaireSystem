<?php

	$flag = true;

	do {
		if ($flag) {
			fwrite(STDOUT, '请输入用户名：');
			$flag = false;
		} else {
			fwrite(STDOUT, '用户名不能为空，请重新输入：');
		}
		
		$name = trim(fgets(STDIN));
	 
	} while (!$name);

	try {

		$sql = "select * from user where name = :name";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params = [
	    	":name" => $name
	    ];

	    $preparedStatement->execute($params);

	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);

	    if (count($result) != 0) {
	    	echo "用户名已存在" . "\n";
	    	exit;
	    }

    } catch (Exception $e) {
    	echo "未知错误,请稍后再试" . "\n";
    	exit;
    }

	$flag = true;

	do {
		if ($flag) {
			fwrite(STDOUT, '请输入密码：');
			$flag = false;
		} else {
			fwrite(STDOUT, '密码不能为空，请重新输入：');
		}
		
		$password = trim(fgets(STDIN));
	 
	} while (!$password);

	$password = md5($password);

	try {
		$sql = "insert into user(role, name, password, createTime) values('system_admin', :name, :password, :createTime)";

	    $dsn = "mysql:host=localhost;dbname=questionaireWeb";
	    $db = new PDO($dsn, 'root', 'root');
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":name" => $name,
	        ":password" => $password,
	        ":createTime" => date('Y-m-d H:i:s', time())
	    ];

	    $result = $preparedStatement->execute($params);
	    
	    if (!$result) {
	    	echo "创建失败,请稍后再试" . "\n";
	    	return;
	    }
	   
	    echo "创建成功" . "\n";
    } catch (Exception $e) {
        echo "创建失败,请稍后再试" . "\n";
    }
?>