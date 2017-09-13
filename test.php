<?php

use Google\Cloud\Translate\TranslateClient;

$text = 'The text to translate.'
$targetLanguage = 'th';

$translate = new TranslateClient();
$result = $translate->translate($text, [
    'target' => $targetLanguage,
]);
// print("Source language: $result[source]\n");
// print("Translation: $result[text]\n");
echo $result['source'] . '=>' . $result['text'];