<?php
require_once '../common/common.php';

$pid = $_GET ['pid'];
$strategy = $_GET ['strategy'];
echo $pid;
$fileToRead = fopen ( "../writable/$pid.txt", "r" ); // open the file for reading
$game =  json_decode ( fread ( $fileToRead, filesize ( "../writable/$pid.txt" ) ) );
print_r($game);
// $randomStrategy = new RandomStrategy ();
// $randomStrategy->humanShoot ( $newGame->boardMachine, 2, 1 );

// $randomStrategy_a = new RandomStrategy();
// $randomStrategy_a->shootRandom($newGame->boardPlayer);

//TODO fix breaking of for loops
// $sweepStrategy = new SweepStrategy();
// $sweepStrategy->shootSweep($newGame->boardPlayer);

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
	public $shot;
	public $ack_shot;
	public function __construct() {
		$this->shot = array (
				'x' => 'car',
				'y' => 'BMW',
				'isHit' => 'WW',
				'isSunk' => 'Mini',
				'isWin' => 'Civic',
				'ship' => 'Lala'
		);
		$this->ack_shot = array (
				'x' => 'car',
				'y' => 'BMW',
				'isHit' => 'WW',
				'isSunk' => 'Mini',
				'isWin' => 'Civic',
				'ship' => 'Lala'
		);
	}

	public function printShotInfo(){
		echo "x: " . $this->shot['x'] . "\n";
		echo "y: " . $this->shot['y'] . "\n";
		echo "isHit: " . $this->shot['isHit'] . "\n";
		echo "isSunk: " . $this->shot['isSunk']. "\n";
		echo "isWin: " . $this->shot['isWin'] . "\n";
		echo "isShip: " . $this->shot['ship'] . "\n";
	}

	public function print_set_ack_shotInfo(){
		echo "ack_shot: \n";
		echo 'x' . $this->ack_shot['x'];
		echo "\n";
		echo 'y' . $this->ack_shot['y'];
		echo "\n";
		echo 'isHit' . $this->ack_shot['isHit'];
		echo "\n";
		echo 'isSunk' . $this->ack_shot['isSunk'];
		echo "\n";
		echo 'isWin' . $this->ack_shot['isWin'];
		echo "\n";
		echo 'isShip' . $this->ack_shot['ship'];
		echo "\n";
	}
	public function humanShoot($boardMachine, $x, $y) {

		echo "entered humanShoot method\n";
		if (is_numeric ( $boardMachine->grid [$x] [$y] )) {
			echo "entered if-statement\n";
			echo $boardMachine->grid [$x][$y];
			echo 'is numeric and there is no ship here\n';
			$ship = null;
			$this->setShotInfo($x, $y, $ship, $boardMachine);
			echo "Printing shot info\n";
			$this->printShotInfo();
			$boardMachine->grid [$x][$y] = 'X';
			echo $boardMachine->grid [$x][$y] = 'X';
			// echo "there's a ship at $x,$y and it's $boardMachine->grid[$x] [$y]\n";
		}
		else if (! is_numeric ( $boardMachine->grid [$x][$y] ) && $boardMachine->grid [$x][$y] != 'X') {
			echo "entered else-if statement\n";
			echo $boardMachine->grid [$x] [$y];
			echo "is not numeric and there is a ship here\n";
			$ship = $this->findShip ( $boardMachine->grid [$x] [$y], $boardMachine );
			$ship->printShipInfo();
			echo "parameters for ack_shot";
			echo $x . "\n";
			echo $y . "\n";
			$this->print_set_ack_shotInfo();
			$this->set_ack_ShotInfo($x, $y, $ship, $boardMachine);
			echo "After set_ack_shotInfo\n";
			$this->print_set_ack_shotInfo();
		}
	}

	public function findShip($initial, $board) {
		/*
		 * find the index (key) in the board's shipPosition array
		 * from the initial of the ship that was hit (value)
		 */
		$shipIndex = array_search ( $initial, $board->shipPosition );
		/*
		 * Access the board's shipList, find the ship at $shipIndex
		 * and mark its boolean values as appropriate
		 */
		$hitShip = $board->shipList [$shipIndex];
		$hitShip->isHit = true;
		$hitShip->numHits++;
		// if it's hit in as many spots as its size, the ship is sunk
		if ($hitShip->numHits == $hitShip->size) {
			$hitShip->isSunk = true;
		}
		return $hitShip;
	}
	public function setShotInfo($row, $col, $ship, $board) {
		$this->shot ['x'] = $row;
		$this->shot ['y'] = $col;
		$this->shot ['isHit'] = $ship->isHit;
		$this->shot ['isSunk'] = $ship->isSunk;
		$this->shot ['isWin'] = $this->checkIfWin ( $board ); // TODO check with game class whether or not the shot was a win
		$this->shot ['ship'] = $ship->coordinates;

		// json_encode($this->shot);
	}

	// Checks if all the ships in one board are sunk
	public function checkIfWin($board) {
		foreach ( $board->shipList as $ship ) {
			if (! ($ship->isSunk)) {
				return false; // if at least one ship is not sunk,the game's still on
			}
		}
		return true; // otherwise, all ships are sunk, so game is over
	}
	public function set_ack_ShotInfo($row, $col, $ship, $board) {
		// 		echo "in set_ack_ShotInfo\n";
		// 		echo $row;
		// 		echo $col;

		// 		echo $ship->printShipInfo();
		$this->ack_shot ['x'] = $row;
		$this->ack_shot ['y'] = $col;
		$this->ack_shot ['isHit'] = $ship->isHit;
		$this->ack_shot ['isSunk'] = $ship->isSunk;
		$this->ack_shot ['isWin'] = $this->checkIfWin ( $board );
		$this->ack_shot ['ship'] = $ship->coordinates;

		// json_encode($this->shot);
	}
}
class SweepStrategy extends Strategy {

