<?php
echo "I love Jessica <3";
echo "\n";
echo "BlahBlahBlah";


class Player{
	public $board;
	public $ships = array();
	public $opponent;


	public function __construct($size){
		$this->board = new Board($size);

	}

	public function addShips($ship1,$ship2,$ship3,$ship4,$ship5){
		array_push($this->ships, $ship1);
		array_push($this->ships, $ship2);
		array_push($this->ships, $ship3);
		array_push($this->ships, $ship4);
		array_push($this->ships, $ship5);
	}

	public function shoot($x,$y){



	}

	public function areShipsSunk(){
		foreach($this->ships as $ship){
			if(!($ship->isSunk))
				return false;
				return true;
		}
	}

	public function setOpponent($player){
		$this->opponent = $player;
	}
}


$game1 = new Game();
$ship1 = new Ship("Aircraft carrier",1,6,true);
for($i = 7;$i<11;$i++){
	$ship1->addCoordinates(1,$i);
}

$ship2 = new Ship("Battleship",7,5,true);
$ship3 = new Ship("Submarine",9,6,false);
$ship4 = new Ship("Frigate",2,1,false);
$ship5 = new Ship("Minesweeper",10,9,false);

$game1->humanPlayer->addShips($ship1, $ship2, $ship3, $ship4, $ship5);
echo "Human player's ships: ";
echo "\n";
foreach($game1->humanPlayer->ships as $ship){
	$ship->printShipInfo();
	echo"\n";
}


$ship1a = new Ship("Aircraft carrier",1,6,false);
for($i = 7;$i<11;$i++){
	$ship1a->addCoordinates($i,1);
}

$ship2a = new Ship("Battleship",7,5,true);
$ship3a = new Ship("Submarine",9,6,false);
$ship4a = new Ship("Frigate",2,1,false);
$ship5a = new Ship("Minesweeper",10,9,false);
$game1->serverPlayer->addShips($ship1a, $ship2a, $ship3a, $ship4a, $ship5a);
echo "Server player's ships: ";
foreach($game1->serverPlayer->ships as $ship){
	$ship->printShipInfo();
	echo "\n";
}
// // echo "\n";
// // $ship1->printShipInfo();
// // echo "\n";
// // $ship2->printShipInfo();

// $humanPlayer = new Player();
// $humanPlayer->addShip($ship1);
// $humanPlayer->addShip($ship2);
// $humanPlayer->addShip($ship3);
// $humanPlayer->addShip($ship4);
// $humanPlayer->addShip($ship5);

// foreach ($humanPlayer->ships as $ship ){
// 	echo $ship->printShipInfo();
// 	echo "\n";
//}


// Aircraft+carrier,1,6,false;Battleship,7,5,
// true;Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false





?>
