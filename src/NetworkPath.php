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

				foreach($network as &$device) {
					asort($device, SORT_NUMERIC);
				}

				$this->network = $network;

				// Close the file
				fclose($h);
			}
		} catch (Exception $e) {
			echo "Exception: {$e->getMessage()}";
		}
	}

	/**
	 * @param $data
	 *
	 * @return array|bool
	 * @author Chris Mok
	 */
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

	/**
	 * @param $data
	 *
	 * @return bool
	 * @author Chris Mok
	 */
	public function validation($data) {
		//Latency data type check.
		return is_numeric($data[2]);
	}

	/**
	 * @param $data
	 *
	 * @return string
	 * @author Chris Mok
	 */
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
				$cnt = 0;
				foreach($device as $neighbour => $latency) {
					if(in_array($neighbour, $this->visitedDevices)) {
						$cnt++;
						if($cnt == count($device)) {
							if($neighbour != $searchTo) {
								if($this->isPossibleRouteAllDone($data)) {
									unset($this->tmpNetwork);
									break;
								} else {
									$resultPath = [$searchFrom];
									$resultLatency = 0;
									$this->currentDevice = $searchFrom;
									$this->visitedDevices = array_slice($this->visitedDevices, 0, count($this->tmpNetwork[$this->currentDevice]));
									break;
								}
							}
						} else {
							continue;
						}
					}

					$this->visitedDevices[] = $neighbour;

					if($neighbour == $searchTo) { //Find goal.
						$resultPath[] = $neighbour;
						$resultLatency += $latency;

						if($resultLatency > $searchLatency) {
							if($this->isPossibleRouteAllDone($data)) {
								unset($this->tmpNetwork);
								break;
							}
							$resultPath = [$searchFrom];
							$resultLatency = 0;
							$this->currentDevice = $searchFrom;
							$this->visitedDevices = array_slice($this->visitedDevices, 0, count($this->tmpNetwork[$this->currentDevice]));
						} else {
							unset($this->tmpNetwork);
						}
					} else {
						$this->currentDevice = $neighbour;
						$resultPath[] = $neighbour;
						$resultLatency += $latency;

						if($resultLatency > $searchLatency) {
							if($this->isPossibleRouteAllDone($data)) {
								unset($this->tmpNetwork);
								break;
							}

							$resultPath = [$searchFrom];
							$resultLatency = 0;
							$this->currentDevice = $searchFrom;
							$this->visitedDevices = array_slice($this->visitedDevices, 0, count($this->tmpNetwork[$this->currentDevice]));
						}
					}
					break;
				}
			} else {
				//unset($this->tmpNetwork);
				$resultPath = [$searchFrom];
				$resultLatency = 0;
				$this->currentDevice = $searchFrom;
				$this->visitedDevices = array_slice($this->visitedDevices, 0, count($this->tmpNetwork[$this->currentDevice]));
			}
		}

		if(!empty($resultPath) && count($resultPath) > 1) {
			if($resultLatency > $searchLatency) {
				return "Path not found";
			} else {
				return implode(" => ", $resultPath) . " => " . $resultLatency;
			}
		} else {
			return "Path not found";
		}
	}

	/**
	 * @param $data
	 *
	 * @return bool
	 * @author Chris Mok
	 */
	public function isPossibleRouteAllDone($data) {
		$searchFrom = strtoupper(trim($data[0]));
		foreach($this->tmpNetwork[$searchFrom] as $deviceName => $latency) {
			if(!in_array($deviceName, $this->visitedDevices)) {
				return false;
			}
		}
		return true;
	}
}