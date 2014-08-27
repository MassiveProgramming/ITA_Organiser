<?php

abstract class ajax {
	
	public $super;
	
	public function ajax( &$main )
	{
		$this->super = &$main;
	}
	
	/**
	 * Run the AJAX Script
	 */
	public abstract function main();
	
};