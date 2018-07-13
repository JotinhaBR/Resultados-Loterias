<style>
.numeros{
display: inline-block;
margin: 0 12px 0 0;
font-family: "FuturaWeb",sans-serif;
font-size: 3rem;
color: #fff;
border-radius: 35px;
width: 67px;
padding: 20px 0;
text-align: center;
}
p{
font-family: "FuturaWeb",sans-serif;
font-size: 2rem;
}
body{
text-align: center;
font-size: 2rem;
}
</style>
<link rel="stylesheet" href="bootstrap.min.css">
<?php

federal();

//Pegar Numeros da Federal
function federal() {
$url = "http://www.loterias.caixa.gov.br/wps/portal/loterias/landing/federal/";

$federal = new Connect($url,"federal");


foreach ($federal->doc->getElementsByTagName('h2') as $h2) {
	if ((strpos($h2->textContent, 'Resultado Concurso') !== false)) {
		print_r("<p>".$h2->textContent."</p>");
	}
}

foreach ($federal->doc->getElementsByTagName('h3') as $h3) {
	if (($h3->getAttribute('class') == 'epsilon') && (strpos($h3->textContent, 'Acumulou') !== false)) {
		print_r("<h2>".$h3->textContent."</h2>");
	}
}
foreach ($federal->doc->getElementsByTagName('p') as $p) {
	if (($p->getAttribute('class') == 'description') && (strpos($p->textContent, 'Sorteio') !== false)) {
		print_r("<p>".$p->textContent."</p><br>");
	}
}

echo "<span style='font-size: 3rem;'>Destino <span style='margin-left: 15px;'></span> Bilhete <span style='margin-left: 15px;'></span> Valor do Prêmio (R$)</span><br>";

foreach ($federal->doc->getElementsByTagName('table') as $pagina) {
    if ($pagina->getAttribute('class') == "simple-table resultado-table three-column-table") {
$string = $pagina->textContent;
$string = preg_replace('/\s/','#',$string);
$string = str_replace ('Destino##########Bilhete##########Valor#do#Prêmio#(R$)#########','', $string);
$string = str_replace('########',' <span style="margin-left: 15px;"></span> ',$string);
$string = str_replace('##',' <span style="margin-left: 15px;"></span> ',$string);
$string = str_replace('#','</br>',$string);
echo "<span style='font-size: 3rem;'>".$string."</span>";
}
}

}

// Sistema para conectar no WebSite da Caixa
class Connect {
public $doc;
public function __construct($url, $modo) {
$c = curl_init();
$cookie_file = __DIR__.DIRECTORY_SEPARATOR.$modo.'.txt';
curl_setopt_array($c, array(
    CURLOPT_URL => $url,
    CURLOPT_REFERER => 'http://www.loterias.caixa.gov.br',
    CURLOPT_USERAGENT => 'AdrianCF PlanetsWEB',
    CURLOPT_HEADER => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 6,
    CURLOPT_TIMEOUT => 6,
    CURLOPT_MAXREDIRS => 1,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIESESSION => true,
    CURLOPT_COOKIEFILE => $cookie_file,
    CURLOPT_COOKIEJAR => $cookie_file
));

try {
    $content = curl_exec($c);
    $data = curl_getinfo($c);
    $data['content'] = $content;
    unset($content);
    $data['errno'] = curl_errno($c);
    $data['errmsg'] = curl_error($c);
    if ((int)$data['errno'] !== 0 || (int)$data['http_code'] !== 200) {
        echo 'error number: '.$data['errno'];
        echo 'error message: '.$data['errmsg'];
        echo 'http status: '.$data['http_code'];
        exit;
    }
} catch (HttpException $ex) {
    print_r($ex); exit;
}

curl_close($c); 

$doc = new DOMDocument();
@$doc->loadHTML($data['content']);
unset($data);
$this->doc = $doc;
}
}






?>
