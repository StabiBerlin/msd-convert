# msd-convert
 MultiSlavDict conversion files

<h2>1. Kratslov - Kratkij slovarʹ šesti slavjanskich jazykov, Spb 1885</h2>

 <h3>Procedure</h3>
 <p>Scripts are working with every PHP version. Recommendable is PHP-CLI 7.2</p>
 <ul>
   <li>split.php - splitting Data into pages, error correction, language marking, word and char analysis</li>
   <li>data_import.php - import data into SQlite database</li>
   <li>data_pr.php - process data, further splitting of language data (Russian vs. Church Slavonic)</li>
   <li>ks_2solr.php - conversion to Solr post xml file, prepare suggestion list</li>
 </ul></p>
