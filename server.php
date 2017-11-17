<?php

// Cannot debug to print show in page
// But will echo response to root source data is sent
if (!is_null($_POST["temperature"])) {
	echo "Temperature is " . $_POST["temperature"];
}
else {
	echo "Cannot receive any data";
}