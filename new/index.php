<?php

/*
 * Source code by:
 * Jessica Dozal - jldozalcruz@miners.utep.edu
 * Ana Garcia - ajgarciaramirez@miners.utep.edu
 *
 */
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
			$currShip = new Ship ( $boat [0], (int)$boat [1], (int)$boat [2], $boat [3] );
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
			// If there are no errors procede and add ship to array and initial to ship position array
			$this->shipArray [] = $currShip;
			$this->board->shipPosition [] = $currShip->name [0];
			//
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
	
	// function to create random board
	public function createRandomBoard($size) {
		$ranBoard = new Board ( $size );
		$ranBoard->shipPosition = array (
				A,
				B,
				F,
				S,
				M 
		);
		$p1 = new Ship ( "Aircraft carrier", 1, 1, true );
		$p2 = new Ship ( "Battleship", 1, 1, true );
		$p3 = new Ship ( "Frigate", 1, 1, false );
		$p4 = new Ship ( "Submarine", 1, 1, false );
		$p5 = new Ship ( "Minesweeper", 1, 1, false );
		$shipList = array (
				$p1,
				$p2,
				$p3,
				$p4,
				$p5 
		);
		$ranBoard->shipList = $shipList;
		$count = 0;
		while ( $count < count ( $shipList ) ) {
			// get random orientation
			$currOrientation = rand ( 0, 1 ) ? 'true' : 'false';
			// get random coordinates
			$x = rand ( 0, $size - 1 );
			$y = rand ( 0, $size - 1 );
			// set orientation
			$shipList [$count]->orientation = $currOrientation;
			$validCoordinates = true;
			
			// Place horizontal ships in board
			if (strcasecmp ( $currOrientation, "true" ) == 0) {
				if ($x + $shipList [$count]->size > 10) {
					$validCoordinates = false;
				}
				$i = 0;
				while ( $validCoordinates && $i < $shipList [$count]->size ) {
					// check if there is not a ship in that position already
					if ($ranBoard->grid [$y] [$x + $i] != "0") {
						// if there is a ship set validCoordinates as false and break
						$validCoordinates = false;
						continue;
					}
					$i ++;
				}
				// if coordinates are valid, place coordinates on board
				if ($validCoordinates) {
					for($j = 0; $j < $shipList [$count]->size; $j ++) {
						// placing coordinates on board;
						$ranBoard->grid [$y] [$x + $j] = $shipList [$count]->name [0];
					}
					$shipList [$count]->addCoordinates ( $x + 1, $y + 1 );
					$count ++;
				}
			}
			// Place vertical ships on board
			if (strcasecmp ( $currOrientation, "false" ) == 0) {
				if ($y + $shipList [$count]->size > 10) {
					$validCoordinates = false;
				}
				$i = 0;
				while ( $validCoordinates && $i < $shipList [$count]->size ) {
					// check if there is not a ship in that position already
					if ($ranBoard->grid [$y + $i] [$x] != "0") {
						// if there is a ship set validCoordinates as false and break
						$validCoordinates = false;
						continue;
					}
					$i ++;
				}
				// if coordinates are valid, place coordinates on board
				if ($validCoordinates) {
					for($j = 0; $j < $shipList [$count]->size; $j ++) {
						// placing coordinates on board;
						$ranBoard->grid [$y + $j] [$x] = $shipList [$count]->name [0];
					}
					$shipList [$count]->addCoordinates ( $x + 1, $y + 1 );
					$count ++;
				}
			}
		}
		return $ranBoard;
	}
}

// http://cs3360.cs.utep.edu/jldozalcruz/new?strategy=Sweep&ships=Aircraft+carrier,1,6,false;Battleship,7,5,true;Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false
$strategy = $_GET ['strategy'];
$deployment = $_GET ['ships'];
$valid = true;
$strategy = "Sweep";
$deployment = "Aircraft carrier,1,6,false;Battleship,7,5,true;Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false";
$validate = new Validate ( $strategy, $deployment );
if (empty ( $deployment )) {
	$validate->board = $validate->createRandomBoard ( 10 );
	$validate->checkStrategy ();
} else {
	$validate->getDeployment ();
	$validate->checkStrategy ();
	$validate->checkDeployment ();
	if ($validate->valid) {
		$validate->createBoard ();
	}
}
$pid = $validate->printResponse ();
if ($validate->valid) {
	$newGame = new Game ( $pid, $validate->board, $validate->createRandomBoard ( 10 ), $strategy );
	$newGame->createFile ( $pid, json_encode ( $newGame ) );
	//fwrite($pid,$game);
	echo "<br/>MACHINE ";
	$newGame->boardMachine->printGrid();
	echo "<br/>PLAYER ";
	$newGame->boardPlayer->printGrid();
}
?>


