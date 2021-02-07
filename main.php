<?php
use \SubscribeHR\Test\NetworkPath;

require_once 'vendor/autoload.php';

if(count($argv) != 2) {
	echo "Usage: php main.php [CSV Data Filename]".PHP_EOL;
	exit;
}

if(!file_exists($argv[1])) {
	echo "Error: Data File is not found.".PHP_EOL;
	exit;
} else {
	try {
		$np = new NetworkPath($argv[1]);
		if($np) {
			while(true) {
				echo PHP_EOL."Please input device from, device to and time. (e.g A F 1000 followed by ENTER key)".PHP_EOL;
				$h = fopen("php://stdin","r");
				$line = fgets($h);
				if(strtoupper(trim($line)) == 'QUIT') {
					echo "Bye :)".PHP_EOL;
					exit;
				} else {
					if(($data = $np->parseData($line))) {
						echo $np->findPath($data).PHP_EOL;
					} else {
						echo "Error: Wrong data!".PHP_EOL.PHP_EOL;
					}
				}
			}
		}
	} catch (Exception $e) {
		echo "Exception: {$e->getMessage()}".PHP_EOL;
	}
	exit;
}
