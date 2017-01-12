<?php
	require "../config/config.php";

	$flag = true;
	$dsn = "mysql:host=" . MYSQL_HOST . ";dbname=" . DB_NAME;
	$db = new PDO($dsn, USERNAME, PASSWORD);

	do {
		if ($flag) {
			fwrite(STDOUT, '請輸入用戶名:');
			$flag = false;
		} else {
			fwrite(STDOUT, '用戶名不能爲空，請重新輸入');
		}
		
		$name = trim(fgets(STDIN));
	 
	} while (!$name);

	try {

		$sql = "select * from user where name = :name";
	    
	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params = [
	    	":name" => $name
	    ];

	    $preparedStatement->execute($params);

	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);

	    if (count($result) != 0) {
	    	echo "用戶名已存在" . "\n";
	    	exit;
	    }

    } catch (Exception $e) {
    	echo "未知錯誤,請稍後再試" . "\n";
    	exit;
    }

	$flag = true;

	do {
		if ($flag) {
			fwrite(STDOUT, '請輸入密碼:');
			$flag = false;
		} else {
			fwrite(STDOUT, '密碼不能爲空，請重新輸入');
		}
		
		$password = trim(fgets(STDIN));
	 
	} while (!$password);

	$password = md5($password);

	try {
		$sql = "insert into user(role, name, password, createTime) values('system_admin', :name, :password, :createTime)";

	    $db->query('set names utf8;');

	    $preparedStatement = $db->prepare($sql);

	    $params =[
	        ":name" => $name,
	        ":password" => $password,
	        ":createTime" => date('Y-m-d H:i:s', time())
	    ];

	    $result = $preparedStatement->execute($params);
	    
	    if (!$result) {
	    	echo "創建失敗,請稍後再試" . "\n";
	    	return;
	    }
	   
	    echo "創建成功" . "\n";
    } catch (Exception $e) {
        echo "創建失敗,請稍後再試" . "\n";
    }
?>