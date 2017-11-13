<?php

$ip = "192.1.254.75";

$exe = shell_exec("ping -n 3 $ip");

if (strrpos($exe,"100% loss") > 0) {
	echo $ip . " เซิฟเวอร์ล่มแล้วจ้า";
}
else { 
	echo $ip . " เซิฟเวอร์ยังไม่ล่มจ้า";
}