<?php

	$pdo = new PDO('sqlite:./db/msd_sugg.sqlite');
	$pdo->beginTransaction();
	
	$pdo->exec("DELETE FROM sugg where col_3='SlownikPRN'");
	
	$csv = file("sug.txt");
	for($i=0;$i < count($csv); $i++){
	$csv[$i] = preg_replace("/\'/","''",$csv[$i]);
	   $row = explode("\t",$csv[$i]);
	
		#$row[6] = trim($row[6]); // remove trailing delimiter
		
		$dcheck = $pdo->query('SELECT rowid,* FROM main.sugg WHERE col_1 ="'.$row[0].'" limit 1')->fetchall();
		if ($dcheck[0]['col_1'] !='') { continue;}
		
			$sql = "INSERT INTO sugg (
			'col_1',
			'col_2',
			'col_3',
			'col_4'
			) VALUES (
			'{$row[0]}',
			'{$row[1]}',
			'{$row[2]}',
			'{$row[3]}'
			)";

			$pdo->query($sql);
	}
	

			unset($query);
		
			$pdo->commit();	
			
			$pdo->exec("REINDEX main.idx1");
			unset($pdo);
	
?>