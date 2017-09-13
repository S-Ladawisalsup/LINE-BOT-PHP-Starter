<?php
$str = '@123 E l e 34แปล pha 45nงะt';
$str = preg_replace('/[^\00-\255]+/u', '', $str);
echo $str;