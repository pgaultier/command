<?php

namespace sweelix\command;

class Help implements CommandInterface {

	public function run($plop=null, $plip=null) {
		echo 'before';
		var_dump($plop, $plip);
		echo 'after';
	}
	public function cool() {
		echo 'cool'."n";
	}
}