	public function __construct(){

	}
	// boardPlayer is beting hit (the server is hitting the human's board)
	public function shootSweep($boardPlayer) {
		echo "entered shootSweep\n";
		//human shot before every server shot as long as the game isn't over
		for($row = 0; $row < 10; $row ++) {
			for($col = 0; $col < 10; $col ++) {
				//nothing to hit
				if (is_numeric($boardPlayer->grid[$row][$col])) {
					echo "inside if-statement, is_numeric";
					echo $boardPlayer->grid [$row][$col] . "\n";
					$ship = null;
					$this->set_ack_shotInfo($row,$col,$ship,$boardPlayer);
					echo "shot_info:\n";
					$this->print_set_ack_shotInfo();
					$boardPlayer->grid[$row][$col] = 'X';
					echo $boardPlayer->grid[$row][$col];
					echo "about to break\n";
					return;
					//there is a ship to hit
				} else if (!is_numeric ( $boardPlayer->grid [$row][$col] ) && $boardPlayer->grid [$row][$col] != 'X'){
					echo "inside else if- is not numeric\n";
					echo $boardPlayer->grid [$row][$col] . "\n";
					$ship = $this->findShip ( $boardPlayer->grid[$row][$col], $boardPlayer ); // find which ship was shot
					$ship->printShipInfo();
					$this->setShotInfo($row,$col,$ship, $boardPlayer);
					echo "printing shot info\n";
					$this->printShotInfo();
				}
			}
		}
	}
}

class RandomStrategy extends Strategy {
	public function __construct(){

	}

	// TODO do I need constructors with no parameters for each strategy?
	public function shootRandom($boardPlayer) {
		echo "now in shootRandom\n";
		$row = rand ( 0, count ( $boardPlayer->grid ) - 1 );
		$col = rand ( 0, count ( $boardPlayer->grid ) - 1 );

		// while row,col has been hit
		while ( $boardPlayer->grid[$row] [$col] == 'X' ) {
			// find another random row, col
			$row = rand ( 0, count ( $boardPlayer->grid ) - 1 );
			$col = rand ( 0, count ( $boardPlayer->grid ) - 1 );
		}
		echo $row . "\n";
		echo $col . "\n";
		// there's no ship at row,col
		if ($boardPlayer->grid[$row][$col] == "0") {
			echo "entered if- there is no ship at row,col";
			$boardPlayer->grid[$row] [$col] = 'X';
			echo $boardPlayer->grid[$row] [$col];
			// encode the shot as a miss by calling the superclass' method
			$ship = NULL;
			$this->setShotInfo ( $row, $col, $ship, $boardPlayer );
			$this->printShotInfo();
		} else {
			$ship = $this->findShip ( $boardPlayer->grid[$row] [$col], $boardPlayer );
			//encode an actual shot
			$ship->printShipInfo();
			$this->setShotInfo ( $row, $col, $ship, $boardPlayer );
			$this->printShotInfo();
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
					$ship = NULL;
					// encode the shot as a miss by calling the superclass' method
					$this->setShotInfo ( $row, $col, $ship, $boardPlayer );
				}
			}
		}
	}
}

?>