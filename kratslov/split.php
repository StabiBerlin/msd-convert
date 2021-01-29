<?php

	header("Content-type: text/xml");
	
    ini_set('memory_limit', '-1');
	

	$abs_pfad = getcwd();
	//echo $abs_pfad;

	#$directory = new RecursiveDirectoryIterator('./test');
	$directory = new RecursiveDirectoryIterator('./htm');
	
	$flattened = new RecursiveIteratorIterator($directory);

	// Filetype filtern
	$files = new RegexIterator($flattened, '/^.+\.htm$/i', RecursiveRegexIterator::GET_MATCH);
	
	foreach($files as $file) {
		
		
		$html = file_get_contents($file[0]);
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
		$html = preg_replace("@\"Monomakh Unicode\",serif.*?>(.*?)<\/@mis",'\"Monomakh Unicode\",serif\'>#m1#$1#m2#</',$html);
		$html = preg_replace("@\[\%p<.*?(\d+)<@mis",'[%p$1<',$html);
		$html = preg_replace("@\[\%p(\d+)@",'#####[%p$1',$html);
		$html = $html . '#####'; // last page
		preg_match_all("@\[\%p(\d+)(.*?)#####+@mis",$html,$pages);
		
		#print_r($pages);
		print count($pages[0]) . PHP_EOL;
		
	}
	
	for ($i = 0; $i < count($pages[0]); $i++) {
		
		#if ($i > 20) {break;}
		
		#print $pages[0][$i] . PHP_EOL;
		print $i . '-' . $pages[1][$i] . PHP_EOL;
		$pages[0][$i] = '<table>' . $pages[0][$i] . '</table>';
		
			//html schreiben
			#text2file($pages[0][$i], './split/p-'.$pages[1][$i].'.htm');
			#file_put_contents('./split/p-'.$pages[1][$i].'.htm',$pages[0][$i]);
		   
 		$pages[0][$i] = preg_replace('@&nbsp;@mis', ' ', $pages[0][$i]);
		
		
		// Grammatik markieren
			# Klammern entkursivieren
			#$pages[0][$i] = preg_replace('@<i>\(@mis', "(<i>", $pages[0][$i]);
			#$pages[0][$i] = preg_replace('@\)<\/i>@mis', "</i>)", $pages[0][$i]);
		$pages[0][$i] = preg_replace('@<i>@mis', ' #G1#', $pages[0][$i]);
		$pages[0][$i] = preg_replace('@<\/i>@mis', '#G2#', $pages[0][$i]);
		$pages[0][$i] = preg_replace('@<\/td>@mis', '</td>	', $pages[0][$i]);
		$pages[0][$i] = preg_replace('@<\/tr>@mis', '</tr>'.PHP_EOL, $pages[0][$i]); 
		$pages[0][$i] = preg_replace('/\040{1,}/',' ',$pages[0][$i]);
		
		$pages[0][$i] = h2t($pages[0][$i]);
		
		// Nach 2txt reinigen
		$pages[0][$i] = preg_replace('@#####@mis', '', $pages[0][$i]);
 		$pages[0][$i] = preg_replace('@^[\t\s]+$@mi', '', $pages[0][$i]);
		$pages[0][$i] = preg_replace('/^\040{1,}$/m','',$pages[0][$i]);
		
		// Grammatik markieren zurück
		$pages[0][$i] = preg_replace('@#G1#@mis', '{', $pages[0][$i]);
		$pages[0][$i] = preg_replace('@#G2#@mis', '}', $pages[0][$i]);
		
		// Kirchenslawisch rausholen
		$pages[0][$i] = preg_replace('@#m2##m1#@mis', '', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@#m1#@mis', ' [ksl=', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@#m2#@mis', ']', $pages[0][$i]);		
		
		// Leerraum behandeln
		$pages[0][$i] = preg_replace('@\(@mis', ' (', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@,@mis', ', ', $pages[0][$i]);		
		$pages[0][$i] = preg_replace('@;@mis', '; ', $pages[0][$i]);
		
		//Allerletze Bereinigungen
		$pages[0][$i] = preg_replace('/\040{1,}/m',' ',$pages[0][$i]);		
		$pages[0][$i] = preg_replace('/^\s+/m','',$pages[0][$i]);		
		$pages[0][$i] = preg_replace('/\s\t\s/m',"\t",$pages[0][$i]);		
		$pages[0][$i] = preg_replace('/[\t\s]+\r\n/m',"\r\n",$pages[0][$i]);		
		
		// Kosmetik
		$pages[0][$i] = preg_replace('/\.,\s\}/m',".}, ",$pages[0][$i]); //S. 21 безобразіе {n., }безобразность
		$pages[0][$i] = preg_replace('/\}\./m',".}",$pages[0][$i]); //S. 23 безприданная {f}.	без
		$pages[0][$i] = preg_replace('/\} \{\.\}/m',".}",$pages[0][$i]); //теплить, за- {v} {.}
		$pages[0][$i] = preg_replace('/\.: \}/m',".}: ",$pages[0][$i]); //говорной {adj.: }-ная
		$pages[0][$i] = preg_replace('/\{\s\}/m',"",$pages[0][$i]); // leere Klammern
		$pages[0][$i] = preg_replace('/\s\}/m',"} ",$pages[0][$i]); // leerstelle + Klammer
		$pages[0][$i] = preg_replace('/\{\s/m'," {",$pages[0][$i]); // leerstelle + Klammer
		$pages[0][$i] = preg_replace('/\{\.\}/m',"",$pages[0][$i]); // Klammer plus punkt
		$pages[0][$i] = preg_replace('/\.\;\}/m',".}; ",$pages[0][$i]); // kleine korr
		$pages[0][$i] = preg_replace('/\.\: —\}/m',".}: —",$pages[0][$i]); // kleine korr
		$pages[0][$i] = preg_replace('/\{a\} \{dј\.\}/m',"{adj.}",$pages[0][$i]); // kleine korr
		$pages[0][$i] = preg_replace('/\(n\. př,/m',"(n. př.",$pages[0][$i]); // kleine korr
		$pages[0][$i] = preg_replace('/\]ъ/mu',"ъ]",$pages[0][$i]); // kleine korr
		$pages[0][$i] = preg_replace('/v\. pop\.\s/m',"{v. pop.} ",$pages[0][$i]); // v. pop umklammern
		$pages[0][$i] = preg_replace('/\sv\./m'," {v.}",$pages[0][$i]); // v. pop umklammern
		$pages[0][$i] = preg_replace('/\sn\./m'," {n.}",$pages[0][$i]); // v. pop umklammern
		$pages[0][$i] = preg_replace('@[ ]+\s@mu',"",$pages[0][$i]); // U+0096 [control]
		$pages[0][$i] = preg_replace('@ @mu',"",$pages[0][$i]); // U+0096 [control]
		$pages[0][$i] = preg_replace('@ꙑ@mu',"ы",$pages[0][$i]); //  ‎A651 CYRILLIC SMALL LETTER YERU WITH BACK YER = ы
		$pages[0][$i] = preg_replace('@ъі@mu',"ы",$pages[0][$i]); //  ъі = ы
		$pages[0][$i] = preg_replace('@ⲁ@mu',"а",$pages[0][$i]); //  Coptic = а
		$pages[0][$i] = preg_replace('@ƚ@mu',"ł",$pages[0][$i]); //  019A LATIN SMALL LETTER L WITH BAR durch korrektes polnisches l
		$pages[0][$i] = preg_replace('@ᶏ@mu',"ą",$pages[0][$i]); //  ‎1D8F LATIN SMALL LETTER A WITH RETROFLEX HOOK durch poln ą
		$pages[0][$i] = preg_replace('@ο@mu',"o",$pages[0][$i]); //  ‎griech durch lat o
		$pages[0][$i] = preg_replace('@ȩ@mu',"ę",$pages[0][$i]); //  ‎‎0229 LATIN SMALL LETTER E WITH CEDILLA durch poln ę
		$pages[0][$i] = preg_replace('@ƭ@mu',"ť",$pages[0][$i]); //  ‎01AD LATIN SMALL LETTER T WITH HOOK durch cze t'
		$pages[0][$i] = preg_replace('@ƒ@mu',"f",$pages[0][$i]); //  0192 LATIN SMALL LETTER F WITH HOOK durch norm f
		$pages[0][$i] = preg_replace('@ĕ@mu',"ě",$pages[0][$i]); //  ‎0115 LATIN SMALL LETTER E WITH BREVE durch czech ě
		$pages[0][$i] = preg_replace('@ľ @mu',"ľ",$pages[0][$i]); //  französsisches l apostrof leerraum rausnehmen
		
		$pages[0][$i] = preg_replace('/\040{1,}/m',' ',$pages[0][$i]);	
		
		$pages[0][$i] = bereinigen($pages[0][$i]);
		
		// Seiten korr
				$pages[0][$i] = preg_replace_callback(
					"@\[\%p(\d+)@mis",
					function($matches) {
						return '[%p'. ($matches[1] - 1);
					},
					$pages[0][$i]);			
		
		
		   #file_put_contents('./split/p-'.$pages[1][$i].'.txt',h2t($pages[0][$i]));
		   if (mb_strlen($pages[0][$i], 'utf8') > 20) {
				$all_pages .= $pages[0][$i];
				#text2file($pages[0][$i], './split/p-'.$pages[1][$i].'.txt');
		   }
	}
	
	$all_pages = corrwords($all_pages); // Falsch abgeschrieben
	$all_pages = clhypens($all_pages); // Bindestriche korrigieren
	$all_pages = clwords($all_pages); // Korrigiere falsche Zeichen
	
	// Zuallerletzt
	$all_pages = preg_replace('/­/mu',"",$all_pages); // soft hypen, besser raus
	
	text2file($all_pages, './all_pages.txt');
	
	getwords($all_pages);
	
	getchars($all_pages);
	
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
			include 'charkorr.php';
			$value = strtr($value, $table);			
		return($value);		
		}	

		function clhypens($value) {
			include 'hypenkorr.php';
			$value = strtr($value, $table);			
		return($value);		
		}	

		function corrwords($value) {
			include 'corrwords.php';
			$value = strtr($value, $table);			
		return($value);		
		}		
?>