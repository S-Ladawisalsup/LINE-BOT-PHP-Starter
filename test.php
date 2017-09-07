<?php

$data = file('greeting.txt');

$data = iconv("ISO-8859-1","UTF-8", $data);

echo count($data);