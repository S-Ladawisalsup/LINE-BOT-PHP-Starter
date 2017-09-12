<?php

// Get POST body content
$content = file_get_contents('php://input');

echo "This is content : " . $content;