<?php

class site_script extends site {
	
	public function main()
	{
		$this->super->xml->set_title( "Login" );
		$this->super->xml->add_js( "/js/sites/login.js" );
		
		if ( $_SESSION["logged_in"] ) {
			header( "Location: /start" );
			die();
		}
		
		if ( isset( $_POST["username"] ) ) {
			if ( !$this->validate_input() ) {
				return;
			}
			$data = $this->get_id_salt();
			if ( !$data ) {
				$this->super->xml->select_id( "password" )->set_attr( "class", "input_error" );
				$this->super->xml->select_id( "password_warning" )->set_text( "Passwort oder Nutzername falsch!" );
				$this->super->xml->select_id( "password_warning" )->set_attr( "style", "" );
				return;
			}
			if ( $this->check_password( $data["id"], $data["salt"] ) ) {
				
				$this->session_log_in( $data["id"] );
				header( "Location: /start" );
				die();
				
			} else {
				$this->super->xml->select_id( "password" )->set_attr( "class", "input_error" );
				$this->super->xml->select_id( "password_warning" )->set_text( "Passwort oder Nutzername falsch!" );
				$this->super->xml->select_id( "password_warning" )->set_attr( "style", "" );;
				return;
			}
			
		}
	}
	
	/**
	 * Check for empty fields
	 * <hr />
	 * @return boolean
	 */
	private function validate_input()
	{
		$error = false;
		$return = array();
		foreach ( $_POST as $key => $value ) {
			if ( preg_replace( '/\s{2,}/', " ", $value) == " " || $value == "" ) {
				$return[$key] = "Feld darf nicht leer sein!";
				$error = true;
			}
		}
		
		foreach ( $_POST as $key => $value ) {
			$this->super->xml->select_id( $key )->set_attr( "value", $value );
		}
		
		foreach ( $return as $key => $value ) {
			$this->super->xml->select_id( $key )->set_attr( "class", "input_error" );
			$this->super->xml->select_id( $key."_warning" )->set_text( $value );
			$this->super->xml->select_id( $key."_warning" )->set_attr( "style", "" );
		}
		
		return !$error;
	}
	
	/**
	 * Get the id and the salt
	 */
	private function get_id_salt()
	{
		$s = &$this->super->db["users"]->prepare( "SELECT id, salt FROM user WHERE username = :username OR email = :email;" );
		$s->bindParam( ":username", $_POST["username"], SQLITE3_TEXT );
		$s->bindParam( ":email", $_POST["username"], SQLITE3_TEXT );		
		$r = $s->execute();
		
		return $r->fetchArray();
	}
	
	/**
	 * Check if the password belongs to the users id
	 * <hr />
	 * @param int $id
	 * @param string $salt
	 * @return boolean
	 */
	private function check_password( $id, $salt )
	{
		$password = sha1( $salt.$_POST["password"].$salt );
		$db = &$this->super->db["users"];
		$s = &$this->super->db["users"]->prepare( "SELECT id FROM password WHERE password = :password;" );
		$s->bindParam( ":password", $password, SQLITE3_TEXT );
		
		$r = $s->execute();
		
		$result = $r->fetchArray();
		
		if ( $result != false ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Set up the session variables
	 * <hr />
	 * @param int $id
	 */
	private function session_log_in( $id )
	{
		$s = $this->super->db["users"]->prepare( "SELECT * FROM user WHERE id = :id;" );
		$s->bindParam( ":id", $id, SQLITE3_INTEGER );
		
		$r = $s->execute();
		
		$user = $r->fetchArray();
		
		$_SESSION["logged_in"] = true;
		$_SESSION["user_id"] = $user["id"];

		$_SESSION["firstname"] = $user["vorname"];
		$_SESSION["lastname"] = $user["nachname"];
		$_SESSION["email"] = $user["email"];
		$_SESSION["username"] = $user["username"];
		$_SESSION["klasse"] = $user["klasse"];
		$_SESSION["profilbild"] = $user["profilbild"];
		$_SESSION["hintergrundbild"] = $user["hintergrundbild"];
		$_SESSION["admin"] = $user["admin"];
		
		if ( isset( $_POST[ "remember" ] ) && $_POST[ "remember" ] == true ) {
		
			$ses_id = uniqid( null, true );
			
			setcookie( "uid", $ses_id, time() + ( 60 * 60 * 24 * 365 * 5 ) );
			
			$s = $this->super->db[ "users" ]->prepare( "UPDATE password SET session_id = :sid WHERE id = :id;" );
			$s->bindParam( ":sid", $ses_id, SQLITE3_TEXT );
			$s->bindParam( ":id", $id, SQLITE3_INTEGER );
			
			$r = $s->execute();
		} else {
			print_r( $_POST );
		}
	}
	
};