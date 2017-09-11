<?php
$numstick = 0;
$randpack = rand(1,2);
if($randpack=1);
{
	$ra1 = rand(0,2);
	if($ra1 = 0);
	{
		$ra2 = rand(0,18);
		if($ra2=18);
		{
			$ra2 = 21;//
			$numstick = $ra2;
		}
		else
		{
			$numstick = $ra2;
		}
	}
	else if($ra1 = 1)
	{
		$ra2 = rand(0,39);
		$ra2 = $ra2+100;
		$numstick = $ra2;
	}
	else if($ra1 = 2)
	{
		$ra2 = rand(1,30);
		$ra2 = $ra2 + 400;
		$numstick = $ra2;
	}
}
else
{
	$ra1 = rand(0,2);
	if($ra1 = 0)
	{
		$ra2 = rand(19,47);
		if($ra2 <= 21)//
		{
			$ra2 = $ra2 - 1;
			$numstick = $ra2;
		}
	}
	else if($ra1 = 1)
	{
		$ra2 = rand(40,79);
		$ra2 = $ra2 +100;
		$numstick = $ra2;
	}
	else if($ra1 = 2)
	{
		$ra2 = rand(1,27);
		$ra2 = $ra2 + 500;
		$numstick = $ra2;
	}
}

echo $randpack."<br>";
echo $ra1."<br>";
echo $ra2."<br>";
echo $numstick."<br>";