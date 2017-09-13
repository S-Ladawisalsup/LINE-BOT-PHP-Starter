<?php
$str = '@123 E l e 34แปล pha 45n-=งะt';
$str = preg_replace('/[^a-z]/i', '', $str);
echo $str;