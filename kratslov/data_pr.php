<?php

			$pdo = new PDO('sqlite:./db/kratslov.sqlite');
			$pdo->beginTransaction();
			
			$pdo->exec("DELETE FROM fdata");

				$query = $pdo->prepare("SELECT rowid,* FROM data"); //LIMIT 2
				if (!$query) {
				print PHP_EOL . "PDO::errorInfo():" . PHP_EOL;
				print_r($pdo->errorInfo());
				}	
			
				$query->execute();
			
			$zeile = 0;
			foreach($query as $row) {
			
					// Bereinigung vom SQL-Statment
					$row = preg_replace("/\'/","''",$row);
			
				// Seite rausholen
			if (preg_match("@^\[\%@m",$row[col_1])) {
				$page = $row[col_1];
				$page = preg_replace("@^\[\%p@m",'',$page);
				$zeile = 0;
				continue;
				}
				
				
			// Buchstabe rausholen
			if (preg_match("@^\[\#@m",$row[col_1])) {
				#print_r($row);
				$bkyr = trim($row[col_1]);
				$bkyr = preg_replace("@^\[\#@m",'',$bkyr);
				$blat = trim($row[col_2]);
				continue;
				}
				
				$zeile++;						
			
				preg_match_all("@\[ksl=(.*?)\]@m",$row[col_1], $ksl);
				
				if ($ksl[1][0] !='') {
					for ($i = 0; $i < count($ksl[1]); $i++) {
							$_ksl .= $ksl[1][$i] . ' ';
					}
					$_ksl = trim($_ksl);
					$row[col_1] = preg_replace("@\[ksl=(.*?)\]@m",'',$row[col_1]);
					
					// Kirchenslawische korrekturen
					$_ksl = preg_replace("@оу@mu",'ѹ',$_ksl);
					$kslwl .= $_ksl .'|';
				}
				
				
				
				$sql = "INSERT INTO fdata (
				   'col_1',
				   'col_2',
				   'col_3',
				   'col_4',
				   'col_5',
				   'col_6',
				   'col_7',
				   'col_8',
				   'col_9',
				   'col_10',
				   'col_11',
				   'col_12'

			   ) VALUES (
				   '{$row[col_1]}',
				   '{$_ksl}',
				   '{$row[col_2]}',
				   '{$row[col_3]}',
				   '{$row[col_4]}',
				   '{$row[col_5]}',
				   '{$row[col_6]}',
				   '{$row[col_7]}',
				   '{$bkyr}',
				   '{$blat}',
				   '{$page}',
				   '{$zeile}'

			   )";
			   
			   $pdo->query($sql);
			   
			   
			   $_ksl = null;
			}
		
			
	  $pdo->commit();	
	  unset($pdo);			
	  
			$kslwl = preg_replace("@[\(\)]@m",'',$kslwl);
			$_kslwl= explode("|", $kslwl);
			$_kslwl = array_unique($_kslwl);
			$_kslwl = array_count_values($_kslwl);
			#arsort($_wlist);
			file_put_contents('ksl_wl.txt',var_export($_kslwl, true));	  
	  
?>