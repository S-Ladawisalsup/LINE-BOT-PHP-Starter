<?php

$array = file('greeting.txt');

foreach(mb_list_encodings() as $chr){ 
    echo mb_convert_encoding($array, 'UTF-8', $chr)." : ".$chr."<br>";    
}