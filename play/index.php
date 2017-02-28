<?php
/*
 * Source code by:
 * Jessica Dozal - jldozalcruz@miners.utep.edu
 * Ana Garcia - ajgarciaramirez@miners.utep.edu
 *
 */
require_once '../common/common.php';
$pid = $_GET ['pid'];
$fileToRead = fopen ( "../writable/$pid.txt", "r" ); // open the file for reading
$game = json_decode ( fread ( $fileToRead, filesize ( "../writable/$pid.txt" ) ) );
// get shot from url
$shot = $_GET ['shot'];
$shot = explode ( ",", $shot );
$x = (int)$shot [0];
$y = (int)$shot [1];

// get strategy from game object
$strategy = $game->strategy;
//echo $strategy."<br/>";
// save board from player and from machine
$boardPlayer = $game->boardPlayer;
$boardMachine = $game->boardMachine;

//echo "MACHINE BOARD<br/>";
//foreach ( $boardMachine->grid as $line ) {
//	foreach ( $line as $place ) {
//		echo $place;
//	}
//	echo "<br/>";
//}
//echo "PLAYER BOARD<br/>";
//foreach ( $boardPlayer->grid as $line ) {
//	foreach ( $line as $place ) {
//		echo $place;
//	}
//	echo "<br/>";
//}

$response = new Strategy ();
// let player make shot
$response->humanShoot ( $x, $y, $game->boardMachine );

// if player wins with shot print response
if ($response->ack_shot->isWin) {
	$response->shot = null;
	print_r ( json_encode ( $response ) );
} else {
	if (strcasecmp ( $strategy, "Sweep" ) == 0) {
		$response->SweepStrategy ( $game->boardPlayer );
	}
	if (strcasecmp ( $strategy, "Random" ) == 0) {
		$response->RandomStrategy ( $game->boardPlayer );
	}
	print_r ( json_encode ( $response ) );
}
//echo "<br/><br/>MACHINE BOARD<br/>";
//foreach ( $game ->boardMachine->grid as $line ) {
//	foreach ( $line as $place ) {
//		echo $place;
//	}
//	echo "<br/>";
//}
//echo "PLAYER BOARD<br/>";
//foreach ( $game ->boardPlayer->grid as $line ) {
//	foreach ( $line as $place ) {
//		echo $place;
//	}
//	echo "<br/>";
//}
$game->isWin = ($response->ack_shot->isWin || $response->shot->isWin);
if(!$game->isWin){
$response->createFile($pid, json_encode($game));
}


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
		//echo "value in board machine = " . $boardMachine->grid [$y - 1] [$x - 1];
		if (($gridValue == '0') || ($gridValue == 'X')) {
			$boardMachine->grid [$y - 1] [$x - 1] = 'X';
			// response when shot is not significant
			$this->ack_shot = $this->setShot ( $x, $y, false, false, false, [ ] );
		} else {
			$hitShip = $this->findShip ( $gridValue, $boardMachine );
			//print_r ( $hitShip );
			$isSunk = $hitShip->isSunk == 'true';
			$isWin = $this->checkIfWin ( $boardMachine ) == 'true';
            if(!empty($isSunk)) {
			$this->ack_shot = $this->setShot ( $x, $y, true, $isSunk, $isWin, $hitShip->coordinates );
            }else{
                $this->ack_shot = $this->setShot ( $x, $y, true, $isSunk, $isWin, [] );
            }
			$boardMachine->grid [$y - 1] [$x - 1] = 'X';
		}
	}
	public function SweepStrategy($boardPlayer) {
		//echo "SWEEP<br/>";
		for($x = 0; $x < count ( $boardPlayer->grid ); $x ++) {
			for($y = 0; $y < count ( $boardPlayer->grid ); $y ++) {
				$gridValue = $boardPlayer->grid [$y] [$x];
				if ($gridValue == '0') {
					$this->shot = $this->setShot ( $x + 1, $y + 1, false, false, false, [ ] );
					$boardPlayer->grid [$y] [$x] = 'X';
					return;
				} elseif (strcasecmp($gridValue, "X") == 0) {
					continue;
				} else {
					$hitShip = $this->findShip ( $gridValue, $boardPlayer );
					//print_r ( $hitShip );
					$isSunk = $hitShip->isSunk == 'true';
					$isWin = $this->checkIfWin ( $boardPlayer ) == 'true';
                    if(!empty($isSunk)) {
					$this->shot = $this->setShot ( $x + 1,$y + 1, true, $isSunk, $isWin, $hitShip->coordinates );
                    }else {
                        $this->shot = $this->setShot ( $x + 1,$y + 1, true, $isSunk, $isWin, [] );
                    }
					$boardPlayer->grid [$y] [$x] = 'X';
					return;
				}
			}
			//echo "<br/>";
		}
	}
	public function RandomStrategy($boardPlayer) {
		$x = rand ( 0, count ( $boardPlayer->grid ) - 1 );
		$y = rand ( 0, count ( $boardPlayer->grid ) - 1 );
		$gridValue = $boardPlayer->grid [$y] [$x];
		
        /*if $gridValue has been previously shot
        *Find another $x,$y and another $gridValue
        */
        while(strcasecmp($gridValue, "X") == 0){
            $x = rand ( 0, count ( $boardPlayer->grid ) - 1 );
		    $y = rand ( 0, count ( $boardPlayer->grid ) - 1 );
		    $gridValue = $boardPlayer->grid [$y] [$x];            
        }
		if ($gridValue == '0'){
			$this->shot = $this->setShot ( $x + 1, $y + 1, false, false, false, [ ] );
			$boardPlayer->grid [$y] [$x] = 'X';
		} else {
			$hitShip = $this->findShip ( $gridValue, $boardPlayer );
			//print_r ( $hitShip );
			$isSunk = $hitShip->isSunk == 'true';
			$isWin = $this->checkIfWin ( $boardPlayer ) == 'true';
            if(!empty($isSunk)) {
			     $this->shot = $this->setShot ( $x + 1, $y + 1, true, $isSunk, $isWin, $hitShip->coordinates );
            } else {
                $this->shot = $this->setShot ( $x + 1, $y + 1, true, $isSunk, $isWin, [] );
            }
			$boardPlayer->grid [$y] [$x] = 'X';
		}
	}
    public function createFile($pid,$game){
		$fileName = fopen("../writable/$pid.txt","w");
		fwrite($fileName,$game);
	}
}

?>