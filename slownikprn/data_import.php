<?php

	$pdo = new PDO('sqlite:./db/szmidt.sqlite');
	$pdo->beginTransaction();
	
	$pdo->exec("DELETE FROM data");
	
	$csv = file("all_pages.txt");
	for($i=0;$i < count($csv); $i++){
		$csv[$i] = trim($csv[$i]);
	if ($csv[$i] =='') {continue;}
	if (preg_match("/^\d+$/m",$csv[$i])) {continue;}
	if (preg_match("/^\d+\*$/m",$csv[$i])) {continue;}
	if (preg_match("/^\{Tom\.\sI\.\}$/m",trim($csv[$i]))) {continue;}
	if (preg_match("/^\Tom\.\sI\.\$/m",trim($csv[$i]))) {continue;}
	if (preg_match("/^\{\}$/m",trim($csv[$i]))) {continue;}
	if (preg_match("/^\p{L}\p{L}\p{L}$/m",trim($csv[$i]))) {continue;} // Columenüberschriften
	
	if (preg_match("/^Rejestr niektórych imion/mu",trim($csv[$i]))) {continue;} 
	if (preg_match("/^Роспись нѣкоторымЪ именамЪ/mu",trim($csv[$i]))) {continue;} 
	if (preg_match("/^\[fra=Verzeichniß einiger Manner/mu",trim($csv[$i]))) {continue;} 
	if (preg_match("/^Tabella niektórych z nayznakomitszych/mu",trim($csv[$i]))) {continue;} 
	if (preg_match("/^Имена нѣкоторыхЪ знаменитыхЪ/mu",trim($csv[$i]))) {continue;} 
	if (preg_match("/^\[fra=Namen einiger bekannteren/mu",trim($csv[$i]))) {continue;} 

			if (preg_match("@\s\[\%p(\d+)\s@m",$csv[$i], $_page)) {
				#print $_page[0];
				# _page to page siehe unten
				$csv[$i] = preg_replace("@^(.*?)(\s\[\%p\d+\s)(.*?)$@m",'$1 $3',$csv[$i]);
			}


				
				// Seite rausholen
			if (preg_match("@^\[\%@m",$csv[$i])) {
				$page = $csv[$i];
				$page = preg_replace("@^\[\%p@m",'',$page);
				continue;
				}	
				$page = preg_replace("@\]@m",'',$page);
	
				
				
	$csv[$i] = preg_replace("/\'/","''",$csv[$i]);
	
	#preg_match("/^(.*?\})\s(.*)$/m",$csv[$i],$row);
	#preg_match("/^(.*?\})\s(.*)$/m",$csv[$i],$row);
	#preg_match("/^(.*?)\s(\p{Cyrillic}.*)$/mu",$csv[$i],$row);
	
	   #print $csv[$i] . PHP_EOL;
	
		#$row[6] = trim($row[6]); // remove trailing delimiter

		preg_match("/^(.*?)\s(\d\).*)$/mu",$csv[$i],$row); // Wenn die zahl als erstes kommt
		
		if ($row[2]=='') {
			preg_match("/^(.*?)([\s\(]\p{Cyrillic}.*)$/mu",$csv[$i],$row);
		}
		
		if ($row[2]=='') {
			preg_match("/^(.*?\})\s(.*)$/m",$csv[$i],$row);
		}
		
		if ($row[2]=='' && preg_match("/\sv\.\s/m",$csv[$i])) {
				#preg_match("/^(.*?\sv\.)\s(.*)$/m",$csv[$i],$row);
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

			# Seitenübergänge
			if ($_page[1] !='') {
				$page = $_page[1];
				$_page = null;
			}
				
			$pdo->query($sql);
	}
			unset($query);

			#$pdo->exec("UPDATE main.data SET autor = null WHERE autor =''");

		
			$pdo->commit();	
			unset($pdo);
	
?>