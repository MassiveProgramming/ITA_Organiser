<?php

abstract class site {
	
	public $super;
	
	public function site( &$main )
	{
		$this->super = &$main;
	}
	
	public abstract function main();
};