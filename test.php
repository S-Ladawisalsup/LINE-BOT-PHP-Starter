<?php

// $data = file('greeting.txt');

// $data = iconv("ISO-8859-1","UTF-8", $data);

// echo count($data);

/* Detect character encoding with current detect_order */
echo mb_detect_encoding($str);

/* "auto" is expanded according to mbstring.language */
echo mb_detect_encoding($str, "auto");

/* Specify encoding_list character encoding by comma separated list */
echo mb_detect_encoding($str, "JIS, eucjp-win, sjis-win");

/* Use array to specify encoding_list  */
$ary[] = "ASCII";
$ary[] = "JIS";
$ary[] = "EUC-JP";
echo mb_detect_encoding($str, $ary);