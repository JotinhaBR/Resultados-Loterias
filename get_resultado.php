<style>
.numeros{
display: inline-block;
margin: 0 12px 0 0;
font-family: "FuturaWeb",sans-serif;
font-size: 1.5rem;
color: #fff;
border-radius: 35px;
width: 67px;
padding: 20px 0;
text-align: center;
}
p{
font-family: "FuturaWeb",sans-serif;
font-size: 1rem;
}
body{
text-align: center;
}
</style>
<?php
if ($_GET['modo'] == "MegaSena"){
$url = "http://www.loterias.caixa.gov.br/wps/portal/loterias/landing/megasena/";
$class = "numbers mega-sena";
$cor = "background: #209869;";
}else
if ($_GET['modo'] == "Quina"){
$url = "http://www.loterias.caixa.gov.br/wps/portal/loterias/landing/quina/";
$class = "numbers quina";
$cor = "background: #260085;";
}else
if ($_GET['modo'] == "DuplaSena"){
$url = "http://www.loterias.caixa.gov.br/wps/portal/loterias/landing/duplasena/";
$class = "numbers dupla-sena";
$cor = "background: #BF194E;";
}else
if ($_GET['modo'] == "TimeMania"){
$url = "http://www.loterias.caixa.gov.br/wps/portal/loterias/landing/timemania/";
$class = "numbers timemania";
$cor = "background: #FFF600;color: #049645;";
}else
if ($_GET['modo'] == "LotoMania"){
$url = "http://www.loterias.caixa.gov.br/wps/portal/loterias/landing/lotomania/";
$class = "simple-table lotomania";
$cor = "color: #F78100;";
}else
if ($_GET['modo'] == "LotoFacil"){
$url = "http://www.loterias.caixa.gov.br/wps/portal/loterias/landing/lotofacil/";
$class = "simple-table lotofacil";
$cor = "color: #930989;";
}else
if ($_GET['modo'] == "Federal"){
$url = "http://www.loterias.caixa.gov.br/wps/portal/loterias/landing/federal/";
$class = "simple-table resultado-table three-column-table";
$cor = "color: #1f2a47;width: 1px;text-align: left;";
}else{
echo 'ERRO: Você deve escolher um modo valido.';
return;
}


$c = curl_init();
$cookie_file = __DIR__.DIRECTORY_SEPARATOR.$_GET['modo'].'.txt';
curl_setopt_array($c, array(
    CURLOPT_URL => $url,
    CURLOPT_REFERER => 'http://www.loterias.caixa.gov.br',
    CURLOPT_USERAGENT => 'Foo Spider',
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
        //print_r($data);
        exit;
    }
} catch (HttpException $ex) {
    print_r($ex); exit;
}

curl_close($c); 

$doc = new DOMDocument();
@$doc->loadHTML($data['content']);
unset($data);

foreach ($doc->getElementsByTagName('h2') as $h2) {
	if ((strpos($h2->textContent, 'Resultado Concurso') !== false)) {
		print_r("<p>".$h2->textContent."</p>");
	}
}

foreach ($doc->getElementsByTagName('h3') as $h3) {
	if (($h3->getAttribute('class') == 'epsilon') && (strpos($h3->textContent, 'Acumulou') !== false)) {
		print_r("<h2>".$h3->textContent."</h2>");
	}
}
foreach ($doc->getElementsByTagName('p') as $p) {
	if (($p->getAttribute('class') == 'description') && (strpos($p->textContent, 'Sorteio') !== false)) {
		print_r("<p>".$p->textContent."</p><br>");
	}
}


if ($_GET['modo'] == "DuplaSena"){
echo "<h3>1º sorteio</h3>";
foreach ($doc->getElementsByTagName('ul') as $tag) {
    if ($tag->getAttribute('class') == $class) {
        $data = trim(str_replace("1º sorteio", "", str_replace("2º sorteio", "", $tag->textContent)));
    }
}

$arr = str_split($data, 2);
$contar = 0;
foreach ($arr as $value) {
print_r("<span class='numeros' style='".$cor."'>".$arr[$contar]."</span>");
$contar = $contar+1;
}
echo "<h3>2º sorteio</h3>";
foreach ($doc->getElementsByTagName('ul') as $tag) {
    if ($tag->getAttribute('class') == $class) {
        $data2 = trim(str_replace("1º sorteio", "", str_replace("2º sorteio", "", $tag->textContent)));
        break;
    }
}

$arr2 = str_split($data2, 2);
$contar2 = 0;
foreach ($arr2 as $value) {
print_r("<span class='numeros' style='".$cor."'>".$arr2[$contar2]."</span>");
$contar2 = $contar2+1;
}
}else
if (($_GET['modo'] == "LotoFacil") || ($_GET['modo'] == "LotoMania")){
foreach ($doc->getElementsByTagName('table') as $tag) {
    if ($tag->getAttribute('class') == $class) {
		print_r("<span class='numeros' style='".$cor."display: inline;'>".$tag->textContent."</span>");
    }
}
}else
if ($_GET['modo'] == "TEST"){
foreach ($doc->getElementsByTagName('table') as $tag) {
    if ($tag->getAttribute('class') == $class) {
		print_r("<span class='numeros' style='".$cor."display: inline;'>".$tag->textContent."</span>");
    }
}
}else
if ($_GET['modo'] == "Federal"){
echo '<span class="numeros" style="'.$cor.'width: auto;position: absolute;text-align: right;right: 52%;"><br>Destinoº<br>Bilhete<br>Valor do Prêmio (R$)</span><br><br>';
foreach ($doc->getElementsByTagName('tr') as $tag) {
    if (strpos($tag->textContent, 'º') !== false) {
		print_r("<div class='numeros' style='".$cor."'>".$tag->textContent."</div><br>");
    }
}
}else{
foreach ($doc->getElementsByTagName('ul') as $tag) {
    if ($tag->getAttribute('class') == $class) {
        $data = trim(str_replace("1º sorteio", "", str_replace("2º sorteio", "", $tag->textContent)));
    }
}

$arr = str_split($data, 2);
$contar = 0;
foreach ($arr as $value) {
print_r("<span class='numeros' style='".$cor."'>".$arr[$contar]."</span>");
$contar = $contar+1;
}
}

foreach ($doc->getElementsByTagName('div') as $div) {
    if ($div->getAttribute('class') == 'next-prize clearfix') {
        print_r("<br><br><p>".$div->nodeValue."</p>");
    }
    if ($div->getAttribute('class') == 'totals') {
        print_r("<p>".$div->nodeValue."</p><br>");
    }
}
if ($_GET['modo'] != "Federal"){
echo '<h3>Premiação</h3>';
}

foreach ($doc->getElementsByTagName('p') as $p) {
if (($_GET['modo'] == "LotoFacil") || ($_GET['modo'] == "LotoMania")){
	if (($p->getAttribute('class') == 'description') && (strpos($p->textContent, ' acertos') !== false)) {
		print_r("<p>".$p->textContent."</p>");
}
}else
	if (($p->getAttribute('class') == 'description') && (strpos($p->textContent, ' números acertados') !== false)) {
		print_r("<p>".$p->textContent."</p>");
}
}

?>
