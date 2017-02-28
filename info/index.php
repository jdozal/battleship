<?php

/*Source code by:
 * Jessica Dozal - jldozalcruz@miners.utep.edu
 * Ana Garcia - ajgarciaramirez@miners.utep.edu
 *
 */
class Game {
	var $size;
	var $strategies;
	var $ships;
	
	public function __construct($size, $strategies, $ships){
		$this->size = $size;
		$this->strategies = $strategies;
		$this->ships = $ships;
	}
}

class Ship {
	var $name;
	var $size;
	
	public function __construct($name, $size) {
		$this->name = $name;
		$this->size = $size;
	}
}

$ships = array(new Ship('Aircraft carrier', 5), new Ship('Battleship', 4),
				new Ship('Frigate', 3), new Ship('Submarine', 3),
				new Ship('Minesweeper', 2));
$strategies = array('Smart', 'Random', 'Sweep');
$info = new Game(10, $strategies, $ships);
echo json_encode($info);

?>