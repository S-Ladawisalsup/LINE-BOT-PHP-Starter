<?php

$ip = "192.1.100.249";

$exe = shell_exec("ping -n 3 $ip");

if (strrpos($exe,"100% loss") > 0) {
	echo "เซิฟเวอร์ล่มแล้วจ้า";
}
else { 
	echo "เซิฟเวอร์ยังไม่ล่มแล้วจ้า";
}