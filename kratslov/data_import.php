<?php

	$pdo = new PDO('sqlite:./db/kratslov.sqlite');
	$pdo->beginTransaction();
	
	$pdo->exec("DELETE FROM data");
	
	$csv = file("all_pages.txt");
	for($i=0;$i < count($csv); $i++){
	$csv[$i] = preg_replace("/\'/","''",$csv[$i]);
	   $row = explode("\t",$csv[$i]);
	
		#$row[6] = trim($row[6]); // remove trailing delimiter
		
			$sql = "INSERT INTO data (
			'col_1',
			'col_2',
			'col_3',
			'col_4',
			'col_5',
			'col_6',
			'col_7'
			) VALUES (
			'{$row[0]}',
			'{$row[1]}',
			'{$row[2]}',			
			'{$row[3]}',
			'{$row[4]}',
			'{$row[5]}',
			'{$row[6]}'
			)";

			$pdo->query($sql);
	}
			unset($query);

		
			$pdo->commit();	
			unset($pdo);
	
?>