<?php

	header("Content-type: text/xml");
	
    ini_set('memory_limit', '-1');
	

	$abs_pfad = getcwd();
	//echo $abs_pfad;

	// Get the raw files here, unzip in ./htm folder
	// $raw = file_get_contents('https://slavistik-portal.de/msd/raw/MiklLexi_168906952X_Page_0501-600_V2_Ne.zip');
	
	#$directory = new RecursiveDirectoryIterator('./test');
	$directory = new RecursiveDirectoryIterator('./htm');
	
	$flattened = new RecursiveIteratorIterator($directory);

	// Filetype filtern
	$files = new RegexIterator($flattened, '/^.+\.htm$/i', RecursiveRegexIterator::GET_MATCH);
	
	foreach($files as $file) {
		
		
		$html .= file_get_contents($file[0]);

	}
	
		$html = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $html); 
		#$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');	
		$html = preg_replace("@^\s+@m",'',$html);
		$html = preg_replace("@\r\n@m",' ',$html);
		$html = preg_replace("@style=\'width.*?\'@mis",'',$html);
		$html = preg_replace("@style=\'margin.*?\'@mis",'',$html);
		$html = preg_replace("@font-size:\d+.\d+pt;font-family:@mis",'',$html);
		$html = preg_replace("@<spanclass=Bodytext10>@mis",'',$html);
		$html = preg_replace("@<p class=Bodytext101 >@mis",'',$html);
		$html = preg_replace("@<span class=Bodytext10>@mis",'',$html);
		$html = preg_replace("@<span style=\'background:.+?\'>(.+?)<\/span>@mis",'$1',$html);
		$html = preg_replace("@<span style=\'color:.+?\'>(.+?)<\/span>@mis",'$1',$html);
		$html = preg_replace("@<span style=\'text-transform:uppercase\'>-<\/span>@mis",'-',$html);
		$html = preg_replace("@<span style=\'text-transform:uppercase\'>\s-<\/span>@mis",' -',$html);
		$html = preg_replace("@<span style=\'text-transform:uppercase\'>\s<\/span>@mis",' ',$html);
		$html = preg_replace("@<span style=\'text-transform:uppercase\'><\/span>@mis",' ',$html);
		$html = preg_replace("@\"Monomakh Unicode\",\"serif.*?>(.*?)<\/span@mis",'\"Monomakh Unicode\",\"serif\'>#m1#$1#m2#</span',$html);
		$html = preg_replace("@\[\%p<.*?(\d+)<@mis",'[%p$1<',$html);
		#$html = preg_replace("@\[\%p(\d+)\s@",PHP_EOL .'[%p$1'.PHP_EOL,$html);
		$html = preg_replace("@\[\%p(\d+)@",'#####[%p$1',$html);
		$html = $html . '#####'; // last page
		$html = preg_replace("@\d+\*@mis",'',$html);
		preg_match_all("@\[\%p(\d+)(.*?)#####+@mis",$html,$pages);
		
		#print_r($pages);
		print count($pages[0]) . PHP_EOL;
		
	for ($i = 0; $i < count($pages[0]); $i++) {
		
		#if ($i > 20) {break;}
		
		#print $pages[0][$i] . PHP_EOL;
		print $i . PHP_EOL;
		
			//html schreiben
			#text2file($pages[0][$i], './split/p-'.$pages[1][$i].'.htm');
			#file_put_contents('./split/p-'.$pages[1][$i].'.htm',$pages[0][$i]);
		   
 		$pages[0][$i] = preg_replace('@&nbsp;@mis', ' ', $pages[0][$i]);
		
		// Absätze 
		$pages[0][$i] = preg_replace('@<\/p>@mis', '</tr>'.PHP_EOL, $pages[0][$i]); 
		
		
		$pages[0][$i] = preg_replace('/(\[\%p\d+)/','$1 ',$pages[0][$i]);
		$pages[0][$i] = preg_replace('/\040{1,}/',' ',$pages[0][$i]);
		
		$pages[0][$i] = h2t($pages[0][$i]);
		
		// Nach 2txt reinigen
		$pages[0][$i] = preg_replace('@#####@mis', '', $pages[0][$i]);
		$pages[0][$i] = preg_replace('/^\040{1,}$/m','',$pages[0][$i]);
		$pages[0][$i] = preg_replace('/ /m',' ',$pages[0][$i]); //NON-BREAKING SPACE
		
		
		// Kirchenslawisch rausholen
		$pages[0][$i] = preg_replace('@#m2##m1#@mis', '', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@#m1#@mis', '[ksl=', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@#m2#@mis', ']', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@\s]@mis', '] ', $pages[0][$i]);
		$pages[0][$i] = preg_replace('/\040{1,}/m',' ',$pages[0][$i]);			
		$pages[0][$i] = preg_replace('@\]\s\[ksl=@mis', ' ', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@\.\[@mis', '. [', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@\]\.@mis', ']', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@\]-@mis', '-]', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@\]\s-\[ksl=@mis', ' -', $pages[0][$i]);		
		
		// Leerraum behandeln
		$pages[0][$i] = preg_replace('@\(@mis', ' (', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@,@mis', ', ', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@;@mis', '; ', $pages[0][$i]);
		
		//Allerletze Bereinigungen
		$pages[0][$i] = preg_replace('/\040{1,}/m',' ',$pages[0][$i]);		
		$pages[0][$i] = preg_replace('/^\s+/m','',$pages[0][$i]);
		$pages[0][$i] = preg_replace('/^\*\d+/m',"",$pages[0][$i]);		
		$pages[0][$i] = preg_replace('/^\d+/m',"",$pages[0][$i]);		
		
		// Kosmetik	
		$pages[0][$i] = preg_replace('/\[ksl=\s/mis'," [ksl=",$pages[0][$i]);
		$pages[0][$i] = preg_replace('/\[ksl=\]/mis',"",$pages[0][$i]);
		$pages[0][$i] = preg_replace('/\[ksl=ъ\]/mis',"ъ",$pages[0][$i]);
		$pages[0][$i] = preg_replace('/нъ\], adj/mis',"нъ] adj",$pages[0][$i]);
		$pages[0][$i] = preg_replace('/karamz\. 2\. n\./mis',"karamz. 2. nota",$pages[0][$i]);

		$pages[0][$i] = preg_replace('//mu',"і",$pages[0][$i]); // privat use area for rum i trough cyrillic i
		$pages[0][$i] = preg_replace('/і̇/mu',"і",$pages[0][$i]); // combining i for rum i trough cyrillic i
		$pages[0][$i] = preg_replace('/̆̆/mu',"̆",$pages[0][$i]); // double to simple
		$pages[0][$i] = preg_replace('/y/mu',"ý",$pages[0][$i]); // privat use aria to normal
		#$pages[0][$i] = preg_replace('//mu',"--",$pages[0][$i]); // privat use area trough cyrillic uk

		// Grammatische Markierungen
		$pages[0][$i] = preg_replace('/\sadj\s/m'," {adj.} ",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\sadj\./m'," {adj.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\sn\./m'," {n.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\sm\./m'," {m.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\sf\./m'," {f.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\sadv\./m'," {adv.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\sconj\./m'," {conj.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\sposs\./m'," {poss.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\svb\./m'," {vb.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\spl\./m'," {pl.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\snom\.\s/m'," {nom.} ",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\spropr\./m'," {propr.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\spron\./m'," {pron.}",$pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\}\s\{/m'," ",$pages[0][$i]); 
		
		#$pages[0][$i] = preg_replace('/\snsl\.\s/m'," ⟨nsl.⟩ ",$pages[0][$i]); 
		
		$pages[0][$i] = preg_replace('/\040{1,}/m',' ',$pages[0][$i]);	
		
		#$pages[0][$i] = bereinigen($pages[0][$i]);
		
		   #file_put_contents('./split/p-'.$pages[1][$i].'.txt',h2t($pages[0][$i]));
		   if (mb_strlen($pages[0][$i], 'utf8') > 20) {
				$all_pages .= $pages[0][$i];
				#text2file($pages[0][$i], './split/p-'.$pages[1][$i].'.txt');
		   }
	}
	
	$all_pages = corrwords($all_pages); // Falsch abgeschrieben
	$all_pages = markwords($all_pages); // Quellen markieren
	
	#$all_pages = clhypens($all_pages); // Bindestriche korrigieren
	#$all_pages = clwords($all_pages); // Korrigiere falsche Zeichen
	
	// Zuallerletzt
	$all_pages = preg_replace('/­/mu',"",$all_pages); // soft hypen, besser raus
	
	text2file($all_pages, './all_pages.txt');
	
	getwords($all_pages);
	
	getchars($all_pages);
	
	
	getsrc($all_pages);
	
	
	function getsrc($value){
		//preg_match_all("@(\s[a-zšč]+\.)\s@",$value, $src);
		preg_match_all("@\s([a-zšč]+\.-[a-zšč]+\.)\s@",$value, $src);
		//preg_match_all("@\s([a-zšč]+\.\s-[a-zšč]+\.)\s@",$value, $src);
		for ($i = 0; $i < count($src[1]); $i++) {
			$_src[] = $src[1][$i];
		}
			
			$_src = array_count_values($_src);
			arsort($_src);
			file_put_contents('src.txt',var_export($_src, true));		
	}
	
	// Wort-, Fehleranalyse
	function getwords($value){
		$c = 0;
		$value = preg_replace('/\r\n/m'," ",$value);
		$value = preg_replace('/\t/m'," ",$value);
		$value = preg_replace('/[\:\.,;\(\)\{\}\[\]]/m'," ",$value);
		$value = preg_replace('/ksl=/m',"",$value);
		$value = preg_replace('/\#/m',"",$value);
		$value = preg_replace('/\!/m',"",$value);
		$value = preg_replace('/­/mu',"",$value); // soft hypen, wird automatisch zusammengefügt
		$value = preg_replace('/-/mu',"",$value); // hypen-minus
		
		$warr = explode(" ", $value);
		for ($i = 0; $i < count($warr); $i++) {
			$c++;
		
		//if (preg_match("@ћ|ђ@ui",$warr[$i])) { $serb .= $warr[$i] .'|';} // Zeichen rausholen
		
		if (preg_match("@[\p{L}]-[\p{L}]@ui",$warr[$i])) {
						$tlist .= $warr[$i] .'|';
					}			
		if (preg_match("@[\p{Cyrillic}][^\p{Cyrillic}]|[^\p{Cyrillic}][\p{Cyrillic}]@ui",$warr[$i])) {
		//if (preg_match("@\xcc\x81@i",$warr[$i])) {
				$wlist .= $warr[$i] .'|';
			}
		}
			$_wlist= explode("|", $wlist);
			$_wlist = array_unique($_wlist);
			$_wlist = array_count_values($_wlist);
			#arsort($_wlist);
			file_put_contents('wlist.txt',var_export($_wlist, true));		

			$_tlist= explode("|", $tlist);
			$_tlist = array_unique($_tlist);
			$_tlist = array_count_values($_tlist);
			#arsort($_tlist);
			file_put_contents('tlist.txt',var_export($_tlist, true));	
			print $c . PHP_EOL;

			$_serb= explode("|", $serb);
			$_serb = array_unique($_serb);
			$_serb = array_count_values($_serb);
			#arsort($_wlist);
			file_put_contents('serb_c-d.txt',var_export($_serb, true));
			
	}
	
	
	function getchars($value){
			$value = preg_replace('/\r\n/m'," ",$value);
			$value = preg_replace('/\t/m'," ",$value);		
			$char = preg_split('//u', $value, null, PREG_SPLIT_NO_EMPTY);
			for ($i = 0; $i < count($char); $i++) {
				if ($char[$i] !='') {$_char[] = $char[$i];}
			}
			#$_char = array_count_values($_char);
			$_char = array_unique($_char);
			asort($_char);
			file_put_contents('charlist.txt',var_export($_char, true));
	}

	
	function text2file($text, $filename)
	{
		// Add to fille
		$q = fopen($filename,'w');
		fputs($q, $text);
		fclose($q);
	}
	
	function h2t($v){
		$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
					   '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
					   '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
					   '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
		);

		
		$text = preg_replace($search, '', $v);
		
		return $text;
	}	
	
		function bereinigen($value) {
		$table = array(
				"{ (vulg.}"=>"({vulg.}",
				"{ (clupea"=>"{(clupea",
				"{ (dial.}"=>"({dial.}",
				"{ (paris"=>"{(paris",
				"{ (proximus)}"=>"{(proximus)}",
				"{ (pl. "=>"{(pl. ",
				"{ (pl.}"=>"({pl.}",
			
			);
				
		$value = strtr($value, $table);			
		
		return($value);
	  }
	  
		function clwords($value) {
			include 'inc/charkorr.php';
			$value = strtr($value, $table);			
		return($value);		
		}	

		function clhypens($value) {
			include 'inc/hypenkorr.php';
			$value = strtr($value, $table);			
		return($value);		
		}	

		function corrwords($value) {
			include 'inc/corrwords.php';
			$value = strtr($value, $table);			
		return($value);		
		}	

		function markwords($value) {
			include 'inc/markwords.php';
			
			$value = strtr($value, $table);			
		return($value);		
		}		
?>