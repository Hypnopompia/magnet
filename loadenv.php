<?php
	$file = file($argv[1]);
	$cmd = "eb setenv ";

	foreach ($file as $line) {
		$line = trim($line);
		if ($line != "") {
			$cmd .= $line . " ";
		}
	}

	echo $cmd . "\n";
	system($cmd);
