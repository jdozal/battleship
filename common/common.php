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
			default:
				return -1;
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
	public function deploy() {
	}
}
?>