<?php
$str = '@123 E l e 34แปล pha 45nงะt';
$str = preg_replace('/[^a-z0-9_ ]/i', '', $str);
echo $str;