<?php

	$pdo = new PDO('sqlite:./db/ks_sugg.sqlite');
	$pdo->beginTransaction();
	
	$pdo->exec("DELETE FROM sugg");
	
	$csv = file("txt/sug.txt");
	for($i=0;$i < count($csv); $i++){
	$csv[$i] = preg_replace("/\'/","''",$csv[$i]);
	   $row = explode("\t",$csv[$i]);
	
		#$row[6] = trim($row[6]); // remove trailing delimiter
		
			$sql = "INSERT INTO sugg (
			'col_1',
			'col_2',
			'col_3'
			) VALUES (
			'{$row[0]}',
			'{$row[1]}',
			'{$row[2]}'
			)";

			$pdo->query($sql);
	}
	

			unset($query);
		
			$pdo->commit();	
			
			$pdo->exec("REINDEX main.idx1");
			unset($pdo);
	
?>