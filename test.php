<?php
$str = '@123a E l e 34แปล pha 45n-=งะt//s';
$str = preg_replace('/[^A-Za-z]/', '', $str);
$str = ucfirst(strtolower($str));
echo $str;