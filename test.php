<?php

$array = file('greeting.txt');

file_put_contents("greeting.txt", "\xEF\xBB\xBF" . $data);

echo count($data);