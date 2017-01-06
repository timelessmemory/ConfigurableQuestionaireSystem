<?php
	header("Content-Type:text/html;charset=UTF-8");

	$id = $_GET["id"];

	$name = "answer_to_questionaire_" . $id;

	$filename = urlencode($name);
	$file_name = $filename . '.xlsx';

	$file_path = dirname(__FILE__) . "/answer_files/" . $file_name;

	// if (!file_exists($file_path)) {

	$dsn = "mysql:host=localhost;dbname=questionaireWeb";
    $db = new PDO($dsn, 'root', 'root');
    $db->query('set names utf8;');

	try {
		$sql = "select * from answer where questionaireId = :id";

    	$params = [
    		":id" => $id
    	];

    	$preparedStatement = $db->prepare($sql);

	    $preparedStatement->execute($params);

	    $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);

	    if (count($result) == 0) {
	    	echo json_encode(array("code" => 400, "result" => "no data"));
	    	return;
	    }

	    error_reporting(E_ALL);

		date_default_timezone_set('Asia/Shanghai');

		$path = dirname(__FILE__) . "/Classes" ;

		set_include_path($path);

		require_once 'PHPExcel.php';

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()
					->setCreator('script')
		            ->setLastModifiedBy('script')
		            ->setTitle('Office 2007 XLSX Document')
		            ->setSubject('Office 2007 XLSX Document')
		            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
		            ->setKeywords('office 2007 openxml php')
		            ->setCategory('Result file');

		$objPHPExcel->createSheet();

		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()
		      		->setCellValue('A1', "mobile")
		      		->setCellValue('B1', "email")
		      		->setCellValue('C1', "agree")
		      		->setCellValue('D1', "content")
		      		->setCellValue('E1', "answerTime");
		
		$index = 2;
		
		foreach ($result as $item) {
			
		    $objPHPExcel->getActiveSheet()
		      			->setCellValue('A' . $index, $item['mobile'])
		      			->setCellValue('B' . $index, $item['email'])
		      			->setCellValue('C' . $index, $item['agree'])
		      			->setCellValue('D' . $index, $item['content'])
		      			->setCellValue('E' . $index, $item['answerTime']);
			$index++;
		}

		$objPHPExcel->getActiveSheet()->setTitle($name);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save(dirname(__FILE__) . "/answer_files/" . $filename . '.xlsx');
	    
    } catch (Exception $e) {
        echo json_encode(array("code" => 500));
    }
	// }

	$file_size = filesize($file_path);

	header('Content-Description: File Transfer');
	header("Content-type:application/octet-stream");
	header("Content-disposition: attachment; filename=\"" . $file_name . "\""); 
	header("Accept-Ranges:bytes");
	header("Content-Length: " . $file_size);

	$fp = fopen($file_path, "rb");

	$buffer = 1024;

	while (!feof($fp)) {
		$file_data = fread($fp, $buffer);
		echo $file_data;
	}

	fclose($fp);
?> 