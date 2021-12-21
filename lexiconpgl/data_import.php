<?php

	$pdo = new PDO('sqlite:./db/mlexicon.sqlite');
	$pdo->beginTransaction();
	
	$pdo->exec("DELETE FROM data");
	
	$csv = file("all_pages.txt");
	for($i=0;$i < count($csv); $i++){
		$csv[$i] = trim($csv[$i]);
	if ($csv[$i] =='') {continue;}
	if (preg_match("/^\d+$/m",$csv[$i])) {continue;}
	if (preg_match("/^\d+\*$/m",$csv[$i])) {continue;}

			if (preg_match("@\s\[\%p(\d+)\s@m",$csv[$i], $_page)) {
				#print $_page[0];
				$page = $_page[1];
				$csv[$i] = preg_replace("@^(.*?)(\s\[\%p\d+\s)(.*?)$@m",'$1 $3',$csv[$i]);
				$csv[$i] = preg_replace("@\]\s\[ksl=@m",' ',$csv[$i]);
				$_page = null;
			}				
				// Seite rausholen
			if (preg_match("@^\[\%@m",$csv[$i])) {
				$page = $csv[$i];
				$page = preg_replace("@^\[\%p@m",'',$page);
				continue;
				}	
	
				
				
	$csv[$i] = preg_replace("/\'/","''",$csv[$i]);
	preg_match("/^(.*?\])\s(.*)$/m",$csv[$i],$row);
	   #print $csv[$i] . PHP_EOL;
	
		#$row[6] = trim($row[6]); // remove trailing delimiter
		$row[2] = preg_replace("/^m\.\s/m","{m.} ",$row[2]);
		$row[2] = preg_replace("/^f\.\s/m","{f.} ",$row[2]);
		$row[2] = preg_replace("/^n\.\s/m","{n.} ",$row[2]);
		$row[2] = preg_replace("/^pl\.\s/m","{pl.} ",$row[2]);
		$row[2] = preg_replace("/^adv\.\s/m","{adv.} ",$row[2]);
		$row[2] = preg_replace("/^adj\.\s/m","{adj.} ",$row[2]);
		$row[2] = preg_replace("/^num\.\s/m","{num.} ",$row[2]);
		$row[2] = preg_replace("/^subst\.\s/m","{subst.} ",$row[2]);
		
		$row[2] = preg_replace("@\spl\.\s@m"," {pl.} ",$row[2]);
		$row[2] = preg_replace("@\sn\.\s@m"," {n.} ",$row[2]);
		$row[2] = preg_replace("@\sf\.\s@m"," {f.} ",$row[2]);
		$row[2] = preg_replace("@\sm\.\s@m"," {m.} ",$row[2]);
		$row[2] = preg_replace("@\sgen\.\s@m"," {gen.} ",$row[2]);
		$row[2] = preg_replace("@\sacc\.\s@m"," {acc.} ",$row[2]);
		$row[2] = preg_replace("@\sinstr\.\s@m"," {instr.} ",$row[2]);
		$row[2] = preg_replace("@\sposs\.\s@m"," {poss.} ",$row[2]);
		$row[2] = preg_replace("@\spron\.\s@m"," {pron.} ",$row[2]);
		$row[2] = preg_replace("@\sindecl\.\s@m"," {indecl.} ",$row[2]);
		$row[2] = preg_replace("@\ssubst\.\s@m"," {subst.} ",$row[2]);
		$row[2] = preg_replace("@\saor\.\s@m"," {aor.} ",$row[2]);
		$row[2] = preg_replace("@\snum\.\s@m"," {num.} ",$row[2]);
		
		$row[2] = preg_replace("@\}\s\{@m"," ",$row[2]);
		
		if (preg_match("@^\{.+?\}\s@m",$row[2])) {
			preg_match("@^(\{.+?\})\s@m",$row[2],$gram);
			$row[1] = $row[1] . ' ' . $gram[1]; $gram = null;
			$row[2] = preg_replace("@^\{.+?\}\s@m","",$row[2]);
			}
			
			$warr  = explode(" ",$row[2]);
			#print_r($warr);
			for ($w = 0; $w < count($warr); $w++) {		
				if (preg_match("@\p{Greek}@ui",$warr[$w])) {
					#print $warr[$w];
						$wlist .= $warr[$w] .'|';
					}
			}
		
			$sql = "INSERT INTO data (
			'col_1',
			'col_2',
			'col_3',
			'col_4',
			'col_5'
			) VALUES (
			'{$csv[$i]}',
			'{$row[1]}',
			'{$row[2]}',
			'{$page}',
			'{$wlist}'
			)";
			$wlist = null;
			$warr  = null;

			$pdo->query($sql);
	}
			unset($query);

			#$pdo->exec("UPDATE main.data SET autor = null WHERE autor =''");

		
			$pdo->commit();	
			unset($pdo);
	
?>