<?php

class session {
	
	private $super;
	
	public function session( &$main )
	{
		$this->super = &$main;
		session_start();
		$this->init();
	}
	
	/**
	 * Initialise the session variables
	 */
	public function init()
	{
		if ( isset( $_SESSION["logged_in"] ) ) {
			return;
		}
		$_SESSION["logged_in"]		= false;
		$_SESSION["user_id"]		= false;
		
		$_SESSION["firstname"]		= "";
		$_SESSION["lastname"]		= "";
		$_SESSION["email"]		= "";
		$_SESSION["username"]		= "";
		$_SESSION["klasse"]		= "";
		$_SESSION["profilbild"]		= "";
		$_SESSION["hintergrundbild"]	= "";
		$_SESSION["admin"]		= false;
	}
	
	/**
	 * Check if the remember cookie is set
	 */
	public function remember()
	{
		/* TODO */
		if ( isset( $_COOKIE[ "uid" ] ) && $_COOKIE[ "uid" ] != "" ) {
			$this->get_ses_by_sesid( $_COOKIE[ "uid" ] );
		}
	}
	
	/**
	 * Get the user data from the session id
	 * <hr />
	 * @param int $sid
	 */
	private function get_ses_by_sesid( $sid )
	{
		$arr = array();
		$s = $this->super->db[ "users" ]->prepare( "SELECT user_id FROM password WHERE session_id = :sid;" );
		$s->bindParam( ":sid", $sid, SQLITE3_TEXT );
		$r = $s->execute();
		$r = $r->fetchArray();
		
		if ( empty( $r ) ) {
			return;
		}
		$uid = $r[ "user_id" ];
		
		$s = $this->super->db[ "users" ]->prepare( "SELECT * FROM user WHERE id = :uid;" );
		$s->bindParam( ":uid", $uid, SQLITE3_INTEGER );
		$r = $s->execute();
		
		$r = $r->fetchArray();
		
		$_SESSION["logged_in"]		= true;
		$_SESSION["user_id"]		= $uid;
		
		$_SESSION["firstname"]		= $r[ "vorname" ];
		$_SESSION["lastname"]		= $r[ "nachname" ];
		$_SESSION["email"]		= $r[ "email" ];
		$_SESSION["username"]		= $r[ "username" ];
		$_SESSION["klasse"]		= $r[ "klasse" ];
		$_SESSION["profilbild"]		= $r[ "profilbild" ];
		$_SESSION["hintergrundbild"]	= $r[ "hintergrundbild" ];
		$_SESSION["admin"]		= $r[ "admin" ];
		
	}
	
	/**
	 * Get the session array
	 * @return array
	 */
	public function get_ses()
	{
		return $this->ses;
	}
	
	/**
	 * Close the active session
	 */
	public function close()
	{
		session_destroy();
	}
	
	/**
	 * 
	 * @param mixed $key
	 * @return mixed
	 */
	public function __invoke( $key )
	{
		return $ses[$key];
	}
	
};