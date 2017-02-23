<?php
class Ship {
	public $name;
	public $size;
	public $orientation;
	public $isHit;
	public $isSunk;
	public $coordinates = array ();
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
		array_push ( $this->coordinates, $x );
		array_push ( $this->coordinates, $y );
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
	public $grid = [];
	public $shipList = array ();
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

	public function printGrid(){
		echo "BOARD\n";
		foreach ( $this->grid as $line ) {
			foreach ( $line as $place ) {
				echo $place;
			}
			echo "\n";
		}
	}
	public function addShip($ship) {
		array_push ( $this->shipList, $ship );
		
		
	}

	
	// check whether the given (x,y) is occupied by a ship
	public function isOccupied($x, $y) {
		if ($this->grid [$x] [$y])
			return true;
		else {
			return false;
		}
	}
}
?>