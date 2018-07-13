<html><meta charset="UTF-8">
<style>
.numeros{
display: inline-block;
margin: 0 12px 0 0;
font-family: "FuturaWeb",sans-serif;
font-size: 3rem;
color: #fff;
border-radius: 35px;
width: 77px;
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
</head> </html>
<?php

Loteca();

//Pegar Numeros da Loteca
function Loteca() {
$url = "http://www.lotece.com.br/v2/";

$Loteca = new Connect($url,"Loteca");

foreach ($Loteca->doc->getElementsByTagName('div') as $pagina) {
    if ($pagina->getAttribute('class') == "dataResultado") {
$string = $pagina->textContent;
$string = preg_replace('/\s/','#',$string);
$string = str_replace('########','',$string);
$string = str_replace('##',' ',$string);
$string = str_replace ('ExtraÃ§Ã£o','Extração', $string);
$string = str_replace('PRÃMIO','PRÊMIO',$string);
$string = str_replace('Â°','º',$string);
$string = str_replace('#','',$string);
$string = str_replace('1ºPRÊMIO','<div class="row"><div class="col-md-6" style="width: 50%;float: left;" align="right"></br>1º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('2ºPRÊMIO','</span></br>2º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('3ºPRÊMIO','</span></br>3º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('4ºPRÊMIO','</span></br>4º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('5ºPRÊMIO','</span></br>5º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('6ºPRÊMIO','</span></div><div class="col-md-6" style="width: 50%;float: left;" align="left"></br>6º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('7ºPRÊMIO','</span></br>7º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('8ºPRÊMIO','</span></br>8º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('9ºPRÊMIO','</span></br>9º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('10ºPRÊMIO','</span></br>10º PRÊMIO</br><span class="numeros" style="background: #362b24;">',$string);
$string = str_replace('Dia','</span></div></div></br></br>Dia ',$string);
$string = str_replace('Exibirmais... ','',$string);
$string = str_replace('Extraçãodas',' Extração das ',$string);
echo $string;
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
    CURLOPT_REFERER => 'http://www.lotece.com.br',
    CURLOPT_USERAGENT => 'AdrianCF PlanetsWEB',
    CURLOPT_HEADER => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 6,
    CURLOPT_TIMEOUT => 30,
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
