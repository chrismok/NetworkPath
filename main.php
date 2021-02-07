<?php
use \SubscribeHR\Test\NetworkPath;

require_once 'vendor/autoload.php';

if(count($argv) != 2) {
	echo "Usage: php main.php [CSV Data Filename]\n";
	exit;
}

if(!file_exists($argv[1])) {
	echo "Error: Data File is not found.\n";
	exit;
} else {
	try {
		$np = new NetworkPath($argv[1]);
		if($np) {
			while(true) {
				echo "\nPlease input device from, device to and time. (e.g A F 1000 followed by ENTER key)\n";
				$h = fopen("php://stdin","r");
				$line = fgets($h);
				if(strtoupper(trim($line)) == 'QUIT') {
					echo "Bye :)\n";
					exit;
				} else {
					if(($data = $np->parseData($line))) {
						echo $np->findPath($data);
					} else {
						echo "Error: Wrong data!\n\n";
					}
				}
			}
		}
	} catch (Exception $e) {
		echo "Exception: {$e->getMessage()} \n";
	}
	exit;
}
