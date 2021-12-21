# msd-convert
 <p>MultiSlavDict conversion files</p>
 <p>Conversion is ready to be searched in <a href="https://slavistik-portal.de/msd/"><b>MultiSlavDict</b></a> at Slavistik-Portal</p>

<h2>1. KratkijSlovar - Kratkij slovarʹ šesti slavjanskich jazykov, Spb 1885</h2>

 <h3>Procedure</h3>
 <p>Scripts are working with every PHP version. Recommendable is PHP-CLI 7.2</p>
 <ul>
   <li>raw files are located in './htm' folder for the download</li>
   <li>split.php - splitting Data into pages, error correction, language marking, word and char analysis</li>
   <li>data_import.php - import data into SQlite database</li>
   <li>data_pr.php - process data, further splitting of language data (Russian vs. Church Slavonic)</li>
   <li>ks_2solr.php - conversion to Solr post xml file, prepare suggestion list</li>
 </ul></p>

<h2>2. LexiconPGL	- Lexicon palaeoslovenico-graeco-latinum / Franz Miklosich. Vindobonae : Braumueller, 1862-1865.</h2>

 <h3>Procedure</h3>
 <p>Scripts are working with every PHP version. Recommendable is PHP-CLI 7.2</p>
 <ul>
   <li>raw files are located in './htm' folder for the download</li>
   <li>split.php - splitting Data into pages, error correction, language marking, word and char analysis</li>
   <li>data_import.php - import data into SQlite database</li>
   <li>data_pr.php - process data, further splitting of language data (Church Slavonic, Greek, Latin)</li>
   <li>ks_2solr.php - conversion to Solr post xml file, prepare suggestion list</li>
 </ul></p>
 
 <h2>3. SlownikPRN	- Słownik polsko-rossyisko-niemiecki / Johann Adolf Erdmann Schmidt. - w Wrocławiu : W. B. Korn, 1834.</h2>

 <h3>Procedure</h3>
 <p>Scripts are working with every PHP version. Recommendable is PHP-CLI 7.2</p>
 <ul>
   <li>raw files are located in './htm' folder for the download</li>
   <li>split.php - splitting Data into pages, error correction, language marking, word and char analysis</li>
   <li>data_import.php - import data into SQlite database</li>
   <li>data_pr.php - process data, further splitting of language data (German, Polish)</li>
   <li>ks_2solr.php - conversion to Solr post xml file, prepare suggestion list</li>
 </ul></p>
