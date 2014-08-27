<?php 

class request_handler {
	
	public	$url;
	public	$site = "";
	
	public  $super;
	
	public function request_handler( &$main )
	{
		$this->super = &$main;
		$this->url = explode( '/', $_GET["url"] );
	}
	
	/**
	 * Validate the requested site and redirect if necessary
	 */
	public function validate()
	{
		switch ( $this->url[0] ) {
		case "":
			header( "Location: /start" );
			die();
			break;
		case "start":
		case "login":
		case "logout":
		case "profil":
		case "chat":
		case "registrieren":
		case "klasse":
		case "hausaufgaben":
		case "klassenarbeiten":
		case "stundenplan":
		case "kalender":
		case "test":
		case "admin":
		case "error":
			$this->site = $this->url[0];
			break;
		case "js":
			$this->js();
			break;
		case "css":
			$this->css();
			break;
		case "ajax":
			$this->ajax();
			break;
		case "img":
			$this->img();
			break;
		default:
			header( "Location: /error" );
			die();
		}
	}
	
	/**
	 * Treat the content as CSS / PNG
	 */
	private function css()
	{
		if ( file_exists( "../".$_GET["url"] ) ) {
			if ( substr( $_GET["url"], -4 ) == ".css" ) {
				header( "Content-type: text/css" );
				print file_get_contents( "../".$_GET["url"] );
				die();
			} else if ( substr( $_GET["url"], -4 ) == ".png" ) {
				header( "Content-type: image/png" );
				print file_get_contents( "../".$_GET["url"] );
				die();
			}
		} else {
			header( "Content-type: text/css" );
			print "/* Datei existiert nicht! */";
			die();
		}
	}
	
	/**
	 * Treat the content as JS / PNG
	 */
	private function js()
	{
		if ( file_exists( "../".$_GET["url"] ) ) {
			if ( substr( $_GET["url"], -3 ) == ".js" ) {
				header( "Content-type: text/javascript" );
				print file_get_contents( "../".$_GET["url"] );
				die();
			} else if ( substr( $_GET["url"], -4 ) == ".png" ) {
				header( "Content-type: image/png" );
				print file_get_contents( "../".$_GET["url"] );
				die();
			}
		} else {
			header( "Content-type: text/javascript" );
			print "/* Datei existiert nicht! */";
			die();
		}
	}
	
	/**
	 * Load an AJAX-Script
	 */
	private function ajax()
	{
		if ( file_exists( $_GET["url"].".php" ) && $_GET["url"] != "ajax/ajax" ) {
			include_once "ajax/ajax.php";
			include_once $_GET["url"].".php";
			$ajax = new ajax_script( $this->super );
			$ajax->main();
			die();
		} else {
			print "AJAX Pfad inkorrekt!";
			die();
		}
	}
	
	/**
	 * Print the requested URL
	 * <hr />
	 * @return string
	 */
	public function get_url()
	{
		return print_r( $this->url, true );
	}
	
	/**
	 * Get part of the requested URL-String
	 * <hr />
	 * @param int $index
	 * @return string
	 */
	public function get( $index )
	{
		if ( isset( $this->url[$index] ) ) {
			return $this->url[$index];
		}
		return "";
	}
	
};