<?php

    ini_set('memory_limit', '-1');

				
		// Create file
		$qname = "mlex_2solr-".date("Ymd",time()).".xml";
		$q = fopen($qname,'w');
		fputs($q, $text);
		fclose($q);
		$text ='';
		$cdate = date ("Y-m-d");
		
		$text = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
		$text .= '<add>' . PHP_EOL;
	
		$pdo = new PDO('sqlite:./db/mlexicon.sqlite');
		$pdo->beginTransaction();

		$query = $pdo->query("SELECT rowid,* FROM main.fdata"); 
		
		$c = 1;
		$c2 = 1;

		foreach($query as $row){
			echo $c .PHP_EOL;
			#if ($c >2) {break;}

			// start-doc
			if ($row[rowid] !='') {$text .= '<doc>'. PHP_EOL;} else {continue;}	

			if ($row[rowid] !='') {
			$text .= '<field name="id">mlx_'.FormatXML($row[rowid]).'</field>' . PHP_EOL;
			}
			
			// https://de.wikipedia.org/wiki/Liste_der_ISO-639-1-Codes
			
			if ($row[col_2] !='') {
				$page = $row[col_4];
				$row[col_2] = preg_replace("@\[ksl=(.*?)\]@m",'<ksl>$1</ksl>',$row[col_2]);
			$text .= '<field name="chu" boost="2.0">'.FormatXML($row[col_2]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_2],'chu');
			s2sg($row[col_2],'chu');
			}
			
			if ($row[col_3] !='') {
				$row[col_3] = preg_replace("@\[ksl=(.*?)\]@m",'<ksl>$1</ksl>',$row[col_3]);
				$row[col_3] = preg_replace("@⟨@m",'<qu>',$row[col_3]);
				$row[col_3] = preg_replace("@⟩@m",'</qu>',$row[col_3]);
			$text .= '<field name="mlx_entry">'.FormatXML($row[col_3]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_3],'bul');
			}			
			
			if ($row[col_5] !='') {
						
						$agre = explode("|",$row[col_5]);
						for ($g = 0; $g < count($agre); $g++) {
							if ($agre[$g] !='') {
								$text .= '<field name="gre">'.FormatXML($agre[$g]).'</field>' . PHP_EOL;
								s2sg($agre[$g],'gre');
								}
						}
				#$row[col_5] = preg_replace("@\|@m",' ',$row[col_5]);
				#$text .= '<field name="gre">'.FormatXML($row[col_5]).'</field>' . PHP_EOL;
			}
			$agre = null;

			if ($row[col_4] !='') {
			$text .= '<field name="page">'.FormatXML($page).'</field>' . PHP_EOL;
			}

			if ($row[col_6] !='') {
				$row[col_6] = preg_replace("@\[ksl=@m",'',$row[col_6]);
				$row[col_6] = preg_replace("@\]@m",'',$row[col_6]);
				$row[col_6] = preg_replace("@\.@m",'',$row[col_6]);
			$text .= '<field name="chart">'.FormatXML(chtr($row[col_6])).'</field>' . PHP_EOL;
			$text .= '<field name="charo">'.FormatXML($row[col_6]).'</field>' . PHP_EOL;
			}
			
			$text .= '<field name="stitle">LexiconPGL</field>' . PHP_EOL;
			$text .= '<field name="cdate">'.$cdate.'</field>' . PHP_EOL;
			
			
			// end-doc
			if ($row[rowid] !='') {$text .= '</doc>'. PHP_EOL; /* echo $c . $row[rowid] . PHP_EOL; */ $c++; $c2++;}
		
				if ($c2 == 10001 ) {
					$text = text2file($text,$qname); // Schreiben in eine Datei
					echo "schreiben" . PHP_EOL;
					$c2 = 1;
					$text = '';
					}
					
			
			} // End of rows
			
		$text .= '</add>' . PHP_EOL;

		text2file($text,$qname);
		file_put_contents('sug.txt',$sugtext);

		
		function text2file($text,$qname)
		{
			// Add to fille
			$q = fopen($qname,'a');
			fputs($q, $text);
			fclose($q);
		}
  

		$pdo->commit();
		unset($query);
		unset($pdo);
	

	function s2s($value,$spr) {
			$acompl = preg_replace('@\{.*?\}@','',$value);
			$acompl = preg_replace('@[:;,]@','|',$acompl);
			$ac = explode("|",$acompl);
			$ac = array_unique($ac);
			for ($i = 0; $i < count($ac); $i++) {
				if ($ac[$i] !='') {$text .= '<field name="'.$spr.'_s">'.FormatXML($ac[$i]).'</field>' . PHP_EOL; $m = 'ja';}
			}			
		
		if ($m == 'ja') {return($text);} else {return('');}
		
	}
	
	function s2sg($value,$spr) {
		
		global $sugtext;
		
			$value = preg_replace('@ѹ@u','у',$value);
			
			$acompl = preg_replace('@\{.*?\}@','',$value);
			$acompl = preg_replace('@[:;,]@','|',$acompl);
			$acompl = preg_replace("/-/","–",$acompl); // Trennstrich durch Gedanken
			$acompl = preg_replace("/-/","–",$acompl); // 2.Trennstrich durch Gedanken
			$acompl = preg_replace("/<ksl>/","",$acompl); 
			$acompl = preg_replace("/<\/ksl>/","",$acompl); 
			$ac = explode("|",$acompl);
			$ac = array_unique($ac);
			for ($i = 0; $i < count($ac); $i++) {
				$ac[$i] = trim($ac[$i]);
				if ($ac[$i] !='' && mb_strlen($ac[$i], 'UTF-8')>1) {$sugtext .= $ac[$i] . '	'. transl($ac[$i]) . '	' . $spr . '	' . 'LexiconGSL' . PHP_EOL;}
			}
		
	}	
	
	function FormatXML($value) {
		$value = trim($value);		
		$value = htmlspecialchars($value, ENT_COMPAT, "UTF-8");
		return($value);
	}

