<?php

    ini_set('memory_limit', '-1');

				
		// Create file
		$qname = "ks_2solr-".date("Ymd",time()).".xml";
		$q = fopen($qname,'w');
		fputs($q, $text);
		fclose($q);
		$text ='';

		$text = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
		$text .= '<add>' . PHP_EOL;
	
		$pdo = new PDO('sqlite:./db/kratslov.sqlite');
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
			$text .= '<field name="id">ks_'.FormatXML($row[rowid]).'</field>' . PHP_EOL;
			}
			
			// https://de.wikipedia.org/wiki/Liste_der_ISO-639-1-Codes
			if ($row[col_1] !='') {
				$page = $row[col_11];
			$text .= '<field name="rus">'.FormatXML($row[col_1]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_1],'rus');
			s2sg($row[col_1],'rus');
			}
			
			if ($row[col_2] !='') {
				$page = $row[col_11];
			$text .= '<field name="chu">'.FormatXML($row[col_2]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_2],'chu');
			s2sg($row[col_2],'chu');
			}
			
			if ($row[col_3] !='') {
				$page = $row[col_11];
			$text .= '<field name="bul">'.FormatXML($row[col_3]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_3],'bul');
			s2sg($row[col_3],'bul');
			}			
			
			if ($row[col_4] !='') {
				$page = $row[col_11];
			$text .= '<field name="srp">'.FormatXML($row[col_4]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_4],'srp');
			s2sg($row[col_4],'srp');
			}
			
			if ($row[col_5] !='') {
				$page = $row[col_11];
			$text .= '<field name="cze">'.FormatXML($row[col_5]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_5],'cze');
			s2sg($row[col_5],'cze');
			}			

			if ($row[col_6] !='') {
				$page = $row[col_11] - 1;
			$text .= '<field name="pol">'.FormatXML($row[col_6]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_6],'pol');
			s2sg($row[col_6],'pol');
			}
			
			if ($row[col_7] !='') {
				$page = $row[col_11] - 1;
			$text .= '<field name="fre">'.FormatXML($row[col_7]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_7],'fre');
			s2sg($row[col_7],'fre');
			}			
			
			if ($row[col_8] !='') {
				$page = $row[col_11] - 1;
			$text .= '<field name="ger">'.FormatXML($row[col_8]).'</field>' . PHP_EOL;
			#$text .= s2s($row[col_8],'ger');
			s2sg($row[col_8],'ger');
			}
			
			if ($row[col_9] !='') {
			$text .= '<field name="charo">'.FormatXML($row[col_9]).'</field>' . PHP_EOL;
			}			
			
			if ($row[col_10] !='') {
			$text .= '<field name="chart">'.FormatXML($row[col_10]).'</field>' . PHP_EOL;
			}

			if ($row[col_11] !='') {
			$text .= '<field name="page">'.FormatXML($page).'</field>' . PHP_EOL;
			}

			if ($row[col_12] !='') {
			$text .= '<field name="line">'.FormatXML($row[col_12]).'</field>' . PHP_EOL;
			}
			
			$text .= '<field name="stitle">KratkijSlovar</field>' . PHP_EOL;
			
			
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
		file_put_contents('txt/sug.txt',$sugtext);

		
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
			$ac = explode("|",$acompl);
			$ac = array_unique($ac);
			for ($i = 0; $i < count($ac); $i++) {
				$ac[$i] = trim($ac[$i]);
				if ($ac[$i] !='' && mb_strlen($ac[$i], 'UTF-8')>1) {$sugtext .= $ac[$i] . '	'. transl($ac[$i]) . '	' . $spr . PHP_EOL;}
			}
		
	}	
	
	function FormatXML($value) {
		$value = trim($value);		
		$value = htmlspecialchars($value, ENT_COMPAT, "UTF-8");
		return($value);
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
		
    );
    return strtr($string, $t);
}

?>