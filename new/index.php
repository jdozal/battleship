<?php
require_once 'common.php';
class Board {
	public $gridSize;
	public $grid = array ();
	public function __construct($gridSize) {
		$this->gridSize = $gridSize;
		$this->grid = $this->fillArray ( $gridSize );
		echo "you created a new Board!";
	}
	public function fillArray($gridSize) {
		for($i = 0; $i < 10; $i ++) {
			array_push ( $this->grid, array () );
		}
		
		for($i = 0; $i < 10; $i ++) {
			for($j = 0; $j < 10; $j ++) {
				$this->grid [$i] [$j] = 0;
			}
		}
		print_r ( $this->grid );
	}
	public function addShip($ship) {
		array_push ( $this->shipList, $ship );
	}
	
	// check whether the given (x,y) is occupied by a ship
	public function isOccupied($x, $y) {
		if ($this->grid [$x] [$y])
			return true;
		return false;
	}
}
class Validate {
	public $strategies = array (
			'Smart',
			'Random',
			'Sweep' 
	);
	public $reasons = [ ];
	public $strategy;
	public $deployment;
	public $shipInformation = array ();
	public $shipArray = [ ];
	public function __construct($strategy, $deployment) {
		$this->strategy = $strategy;
		$this->deployment = $deployment;
	}
	function getDeployment() {
		$deployArr = explode ( ";", $this->deployment );
		// Creating double array with deployment information
		foreach ( $deployArr as $shipInfo ) {
			$this->shipInformation [] = explode ( ",", $shipInfo );
		}
		print_r ( $this->shipInformation );
	}
	public function checkStrategy() {
		if (empty ( $this->strategy )) {
			$this->reasons [] = 'Strategy not specified';
		} else if (! in_array ( $this->strategy, $this->strategies )) {
			$this->reasons [] = 'Unknown strategy';
		}
	}
	public function setShips() {
		// Check if you have 5 ship deployments
		if (count ( $this->shipInformation ) < 5) {
			$this->reasons [] = 'Incomplete ship deployments';
			return;
		}
		
		// Check if array of deployments is well formed
		for($i = 0; $i < count ( $this->shipInformation [$i] ); $i ++) {
			if (is_numeric( $this->shipInformation [$i] [0] )) {
				$this->reasons [] = 'Ship deployment not well-formed';
			}
			if (! is_numeric ( $this->shipInformation [$i] [1] )) {
				$this->reasons [] = 'Ship deployment not well-formed';
			}
			if (! is_numeric ( $this->shipInformation [$i] [2] )) {
				$this->reasons [] = 'Ship deployment not well-formed';
			}
			if (is_string ( $this->shipInformation [$i] [3] )) { // FIXME: CHECK IF BOOLEAN
				if (($this->shipInformation [$i] [3] != "true") && ($this->shipInformation [$i] [3] != "false")) {
					$this->reasons [] = 'Ship deployment not well-formed';
				}
			}
		}
		
		// Check if ship name is in list
		for($i = 0; $i < count ( $this->shipInformation [$i] ); $i ++) {
			$currShip = new Ship ( $this->shipInformation [$i] [0], $this->shipInformation [$i] [1], $this->shipInformation [$i] [2], $this->shipInformation [$i] [3] );
			$this->shipArray [] = $currShip;
			// Checks if ship name exists by checking size in the ship class
			if ($currShip->size < 0) {
				$this->reasons [] = 'Unknown ship name';
			}
		}
	}
	public function printResponse() {
		if (empty ( $this->reasons )) {
			$identifier = uniqid ();
			$this->response = array (
					'response' => true,
					'pid' => $identifier 
			);
		} else {
			$this->response = array (
					'response' => 'false',
					'reason' => implode ( ", ", $this->reasons ) 
			);
		}
		$responseJSON = json_encode ( $this->response );
		echo $responseJSON;
	}
}

// http://cs3360.cs.utep.edu/jldozalcruz/new?strategy=Random&ships=Aircraft+carrier,6,3,true;Battleship,3,1,true;Frigate,2,4,false;Submarine,6,6,true;Minesweeper,3,9,true

// $strategy = $_GET ['strategy'];
// $deployment = $_GET ['ships'];

$valid = True;
$strategy = "Random";
$deployment = "Aircraft carrier,6,3,true;Battleship,3,1,true;Frigate,2,4,false;Submarine,6,6,true;Minesweeper,3,9,true";
$validate = new Validate ( $strategy, $deployment );
$validate->getDeployment ();
$validate->checkStrategy ();
$validate->setShips ();
$validate->printResponse ();

// Add ship if there isn't any conflict
$board = array ();
for($i = 0; $i < 10; $i ++) {
	$board [] = array (
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0 
	);
}

for($i = 0; $i <= count ( $shipInformation [$i] ); $i ++) {
	$y = $shipInformation [$i] [1] - 1;
	$x = $shipInformation [$i] [2] - 1;
	$shipSize = $ships [$shipInformation [$i] [0]] [0];
	$shipID = $ships [$shipInformation [$i] [0]] [1];
	// check if position is outside of board
	if ($x >= 10 || $x < 0 || $y >= 10 || $y < 0) {
		$reasons [] = "Invalid ship position";
		break;
	}
	// placing ships
	if (strcasecmp ( $shipInformation [$i] [3], "true" ) == 0) {
		// if horizontal position check y to check if ship will be inside of board
		if ($y + $shipSize > 10) {
			$reasons [] = "Invalid ship direction";
			continue;
		}
		for($j = 0; $j < $shipSize; $j ++) {
			// check if there is a ship there already
			if ($board [$x] [$y + $j] > 0) {
				$reasons [] = "Conflicting ship deployments";
				break;
			}
			$board [$x] [$y + $j] = $shipID;
		}
	} else {
		// if vertical position check x if ship will be inside of board
		if ($x + $shipSize > 10) {
			$reasons [] = "Invalid ship direction";
			continue;
		}
		for($j = 0; $j < $shipSize; $j ++) {
			// check if there is a ship there already
			if ($board [$x + $j] [$y] > 0) {
				$reasons [] = "Conflicting ship deployments";
				break;
			}
			$board [$x + $j] [$y] = $shipID;
		}
	}
}

// echo "BOARD\n";
// foreach ( $board as $line ) {
// foreach ( $line as $place ) {
// echo $place;
// }
// echo "\n";
// }

?>