function chtr($string) {
	    $t = array(
			'А' => 'A',
			'Б' => 'B',
			'В' => 'V',
			'Г' => 'G',
			'Д' => 'D',
			'Е' => 'E',
			'Ж' => 'Ž',
			'З' => 'Z',
			'И' => 'I',
			'К' => 'K',
			'Л' => 'L',
			'М' => 'M',
			'Н' => 'N',
			'О' => 'O',
			'П' => 'P',
			'Р' => 'R',
			'С' => 'S',
			'Т' => 'T',
			'У' => 'U',
			'Ф' => 'F',
			'Х' => 'Ch',
			'Ц' => 'C',
			'Ч' => 'Č',
			'Ш' => 'Š',
			'Щ' => 'ŠČ',
			'Э' => 'E',
			'Ю' => 'Ju',
			'Я' => 'Ja',
			'Ѳ' => 'F',
			'Ѥ' => 'Je',
			'Ꙗ' => 'Ja',
			'Оу' => 'U',
			'Ѭ' => 'Jǫ',
			'Ѫ' => 'Ǫ',
			'Ѩ' => 'Ę',
			'Ѩ' => 'Ję',
			'Ѯ' => 'Ks',
			'Ѱ' => 'Ps',
			
    );
    return strtr($string, $t);			
}

