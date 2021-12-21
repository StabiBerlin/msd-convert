<?php

			$pdo = new PDO('sqlite:./db/szmidt.sqlite');
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
			
			// Fehlender Verweise nachtragen
			$row = preg_replace("/\sv\.\s/"," {v.} ",$row);
				
				
			// Buchstabe rausholen
			
			//if (preg_match("/^\p{L}\.$/m",$row[col_1]) && $row[col_2] =='') {
			if (preg_match("/^(\p{L}).*$/mu",$row[col_1],$fb)) {
				#print_r($row);
				$bkyr = trim($fb[1]);
				}
				
				$zeile++;						

				$allwords = explode(" ",$row[col_1]);
				#print_r($allwords);
					for ($i = 0; $i < count($allwords); $i++) {
							if (preg_match("@\p{Cyrillic}@ui",$allwords[$i])) {$_kyr .= $allwords[$i] . ' ';}
					}
					$_kyr = trim($_kyr);			
					$kyrwl .= $_kyr;
					$kyrwl = preg_replace("@[,\.]$@m",'',$kyrwl);					
				
				$row[col_3] = trim($row[col_3]);
				preg_match_all("@\[fra=(.*?)\]@m",$row[col_3], $ger);
				if ($ger[1][0] !='') {
					for ($i = 0; $i < count($ger[1]); $i++) {
							$_ger .= $ger[1][$i] . ', ';
					}
					$_ger = trim($_ger);			
					$gerwl .= $_ger;
					$gerwl = preg_replace("@[,\.]$@m",'',$gerwl);					
				}
							
					#$row[col_2] = preg_replace("@\[ksl=@m",'',$row[col_2]);
					
					if (starts_with_upper($row[col_3]) != $row[col_3] ) {$row[col_2] = mb_strtolower($row[col_2], 'UTF-8');}
					
					#$row[col_4] = mb_substr($row[col_3], 0, 1, 'UTF-8');
					
					#if ($row[col_4] >=904) {$bkyr = 'Add';}
					
					
				$sql = "INSERT INTO fdata (
				   'col_1',
				   'col_2',
				   'col_3',
				   'col_4',
				   'col_5',
				   'col_6',
				   'col_7'

			   ) VALUES (
				   '{$row[col_1]}',
				   '{$row[col_2]}',
				   '{$row[col_3]}',
				   '{$row[col_4]}',
				   '{$kyrwl}',
				   '{$gerwl}',
				   '{$bkyr}'

			   )";
			   
			   $pdo->query($sql);
			   
			   $kyrwl = null;
			   $_kyr = null;			   
			   $gerwl = null;
			   $_ger = null;
			}
		
			
	  $pdo->commit();	
	  unset($pdo);	
	  
		function starts_with_upper($str) {
			$chr = mb_substr ($str, 0, 1, "UTF-8");
			return mb_strtolower($chr, "UTF-8") != $chr;
		}
				
		function enlarge_words($value) {
			include 'enlarge_words.php';
			$value = strtr($value, $table);			
		return($value);		
		}
	
?>