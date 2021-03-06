<?php
/*Source code by:
 * Jessica Dozal - jldozalcruz@miners.utep.edu
* Ana Garcia - ajgarciaramirez@miners.utep.edu
*
*/
class Ship {
	public $name;
	public $size;
	public $orientation;
	public $isHit;
	public $isSunk;
	public $numHits;
	public $coordinates = [ ];
	 
	public function __construct($name, $orientation) {
		$this->name = $name;
		$this->size = $this->setSize ();
		$this->orientation = $orientation;
	}

	public function setSize() {
		switch ($this->name) :
		case ('Aircraft carrier') :
			return 5;
		case ("Battleship") :
			return 4;
		case ("Frigate") :
		case ("Submarine") :
			return 3;
		case ("Minesweeper") :
			return 2;
		default :
			return - 1;
			endswitch
			;
	}
	public function addCoordinates($x, $y) {
		array_push($this->coordinates, $x);
		array_push($this->coordinates, $y);
	}

}
class Board {
	public $gridSize;
	public $grid = [ ];
	public $shipList = array ();
	public $shipPosition = array ();
	public function __construct($gridSize) {
		$this->gridSize = $gridSize;
		for($i = 0; $i < $gridSize; $i ++) {
			$this->grid [] = array (
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
	}
	public function printGrid() {
		echo "BOARD<br/>";
		foreach ( $this->grid as $line ) {
			foreach ( $line as $place ) {
				echo $place;
			}
			echo "<br/>";
		}
	}

	// check whether the given (x,y) is occupied by a ship
	public function isOccupied($x, $y) {
		echo $this->grid [$y] [$x];
		if ($this->grid [$y] [$x] == 0) {
			echo false;
			return false;
		} else {
			return true;
		}
	}
}
class Game {
	public $id;
	public $boardPlayer;
	public $boardMachine;
	public $isWin;
	public $strategy;
	public function __construct($id, $board1, $board2, $strategy) {
		$this->id = $id;
		$this->boardPlayer = $board1;
		$this->boardMachine = $board2;
		$this->strategy = $strategy;
	}

	// get game to ask player to ask board if all ships are sunk
	public function isGameOver() {
	}

	public function createFile($pid,$game){
		$fileName = fopen("../writable/$pid.txt","w");
		fwrite($fileName,$game);
	}
}

?>