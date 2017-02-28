<?php
/*
 * Source code by:
 * Jessica Dozal - jldozalcruz@miners.utep.edu
 * Ana Garcia - ajgarciaramirez@miners.utep.edu
 *
 */
require_once 'common.php';
// $pid = $_GET ['pid'];
$fileToRead = fopen ( "writable/58b5202ea8ed0.txt", "r" ); // open the file for reading
$game = json_decode ( fread ( $fileToRead, filesize ( "writable/58b5202ea8ed0.txt" ) ) );
$strategy = $game->strategy;
// get shot from url
// $shot = $_GET ['shot'];
// $shot = explode ( ",", $shot );
$shot = array (
		2,
		5 
);
$x = $shot [0];
$y = $shot [1];
// save board from player and from machine
$boardPlayer = $game->boardPlayer;
$boardMachine = $game->boardMachine;

echo "MACHINE BOARD\n";
foreach ( $boardMachine->grid as $line ) {
	foreach ( $line as $place ) {
		echo $place;
	}
	echo "\n";
}
echo "PLAYER BOARD\n";
foreach ( $boardPlayer->grid as $line ) {
	foreach ( $line as $place ) {
		echo $place;
	}
	echo "\n";
}

$response = new Strategy ();
$response->humanShoot ( $x, $y, $game->boardMachine );

// if (strcasecmp ( $strategy, "Sweep" ) == 0) {
// echo "enter sweep if";
// $response->SweepStrategy($boardPlayer);

// }
echo "\n\nMACHINE BOARD\n";
foreach ( $boardMachine->grid as $line ) {
	foreach ( $line as $place ) {
		echo $place;
	}
	echo "\n";
}
echo "PLAYER BOARD\n";
foreach ( $boardPlayer->grid as $line ) {
	foreach ( $line as $place ) {
		echo $place;
	}
	echo "\n";
}

print_r ( json_encode ( $response ) );
class Strategy {
	public $response = true;
	public $ack_shot = array (
			'x' => false,
			'y' => false,
			'isHit' => false,
			'isSunk' => false,
			'isWin' => false,
			'ship' => false 
	);
	public $shot = array (
			'x' => false,
			'y' => false,
			'isHit' => false,
			'isSunk' => false,
			'isWin' => false,
			'ship' => false 
	);
	public function __construct() {
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
		$hitShip->numHits ++;
		$hitShip->isHit = true;
		// if it's hit in as many spots as its size, the ship is sunk
		if ($hitShip->numHits == $hitShip->size) {
			$hitShip->isSunk = true;
		} else {
			$hitShip->isSunk = false;
		}
		return $hitShip;
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
	public function setShot($x, $y, $isHit, $isSunk, $isWin, $ship) {
		$currShot = array (
				'x' => $x,
				'y' => $y,
				'isHit' => $isHit,
				'isSunk' => $isSunk,
				'isWin' => $isWin,
				'ship' => $ship 
		);
		return $currShot;
	}
	public function humanShoot($x, $y, $boardMachine) {
		$gridValue = $boardMachine->grid [$y - 1] [$x - 1];
		echo "value in board machine = " . $boardMachine->grid [$y - 1] [$x - 1];
		if (($gridValue == '0') && ($gridValue == 'X')) {
			$boardMachine->grid [$y - 1] [$x - 1] = 'X';
			// response when shot is not significant
			$this->ack_shot = $this->setShot ( $x, $y, false, false, false, [ ] );
		} else {
			$hitShip = $this->findShip ( $gridValue, $boardMachine );
			print_r ( $hitShip );
			$isSunk = $hitShip->isSunk == 'true';
			$isWin = $this->checkIfWin ( $boardMachine ) == 'true';
			echo "$isSunk - $isWin";
			$this->ack_shot = $this->setShot ( $x, $y, true, $isSunk, $isWin, $hitShip->coordinates );
			$boardMachine->grid [$y - 1] [$x - 1] = 'X';
		}
	}
	public function SweepStrategy($boardPlayer) {
	}
}

?>