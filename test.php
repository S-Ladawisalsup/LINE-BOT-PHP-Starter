<?php

use Google\Cloud\Translate\TranslateClient;

$text = 'Elephant';
$targetLanguage = 'th';

$translate = new TranslateClient();
$result = $translate->translate($text, [
    'target' => $targetLanguage,
]);
// print("Source language: $result[source]\n");
// print("Translation: $result[text]\n");
echo $result['source'] . '=>' . $result['text'];