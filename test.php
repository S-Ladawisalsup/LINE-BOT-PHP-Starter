<?php

$data = file('question.txt');

echo gettype($data[0]) . '/' . gettype($data);