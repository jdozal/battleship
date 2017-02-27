<?php
class Ship {
	public $name;
	public $size;
	public $orientation;
	public $isHit;
	public $isSunk;
	public $numHits;
	public $coordinates = [ ];
        public $shipPosition = array();
	public function __construct($name, $x, $y, $orientation) {
		$this->name = $name;
		$this->size = $this->setSize ();
		array_push ( $this->coordinates, $x );
		array_push ( $this->coordinates, $y );
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
		$this->coordinates [0] = $x;
		$this->coordinates [1] = $y;
	}
	public function printShipInfo() {
		echo $this->name;
		echo "\n";
		echo $this->size;
		echo "\n";
		echo $this->orientation;
		echo "\n";
		echo $this->isHit;
		echo "\n";
		echo $this->isSunk;
		echo "\n";
		foreach ( $this->coordinates as $spot ) {
			echo $spot;
			echo "\n";
		}
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
		echo "BOARD\n";
		foreach ( $this->grid as $line ) {
			foreach ( $line as $place ) {
				echo $place;
			}
			echo "\n";
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
	public function __construct($id, $board1, $board2) {
		$this->id = $id;
		$this->boardPlayer = $board1;
		$this->boardMachine = $board2;
	}
	
	// get game to ask player to ask board if all ships are sunk
	public function isGameOver() {
	}
}
class File {
	public function createFile($pid, $game) {
		$filename = "../writable/$pid.txt";
		// $filename = "/Users/jdozal/Documents/workspace/battleship/writable/$pid.txt";
		// /Users/jdozal/Documents/workspace/battleship
		$overWrite = 'You have been overwritten!!!!!';
		$writeFile = "Writing to new file!";
		if (file_exists ( $filename )) {
			echo "The file $filename exists";
			$myFile = fopen ( "$pid.txt", "w" );
			fwrite ( $myFile, $overWrite );
			echo "File $filename was overwritten";
		} else {
			echo "The file $filename does not exist";
			echo "creating new file";
			$myFile = fopen ( "$pid.txt", "w" );
			fwrite ( $myFile, $overWrite );
		}
	}
}



// // read entire file and create a new Game object from it
// $fileToRead = fopen ( 'gameState.txt', "r" ); // open the file for reading
// $game2 = new Game ( json_decode ( fread ( $fileToRead, filesize ( 'gameState.txt' ) ) ) );

// $game2JSON = json_encode ( $game2 );
// $newFile = fopen ( 'secondGame.txt', "w" );
// fwrite ( $newFile, $game2JSON );
// echo 'file secondGame.txt was written';

?>