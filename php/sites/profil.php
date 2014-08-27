<?php

class site_script extends site {
	
	public function main()
	{
		$user = $this->check_url();
		$this->add_information( $user );
	}
	
	private function add_information( $user )
	{
		$xml = &$this->super->xml;
		$xml->assign( "profile_firstname", $user[ "vorname" ] );
		$xml->assign( "profile_lastname", $user[ "nachname" ] );
	}
	
	private function check_url()
	{
		$user = array();
		if ( !$_SESSION[ "logged_in" ] ) {
			header( "Location: /error" );
			die();
		}
		if ( $this->super->rh->get( 1 ) == "" ) {
			$user = $this->get_user_by_name( $_SESSION[ "username" ] );
		} else {
			$user = $this->get_user_by_name( $this->super->rh->get( 1 ) );
		}
		return $user;
	}
	
	private function get_user_by_name( $name )
	{
		$s = $this->super->db[ "users" ]->prepare( "SELECT * FROM user WHERE username = :username;" );
		$s->bindParam( ":username", $name, SQLITE3_TEXT );
		$r = $s->execute();
		$r = $r->fetchArray();
		if ( empty( $r ) ) {
			header( "Location: /error" );
			die();
		}
		return $r;
	}
};