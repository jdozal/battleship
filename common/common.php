<?php
class Ship {
	public $name;
	public $size;
	public $orientation;
	public $isHit;
	public $isSunk;
	public $coordinates = [ ];
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

/*
 * {"response": true,
 * "ack_shot": {
 * "x": 4,
 * "y": 5,
 * "isHit": false, // hit a ship?
 * "isSunk": false, // sink a ship?
 * "isWin": false, // game over?
 * "ship:" []} // coordinates (xi,yi)'s of the sunken ship
 * "shot": { // if isSunk is true
 * "x": 5,
 * "y": 6,
 * "isHit": false,
 * "isSunk": false,
 * "isWin": false,
 * "ship:", []}}
 */
class Strategy {
	public $shot = array (
			'x' => NULL,
			'y' => NULL,
			'isHit' => NULL,
			'isSunk' => NULL,
			'isWin' => NULL,
			'ship' => NULL 
	);
	public $ack_shot = array (
			'x' => NULL,
			'y' => NULL,
			'isHit' => NULL,
			'isSunk' => NULL,
			'isWin' => NULL,
			'ship' => NULL 
	);
	public function humanShoot($boardMachine, $x, $y) {
		// if there's a ship at x,y that hasn't been hit
		if (($boardMachine [$x] [$y] != 0) && ($boardMachine [$x] [$y] = ! 'X')) {
			$ship = $this->findShip ( $boardMachine [$x] [$y], $boardMachine );
			$this->ack_shot = $this->setShotInfo ( $x, $y, $ship );
		}  // There's nothing at x,y
else if ($boardMachine [$x] [$y] == 0) {
			$ship = new Ship ();
			$this->set_ack_ShotInfo ( $x, $y, $ship );
			// mark this x,y as hit
			$boardMachine [$x] [$y] = 'X';
		}
	}
	/*
	 * TODO Add stuff to Ship class to find which ship is on
	 * the board and redo this findShip() method and MAKE IT WORK ;)
	 */
	public function findShip($initial, $board) { // TODO fix method naming
		switch ($initial) :
			case 'A' : // Aircraft Carrier
				$hitShip = findShip ( 'A' );
				$hitShip->isHit = true;
				break;
			case 'B' : // Battleship
				$hitShip = findShip ( 'B' );
				$hitShip->isHit = true;
				break;
			case 'F' : // Frigate
				$hitShip = findShip ( 'F' );
				$hitShip->isHit = true;
				break;
			case 'S' : // Submarine
				$hitShip = findShip ( 'S' );
				$hitShip->isHit = true;
				break;
			case 'M' : // Minesweeper
				$hitShip = findShip ( 'M' );
				$hitShip->isHit = true;
				break;
		endswitch
		;
	}
	public function setShotInfo($row, $col, $ship) {
		$this->shot ['x'] = $row;
		$this->shot ['y'] = $col;
		$this->shot ['isHit'] = $ship->isHit;
		$this->shot ['isSunk'] = $ship->isSunk;
		$this->shot ['isWin'] = false; // TODO check with game class whether or not the shot was a win
		if ($ship->isSunk) {
			$this->shot ['ship'] = $ship->coordinates;
		}
		// json_encode($this->shot);
	}
	public function set_ack_ShotInfo($row, $col, $ship) {
		$this->ack_shot ['x'] = $row;
		$this->ack_shot ['y'] = $col;
		$this->ack_shot ['isHit'] = $ship->isHit;
		$this->ack_shot ['isSunk'] = $ship->isSunk;
		$this->ack_shot ['isWin'] = false; // TODO check with game class whether or not the shot was a win
		if ($ship->isSunk) {
			$this->ack_shot ['ship'] = $ship->coordinates;
		}
		// json_encode($this->shot);
	}
}
class SweepStrategy extends Strategy {
	// boardPlayer is beting hit (the server is hitting the human's board
	public function shootSweep($boardPlayer) {
		for($row = 0; $row < 10; $row ++) {
			for($col = 0; $col < 10; $col ++) {
				// There's a ship in this row,col spot in the board
				// and this place hasn't been shot before
				// TODO check this condition. I think it's OK
				if (($boardPlayer [$row] [$col] != 0) && ($boardPlayer [$row] [$col] != 'X')) {
					$ship = $this->findShip ( $boardPlayer [$row] [$col], $boardPlayer ); // find which ship was shot
					                                                                      // call the superclass' setShotInfo method
					$this->setShotInfo ( $row, $col, $ship );
					// mark this spot as hit
					$boardPlayer [$row] [$col] = 'X';
				} else { // there's no ship at row,col
					$ship = new Ship ();
					$this->setShotInfo ( $row, $col, $ship );
					// mark row,col as hit
					$boardPlayer [$row] [$col] = 'X';
				}
			}
		}
	}
}
class RandomStrategy extends Strategy {
	// TODO do I need constructors with no parameters for each strategy?
	public function shootRandom($boardPlayer) {
		$row = rand ( 0, count ( $boardPlayer ) - 1 );
		$col = rand ( 0, count ( $boardPlayer ) - 1 );
		
		// while row,col has been hit
		while ( $boardPlayer [$row] [$col] == 'X' ) {
			// find another random row, col
			$row = rand ( 0, count ( $boardPlayer ) - 1 );
			$col = rand ( 0, count ( $boardPlayer ) - 1 );
		}
		// there's no ship at row,col
		if ($boardPlayer [$row] [$col] == 0) {
			$boardPlayer [$row] [$col] = 'X';
			// encode the shot as a miss by calling the superclass' method
			$ship = new Ship (); // TODO test whether this constructor works with no parameters
			$this->setShotInfo ( $row, $col, $ship );
		} else {
			$ship = $this->findShip ( $boardPlayer [$row] [$col], $boardPlayer );
			// encode the shot as a miss by calling the superclass' method
			$this->setShotInfo ( $row, $col, $ship );
		}
	}
}
class SmartStrategy extends Strategy {
	public function shootSmart($boardPlayer) {
		for($row = 0; $row < 10; $row ++) {
			for($col = 0; $col < 10; $col ++) {
				// if there is a ship in this x,y spot on the board
				// and it hasn't been hit before
				if (($boardPlayer [$row] [$col] != 0) && ($boardPlayer [$row] [$col] != 'X')) {
					// find ship that's just been hit
					$ship = $this->findShip ( $boardPlayer [$row] [$col], $boardPlayer );
					// try to shoot spot above it
					// try to shoot to the right of it
					// try to shoot to the left of it
					// try to shoot spot below it
				} else if ($boardPlayer [$row] [$col] == 0) {
					$ship = new Ship ();
					// encode the shot as a miss by calling the superclass' method
					$this->setShotInfo ( $row, $col, $ship );
				}
			}
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