<?php

require_once "xml_engine.php";
require_once "request_handler.php";
require_once "session.php";

class ita_organiser {
	
	public $xml;
	public $rh;
	public $db;
	public $ses;
	
	public function ita_organiser()
	{
		$this->rh		= new request_handler( $this );
		$this->ses		= new session( $this );
		$this->xml		= new xml_engine( "main.html" );
		$this->db["users"]	= new SQLite3( "sql/users.db" );
	}
	
	/**
	 * Initialise the page
	 */
	public static function main()
	{
		$ita_organiser = new ita_organiser();
		$ita_organiser->start();
	}
	
	/**
	 * Run the page
	 */
	public function start()
	{
		ob_start();
		
		$this->rh->validate();
		
		$this->ses->remember();
		
		$this->xml->load( $this->rh->site.".html" );
		
		$this->execute_site_script();
		
		$this->add_personal_data();
		
		$page_content = $this->xml->print_page();
		
		print $this->admin_links( $page_content );
		
		ob_flush();
	}
	
	/**
	 * Execute the script belonging to the requested page
	 */
	private function execute_site_script()
	{
		if ( file_exists( "sites/".$this->rh->site.".php" ) ) {
			include_once "sites/site.php";
			include_once "sites/".$this->rh->site.".php";
			$site_script = new site_script( $this );
			$site_script->main();
		} else {
			$this->xml->set_title( "Unter entwicklung" );
		}
	}
	
	private function add_personal_data()
	{
		if ( !$_SESSION["logged_in"] ) {
			return;
		}
		$this->xml->assign( "firstname", $_SESSION["firstname"] );
		$this->xml->assign( "lastname", $_SESSION["lastname"] );
	}
	
	private function admin_links( $page )
	{
		$data = $page;
		if ( $_SESSION[ "admin" ] ) {
			$data = str_replace( "{admin_link}", "\n<li><a href=\"/admin\">Admin Bereich</a></li>", $data );
		} else {
			$data = str_replace( "{admin_link}", "", $data );
		}
		return $data;
	}
	
};
