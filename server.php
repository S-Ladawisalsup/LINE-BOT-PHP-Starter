<?php

if (!is_null($_POST["temperature"])) {
	header("Refresh:0");
	echo "Temperature is " . $_POST["temperature"];// . ' at time ' . $_POST["timestamp"];
}
else {
	echo "Cannot receive any temperature data";
}