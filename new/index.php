<?php
// array of ships ['name'] => [$size, $ID]
$ships = array (
		'Aircraft carrier' => [ 
				5,
				1 
		],
		'Battleship' => [ 
				4,
				2 
		],
		'Frigate' => [ 
				3,
				3 
		],
		'Submarine' => [ 
				3,
				4 
		],
		'Minesweeper' => [ 
				2,
				5 
		] 
);

$strategies = array (
		'Smart',
		'Random',
		'Sweep' 
);
class ValidResponse {
	var $response;
	var $pid;
	public function __construct($response, $pid) {
		$this->response = $response;
		$this->pid = $pid;
	}
}
class InvalidResponse {
	var $response;
	var $reason;
	public function __construct($response, $reason) {
		$this->reason = $reason;
		$this->response = $response;
	}
}
// http://cs3360.cs.utep.edu/jldozalcruz/new?strategy=Random&ships=Aircraft+carrier,6,3,true;Battleship,3,1,true;Frigate,2,4,false;Submarine,6,6,true;Minesweeper,3,9,true

$strategy = $_GET ['strategy'];
$deployment = $_GET ['ships'];
$valid = True;
// $strategy = "Random";
// $deployment = "Aircraft carrier,6,3,true;Battleship,3,1,true;Frigate,2,4,false;Submarine,6,6,true;Minesweeper,3,9,true";
$reasons = [ ];
$shipInformation = [ ];
$shipLoc = explode ( ";", $deployment );
// Creating double array with deployment information
foreach ( $shipLoc as $shipInfo ) {
	$shipInformation [] = explode ( ",", $shipInfo );
}
// Strategy is empty or not part of the list
if (empty ( $strategy )) {
	$reasons [] = 'Strategy not specified';
} else if (! in_array ( $strategy, $strategies )) {
	$reasons [] = 'Unknown strategy';
}

// Check if you have 5 ship deployments
if (count ( $shipInformation ) < 5) {
	$reasons [] = 'Incomplete ship deployments';
}
// Check if ship name is in list
for($i = 0; $i < count ( $shipInformation [$i] ); $i ++) {
	if (! key_exists ( $shipInformation [$i] [0], $ships )) {
		$reasons [] = 'Unknown ship name';
	}
}

// Check structure of deployment
for($i = 0; $i < count ( $shipInformation [$i] ); $i ++) {
	if (! is_string ( $shipInformation [$i] [0] )) {
		$reasons [] = 'Ship deployment not well-formed';
	}
	if (! is_numeric ( $shipInformation [$i] [1] )) {
		$reasons [] = 'Ship deployment not well-formed';
	}
	if (! is_numeric ( $shipInformation [$i] [2] )) {
		$reasons [] = 'Ship deployment not well-formed';
	}
	if (! is_string ( $shipInformation [$i] [3] )) { // FIXME: CHECK IF BOOLEAN
		$reasons [] = 'Ship deployment not well-formed';
	}
}
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
// 	foreach ( $line as $place ) {
// 		echo $place;
// 	}
// 	echo "\n";
// }

if (empty ( $reasons )) {
	$identifier = uniqid ();
	$response = new ValidResponse ( true, $identifier );
} else {
	$response = new InvalidResponse ( false, implode ( ", ", $reasons ) );
}
$responseJSON = json_encode($response);
echo $responseJSON;

?>