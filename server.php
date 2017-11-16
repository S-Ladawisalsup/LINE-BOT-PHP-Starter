<?php

if (!is_null($_POST["temperature"])) {
	header("Refresh:0");
	echo "Temperature is " . $_POST["temperature"];
}
else {
	echo "Cannot receive any temperature data";
}