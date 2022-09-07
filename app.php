<?php

error_reporting(0);

function GetStr($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return trim(strip_tags(substr($string, $ini, $len)));
}

$bindata = $_GET['bin'];
if (strlen($bindata) > 6) {
       $bin = substr($bindata, 0,6);
}
elseif (strlen($bindata) < 6) {
        exit('invalid_bin_provided');
}
elseif (strlen($bindata) == 6) {
        $bin = $bindata;
}

$header = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
        'Accept-Encoding: none',
        'Accept-Language: en-US,en;q=0.8',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'Content-Type: application/x-www-form-urlencoded',
        'Host: bins.su',
        'Origin: http://bins.su',
        'Referer: http://bins.su/',
        'Sec-GPC: 1',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36',
);

$payload = 'action=searchbins&bins='.$bin.'&bank=&country=';

$url = 'http://bins.su/';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$request = curl_exec($ch);
// echo "<b>Result:</b> $request </br>";
$data = GetStr($request, 'id="result">','</div>');
// echo "<br>Data: $data<br>";

if ($data == "No bins found!") {
        echo "no_data_found";
}

elseif (strpos($data, "found 1 bins") && strpos($data, "$bin")) {
        $country = GetStr($request, "$bin</td><td>","</td>");
        $vendor = GetStr($request, "$bin</td><td>$country</td><td>","</td>");
        $type = GetStr($request, "$bin</td><td>$country</td><td>$vendor</td><td>","</td>");
        $level = GetStr($request, "$bin</td><td>$country</td><td>$vendor</td><td>$type</td><td>","</td>");
        $bank = GetStr($request, "$bin</td><td>$country</td><td>$vendor</td><td>$type</td><td>$level</td><td>","</td>");
}
else{
        echo "Server Side Processing Error";
}

$return = array(
        'bin' => $bin,
        'country' => $country,
        'vendor' => $vendor,
        'type' => $type,
        'level' => $level,
        'bank' => $bank
);
$return = json_encode($return);

echo "$return";
// echo "<br>BIN: $bin<br>Country: $country<br>Vendor: $vendor<br>Type: $type<br>Level: $level<br>Bank: $bank<br>";

curl_close($ch);
?>