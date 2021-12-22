<?php

			$pdo = new PDO('sqlite:./db/mlexicon.sqlite');
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
			
			if ($row[col_1] !='' && $row[col_2] =='') {
				#print_r($row);
				$bkyr = trim($row[col_1]);
				continue;
				}
				
				$zeile++;						
			
				preg_match_all("@\[ksl=(.*?)\]@m",$row[col_2], $ksl);
				
 				
				if ($ksl[1][0] !='') {
					for ($i = 0; $i < count($ksl[1]); $i++) {
							$_ksl .= $ksl[1][$i] . ' ';
					}
					$_ksl = trim($_ksl);
					#$row[col_2] = preg_replace("@\[ksl=(.*?)\]@m",'',$row[col_2]); 
					
					$kslwl .= $_ksl .'|';
					
					
					
					// Kirchenslawische Korrekturen
					#$_ksl = preg_replace("@оу@mu",'ѹ',$_ksl);
					#$_ksl = preg_replace("@є@mu",'е',$_ksl);
					
				}
					
					#$row[col_2] = preg_replace("@\[ksl=@m",'',$row[col_2]);
					#$row[col_2] = preg_replace("@\]@m",'',$row[col_2]);
					
				$sql = "INSERT INTO fdata (
				   'col_1',
				   'col_2',
				   'col_3',
				   'col_4',
				   'col_5',
				   'col_6'

			   ) VALUES (
				   '{$row[col_1]}',
				   '{$row[col_2]}',
				   '{$row[col_3]}',
				   '{$row[col_4]}',
				   '{$row[col_5]}',
				   '{$bkyr}'

			   )";
			   
			   $pdo->query($sql);
			   
			   
			   $_ksl = null;
			}
		
			
	  $pdo->commit();	
	  unset($pdo);						
			
		function enlarge_words($value) {
			include 'enlarge_words.php';
			$value = strtr($value, $table);			
		return($value);		
		}
	
?>