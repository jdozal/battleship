<?php
require_once '../common/common.php';
// require_once 'Board.php';
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
	public $board;
	public $valid = true;
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
		// print_r ( $this->shipInformation );
	}
	public function checkStrategy() {
		if (empty ( $this->strategy )) {
			$this->reasons [] = 'Strategy not specified';
			$this->valid = false;
		} else if (! in_array ( $this->strategy, $this->strategies )) {
			$this->reasons [] = 'Unknown strategy';
			$this->valid = false;
		}
	}
	public function checkDeployment() {
		// Check if you have 5 ship deployments
		if (count ( $this->shipInformation ) < 5) {
			$this->reasons [] = 'Incomplete ship deployments';
			$this->valid = false;
			return;
		}
		
		// Check if array of deployments is well formed
		for($i = 0; $i < count ( $this->shipInformation [$i] ); $i ++) {
			if (count ( $this->shipInformation [$i] ) != 4) {
				$this->reasons [] = 'Ship deployment not well-formed';
				$this->valid = false;
				continue;
			}
			if (is_numeric ( $this->shipInformation [$i] [0] )) {
				$this->reasons [] = 'Ship deployment not well-formed';
				$this->valid = false;
			}
			if (! is_numeric ( $this->shipInformation [$i] [1] )) {
				$this->reasons [] = 'Ship deployment not well-formed';
				$this->valid = false;
			}
			if (! is_numeric ( $this->shipInformation [$i] [2] )) {
				$this->reasons [] = 'Ship deployment not well-formed';
				$this->valid = false;
			}
			if (is_string ( $this->shipInformation [$i] [3] )) {
				if (($this->shipInformation [$i] [3] != "true") && ($this->shipInformation [$i] [3] != "false")) {
					$this->reasons [] = 'Ship deployment not well-formed';
					$this->reasons [] = 'Invalid ship direction';
					$this->valid = false;
				}
			}
		}
	}
	public function createBoard() {
		// Creating instance of Board with size 10
		$this->board = new Board ( 10 );
		
		// Traverse array that contains ship information
		foreach ( $this->shipInformation as $boat ) {
			$currShip = new Ship ( $boat [0], $boat [1], $boat [2], $boat [3] );
			// Checks if ship name exists by checking size in the ship class
			if ($currShip->size < 0) {
				$this->reasons [] = 'Unknown ship name';
				$this->valid = false;
				continue;
			}
			$x = $currShip->coordinates [0] - 1;
			$y = $currShip->coordinates [1] - 1;
			// Check is position is outside of board
			if ($x >= 10 || $x < 0 || $y >= 10 || $y < 0) {
				$this->reasons [] = "Invalid ship position";
				$this->valid = false;
				continue;
			}
			// If there are no errors procede and add ship to array
			$this->shipArray [] = $currShip;
			
			// Place horizontal ships in board
			if (strcasecmp ( $currShip->orientation, "true" ) == 0) {
				// check if ship is going to be placed outside board
				if ($x + $currShip->size > 10) {
					$this->reasons [] = "Invalid ship position";
					$this->valid = false;
					continue;
				}
				for($i = 0; $i < $currShip->size; $i ++) {
					// check if there is not a ship in that position already
					if (is_numeric ( $this->board->grid [$y] [$x + $i] ) != 1) {
						$reasons [] = "Conflicting ship deployments";
						$this->valid = false;
						continue;
					}
					$this->board->grid [$y] [$x + $i] = $currShip->name [0];
				}
			}
			
			// Place vertical ships in board
			if (strcasecmp ( $currShip->orientation, "false" ) == 0) {
				// check if ship is going to be placed outside board
				if ($y + $currShip->size > 10) {
					$this->reasons [] = "Invalid ship position";
					$this->valid = false;
					continue;
				}
				for($i = 0; $i < $currShip->size; $i ++) {
					// check if there is not a ship in that position already
					if (! is_numeric ( $this->board->grid [$y + $i] [$x] )) {
						$this->reasons [] = "Conflicting ship deployments";
						$this->valid = false;
						continue;
					}
					$this->board->grid [$y + $i] [$x] = $currShip->name [0];
				}
			}
			$this->board->shipList = $this->shipArray;
		}
		
		// print_r($this->shipInformation);
		// print_r($this->shipArray);
		$this->board->printGrid ();
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
		return $identifier;
	}
}
// http://cs3360.cs.utep.edu/jldozalcruz/new?strategy=Smart&ships=Aircraft+carrier,1,6,false;Battleship,7,5,true;Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false
 $strategy = $_GET ['strategy'];
 $deployment = $_GET ['ships'];
$valid = True;
$strategy = "Random";
$deployment = "Aircraft carrier,1,6,false;Battleship,7,5,true;Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false";
$validate = new Validate ( $strategy, $deployment );
$validate->getDeployment ();
$validate->checkStrategy ();
$validate->checkDeployment ();
if ($validate->valid) {
	$validate->createBoard ();
}
$pid = $validate->printResponse ();
if ($validate->valid) {
	$newGame = new Game ( $pid, $validate->board, $validate->board );
	print_r($newGame);
}

?>

