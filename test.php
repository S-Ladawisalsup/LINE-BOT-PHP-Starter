<?php
function translate($from_lan='en', $to_lan='th', $text){
    $json = json_decode(file_get_contents('https://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=' . urlencode($text) . '&langpair=' . $from_lan . '|' . $to_lan));
    $translated_text = $json->responseData->translatedText;

    return $translated_text;
}

$str = '@123e l e 34แปล pha 45n-=งะt//s';
$str = preg_replace('/[^A-Za-z]/', '', $str);
$str = ucfirst(strtolower($str));
$str = translate($str);
echo $str;