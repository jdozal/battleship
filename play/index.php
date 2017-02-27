<?php
echo "I love Jessica <3";
echo "\n";
echo "BlahBlahBlah";

require_once 'common.php';

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
		if (($boardMachine [$x] [$y] != 0) && ($boardMachine [$x] [$y] =! 'X')) {
			$ship = $this->findShip ( $boardMachine [$x] [$y], $boardMachine );
			$this->ack_shot = $this->setShotInfo ( $x, $y, $ship,$boardMachine );
		}  // There's nothing at x,y
		else if ($boardMachine [$x] [$y] == 0) {
			$ship = NULL;//no ship was hit. Used to store info in ack_shot
			$this->set_ack_ShotInfo ( $x, $y, $ship );
			// mark this x,y as hit
			$boardMachine [$x] [$y] = 'X';
		}
	}
	/*
	 * TODO Add stuff to Ship class to find which ship is on
	 * the board and redo this findShip() method and MAKE IT WORK ;)
	 */
	public function findShip($initial, $board) {
		/*
		 * find the index (key) in the board's shipPosition array
		 * from the initial of the ship that was hit (value)
		 */
		$shipIndex = array_search ( $board->shipPosition, $initial );
		/*
		 * Access the board's shipList, find the ship at $shipIndex
		 * and mark its boolean values as appropriate
		 */
		$hitShip = $board->shipList[$shipIndex];
		$hitShip->isHit = true;
		$hitShip->numHits ++;
		// if it's hit in as many spots as its size, the ship is sunk
		if ($hitShip->numHits == $hitShip->size) {
			$hitShip->isSunk = true;
		}
		return $hitShip;
	}
	public function setShotInfo($row, $col, $ship,$board) {
		$this->shot ['x'] = $row;
		$this->shot ['y'] = $col;
		$this->shot ['isHit'] = $ship->isHit;
		$this->shot ['isSunk'] = $ship->isSunk;
		$this->shot ['isWin'] = $this->checkIfWin($board); // TODO check with game class whether or not the shot was a win
		$this->shot ['ship'] = $ship->coordinates;
	
		// json_encode($this->shot);
	}
	
	//Checks if all the ships in one board are sunk
	public function checkIfWin($board){
		foreach($board->shipList as $ship){
			if(!($ship->isSunk)){
				return false;//if at least one ship is not sunk,the game's still on
			}				
		}
		return true; //otherwise, all ships are sunk, so game is over
	}
	public function set_ack_ShotInfo($row, $col, $ship, $board) {
		$this->ack_shot ['x'] = $row;
		$this->ack_shot ['y'] = $col;
		$this->ack_shot ['isHit'] = $ship->isHit;
		$this->ack_shot ['isSunk'] = $ship->isSunk;
		$this->ack_shot ['isWin'] = $this->checkIfWin($board); // TODO check with game class whether or not the shot was a win
		$this->ack_shot ['ship'] = $ship->coordinates;
		
		// json_encode($this->shot);
	}
}
class SweepStrategy extends Strategy {
	// boardPlayer is beting hit (the server is hitting the human's board)
	public function shootSweep($boardPlayer) {
		for($row = 0; $row < 10; $row ++) {
			for($col = 0; $col < 10; $col ++) {
				// There's a ship in this row,col spot in the board
				// and this place hasn't been shot before
				// TODO check this condition. I think it's OK
				if (($boardPlayer [$row][$col] != 0) && ($boardPlayer [$row][$col] != 'X')) {
					$ship = $this->findShip ( $boardPlayer [$row][$col], $boardPlayer ); // find which ship was shot
					                                                                      // call the superclass' setShotInfo method
					$this->setShotInfo ( $row, $col, $ship,$boardPlayer );
					// mark this spot as hit
					$boardPlayer [$row][$col] = 'X';
				} else { // there's no ship at row,col
					$ship = NULL;
					$this->setShotInfo ( $row, $col, $ship, $boardPlayer);
					// mark row,col as hit
					$boardPlayer [$row][$col] = 'X';
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
			$ship = NULL; // TODO test whether this constructor works with no parameters
			$this->setShotInfo ( $row, $col, $ship, $boardPlayer );
		} else {
			$ship = $this->findShip ( $boardPlayer[$row][$col], $boardPlayer );
			// encode the shot as a miss by calling the superclass' method
			$this->setShotInfo ( $row, $col, $ship, $boardPlayer );
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
					$this->setShotInfo ( $row, $col, $ship,$boardPlayer );
				}
			}
		}
	}
}

?>
