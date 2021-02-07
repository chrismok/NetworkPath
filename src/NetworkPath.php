<?php

namespace SubscribeHR\Test;

/**
 * Class NetworkPath
 * @author Chris Mok
 * @package SubscribeHR\Test
 */
class NetworkPath
{
	protected $network;
	public $tmpNetwork;
	public $currentDevice;
	public $previousDevice;

	/**
	 * NetworkPath constructor.
	 *
	 * @param null $csvData
	 */
	public function __construct($csvData = null)
	{
		if($csvData !== null) {
			$this->setNetwork($csvData);
		}
	}

	/**
	 * @param mixed $csvData
	 */
	public function setNetwork($csvData)
	{
		try {
			// Open the file for reading
			if(($h = fopen($csvData, "r")) !== FALSE) {
				$network = [];
				// Convert each line into the local $data variable
				while(($data = fgetcsv($h, 1000, ",")) !== FALSE) {
					// Read the data from a single line
					$deviceFrom = strtoupper(trim($data[0]));
					$deviceTo = strtoupper(trim($data[1]));
					$latency = intval($data[2]);

					$network[$deviceFrom][$deviceTo] = $latency;
					$network[$deviceTo][$deviceFrom] = $latency;
				}

				$this->network = $network;

				// Close the file
				fclose($h);
			}
		} catch (Exception $e) {
			echo "Exception: {$e->getMessage()}";
		}
	}

	public function parseData($data) {
		if(!isset($data) || empty($data)) {
			return false;
		}

		$data = explode(" ", trim($data));
		if (count($data) != 3) {
			return false;
		} else {
			$bValid = $this->validation($data);
			if($bValid) {
				return $data;
			} else {
				return false;
			}
		}
	}

	public function validation($data) {
		//Latency data type check.
		return is_numeric($data[2]);
	}

	public function findPath($data) {
		$searchFrom = strtoupper(trim($data[0]));
		$searchTo = strtoupper(trim($data[1]));
		$searchLatency = $data[2];
		$this->tmpNetwork = $this->network;

		$this->currentDevice = $searchFrom;
		$this->visitedDevices = [];
		$resultPath = [$searchFrom];
		$resultLatency = 0;

		$this->visitedDevices[] = $searchFrom;
		while(!empty($this->tmpNetwork)) {
			$device = isset($this->tmpNetwork[$this->currentDevice]) && !empty($this->tmpNetwork[$this->currentDevice]) ? $this->tmpNetwork[$this->currentDevice] : false;
			if($device !== false) {
				foreach($device as $neighbour => $latency) {
					if(in_array($neighbour, $this->visitedDevices)) {
						unset($this->tmpNetwork[$this->currentDevice][$neighbour]);
						continue;
					}
					$this->visitedDevices[] = $neighbour;

					if($neighbour == $searchTo) { //Find goal.
						$resultPath[] = $neighbour;
						$resultLatency += $latency;

						if($resultLatency > $searchLatency) {
							$resultPath = [$searchFrom];
							$resultLatency = 0;
							$this->currentDevice = $searchFrom;
							array_pop($this->visitedDevices);
						} else {
							unset($this->tmpNetwork);
						}
						break;
					} else {
						$this->currentDevice = $neighbour;
						$resultPath[] = $neighbour;
						$resultLatency += $latency;

						if($resultLatency > $searchLatency) {
							$resultPath = [$searchFrom];
							$resultLatency = 0;
							$this->currentDevice = $searchFrom;
						}
						break;
					}
				}
			} else {
				//unset($this->tmpNetwork);
				$resultPath = [$searchFrom];
				$resultLatency = 0;
				$this->currentDevice = $searchFrom;
			}
		}

		if(!empty($resultPath) && count($resultPath) > 1) {
			return implode(" => ", $resultPath) . " => " . $resultLatency;
		} else {
			return "Path not found";
		}
	}

	public function calculator($device, $data) {
		foreach($device as $neighbour => $latency) {

		}
	}
}