function transl($string) {
	$string = preg_replace("/[ъЪ][\s,\.]/u","",$string);
	$string = preg_replace("/^der\s/mu","",$string);
	$string = preg_replace("/^die\s/mu","",$string);
	$string = preg_replace("/^das\s/mu","",$string);
	$string = preg_replace("/^des\s/mu","",$string);
	$string = preg_replace("/^la\s/mu","",$string);
	$string = preg_replace("/^les\s/mu","",$string);

		
 			
    $t = array(
			'É' => 'e',
			'ß' => 'ss',
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ä' => 'a',
			'å' => 'a',
			'ç' => 'c',
			'è' => 'e',
			'é' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'ö' => 'o',
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			'ü' => 'u',
			'ý' => 'y',
			'ă' => 'a',
			'ą' => 'a',
			'ć' => 'c',
			'Č' => 'c',
			'č' => 'c',
			'ď' => 'd',
			'ę' => 'e',
			'ě' => 'e',
			'ľ' => 'l',
			'Ł' => 'l',
			'ł' => 'l',
			'ń' => 'n',
			'ň' => 'n',
			'œ' => 'oe',
			'ŕ' => 'r',
			'Ř' => 'r',
			'ř' => 'r',
			'ś' => 's',
			'Š' => 's',
			'š' => 's',
			'ť' => 't',
			'ů' => 'u',
			'ź' => 'z',
			'Ż' => 'z',
			'ż' => 'z',
			'Ž' => 'z',
			'ž' => 'z',
			'ǹ' => 'n',
			'ȋ' => 'i',
			'̀' => '',
			'́' => '',
			'̂' => '',
			'̇' => '',
			'̈' => '',
			'̋' => '',
			'̏' => '',
			'̑' => '',
			'І' => 'i',
			'Ј' => 'j',
			'А' => 'a',
			'Б' => 'b',
			'В' => 'v',
			'Г' => 'g',
			'Д' => 'd',
			'Е' => 'e',
			'Ж' => 'z',
			'З' => 'z',
			'И' => 'i',
			'К' => 'k',
			'Л' => 'l',
			'М' => 'm',
			'Н' => 'n',
			'О' => 'o',
			'П' => 'p',
			'Р' => 'r',
			'С' => 's',
			'Т' => 't',
			'У' => 'u',
			'Ф' => 'f',
			'Х' => 'ch',
			'Ц' => 'c',
			'Ч' => 'c',
			'Ш' => 's',
			'Щ' => 'sc',
			'Ъ' => '',
			'Ы' => 'y',
			'Ь' => '',
			'Э' => 'e',
			'Ю' => 'ju',
			'Я' => 'ja',
			'а' => 'a',
			'б' => 'b',
			'в' => 'v',
			'г' => 'g',
			'д' => 'd',
			'е' => 'e',
			'ж' => 'z',
			'з' => 'z',
			'и' => 'i',
			'й' => 'j',
			'к' => 'k',
			'л' => 'l',
			'м' => 'm',
			'н' => 'n',
			'о' => 'o',
			'п' => 'p',
			'р' => 'r',
			'с' => 's',
			'т' => 't',
			'у' => 'u',
			'ф' => 'f',
			'х' => 'ch',
			'ц' => 'c',
			'ч' => 'c',
			'ш' => 's',
			'щ' => 'sc',
			'ъ' => '',
			'ы' => 'y',
			'ь' => '',
			'э' => 'e',
			'ю' => 'ju',
			'я' => 'ja',
			'ё' => 'e',
			'ђ' => 'd',
			'є' => 'e',
			'ѕ' => 's',
			'і' => 'i',
			'ї' => 'i',
			'ј' => 'j',
			'љ' => 'l',
			'њ' => 'n',
			'ћ' => 'c',
			'џ' => 'dz',
			'Ѣ' => 'e',
			'ѣ' => 'e',
			'ѥ' => 'e',
			'ѧ' => 'ja',
			'ѩ' => 'ja',
			'ѫ' => 'ju',
			'ѭ' => 'ju',
			'Ѳ' => 'f',
			'ѳ' => 'f',
			'ѵ' => 'y',
			'ӑ' => 'a',
			'ө' => 'f',
			'ṕ' => 'p',
			'ẃ' => 'w',
			'ỳ' => 'y',
			'ꙗ' => 'ja',
			'ѹ' => 'u',

			//Griechisch
			 'ΐ' => 'i',
			 'Α' => 'a',
			 'Δ' => 'd',
			 'Σ' => 's',
			 'Χ' => 'ch',
			 'ά' => 'a',
			 'ί' => 'i',
			 'ΰ' => 'y',
			 'α' => 'a',
			 'β' => 'v',
			 'γ' => 'g',
			 'δ' => 'd',
			 'ε' => 'e',
			 'ζ' => 'z',
			 'η' => 'e',
			 'θ' => 'th',
			 'ι' => 'i',
			 'κ' => 'k',
			 'λ' => 'l',
			 'μ' => 'm',
			 'ν' => 'n',
			 'ξ' => 'ks',
			 'ο' => 'o',
			 'π' => 'p',
			 'ρ' => 'r',
			 'ς' => 's',
			 'σ' => 's',
			 'τ' => 't',
			 'υ' => 'y',
			 'φ' => 'f',
			 'χ' => 'ch',
			 'ψ' => 'ps',
			 'ω' => 'o',
			 'ϊ' => 'i',
			 'ϋ' => 'y',
			 'ό' => 'o',
			 'ϑ' => 'th',
			 'ϕ' => 'f',
			 'Ϝ' => 'f',
			 'ϰ' => 'k',
			 'ἀ' => 'a',
			 'ἁ' => 'a',
			 'ἂ' => 'a',
			 'ἄ' => 'a',
			 'ἅ' => 'a',
			 'ἆ' => 'a',
			 'Ἄ' => 'a',
			 'ἐ' => 'e',
			 'ἑ' => 'e',
			 'ἓ' => 'e',
			 'ἔ' => 'e',
			 'ἕ' => 'e',
			 'ἠ' => 'e',
			 'ἡ' => 'e',
			 'ἢ' => 'e',
			 'ἣ' => 'e',
			 'ἤ' => 'e',
			 'ἥ' => 'e',
			 'ἦ' => 'e',
			 'ἧ' => 'e',
			 'ἰ' => 'i',
			 'ἱ' => 'i',
			 'ἳ' => 'i',
			 'ἴ' => 'i',
			 'ἵ' => 'i',
			 'ἶ' => 'i',
			 'ἷ' => 'i',
			 'ὀ' => 'o',
			 'ὁ' => 'o',
			 'ὂ' => 'o',
			 'ὃ' => 'o',
			 'ὄ' => 'o',
			 'ὅ' => 'o',
			 'ὐ' => 'y',
			 'ὑ' => 'y',
			 'ὓ' => 'y',
			 'ὔ' => 'y',
			 'ὕ' => 'y',
			 'ὖ' => 'y',
			 'ὗ' => 'y',
			 'ὠ' => 'o',
			 'ὡ' => 'o',
			 'ὢ' => 'o',
			 'ὤ' => 'o',
			 'ὥ' => 'o',
			 'ὦ' => 'o',
			 'ὧ' => 'o',
			 'ὰ' => 'a',
			 'ά' => 'a',
			 'ὲ' => 'e',
			 'έ' => 'e',
			 'ὴ' => 'e',
			 'ή' => 'e',
			 'ὶ' => 'i',
			 'ί' => 'i',
			 'ὸ' => 'o',
			 'ό' => 'o',
			 'ὺ' => 'y',
			 'ύ' => 'y',
			 'ὼ' => 'o',
			 'ώ' => 'o',
			 'ᾄ' => 'a',
			 'ᾅ' => 'a',
			 'ᾆ' => 'a',
			 'ᾔ' => 'e',
			 'ᾠ' => 'o',
			 'ᾤ' => 'o',
			 'ᾧ' => 'o',
			 'ᾱ' => 'a',
			 'ᾳ' => 'a',
			 'ᾴ' => 'a',
			 'ᾶ' => 'a',
			 'ᾷ' => 'a',
			 'ῂ' => 'e',
			 'ῃ' => 'e',
			 'ῄ' => 'e',
			 'ῆ' => 'e',
			 'ῇ' => 'e',
			 'ῐ' => 'i',
			 'ῑ' => 'i',
			 'ῒ' => 'i',
			 'ΐ' => 'i',
			 'ῖ' => 'i',
			 'ῠ' => 'y',
			 'ῡ' => 'y',
			 'ΰ' => 'y',
			 'ῥ' => 'r',
			 'ῦ' => 'y',
			 'Ῥ' => 'r',
			 'ῳ' => 'o',
			 'ῴ' => 'o',
			 'ῶ' => 'o',
			 'ῷ' => 'o',
		
    );
    return strtr($string, $t);
}

?>