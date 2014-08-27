<?php

class site_script extends site {
	
	public function main()
	{
		$this->super->xml->set_title( "Registrieren" );
		$this->super->xml->add_js( "/js/sites/registrieren.js" );
		
		if ( $_SESSION["logged_in"] ) {
			header( "Location: /start" );
			die();
		}
		
		if ( isset( $_POST["submit"] ) ) {
			if ( $this->validate_input() ) {
				$this->add_user();
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
		
		$_POST["username"] = preg_replace( '/(\s)+/', '_', $_POST["username"] );
		$_POST["username"] = preg_replace( '/[\?&]+/', "", $_POST["username"] );
		
		foreach ( $_POST as $key => $value ) {
			if ( preg_replace( '/\s{2,}/', " ", $value) == " " || $value == "" ) {
				$return[$key] = "Feld darf nicht leer sein!";
				$error = true;
			}
		}
			
		if ( filter_var( $_POST["email"], FILTER_VALIDATE_EMAIL) == false ) {
			$return["email"] = "Keine gültige Email Adresse!";
			$error = true;
		}
		
		if ( preg_match( '/(\/)+/', $_POST["username"]) ) {
			$return["username"] = "\"/ \" Zeichen sind nicht erlaubt!";
			$_POST["username"] = preg_replace( '/(\/)+/', '.', $_POST["username"] );
			$error = true;
		}
			
		if ( strlen( $_POST["password"] ) < 5 ) {
			$return["password"] = "Das Passwort ist zu kurz!";
		}
			
		if ( $_POST["password"] != $_POST["password_check"] ) {
			$return["password_check"] = "Passwörter stimmen nicht überein!";
			$error = true;
		}
		
		foreach ( $_POST as $key => $value ) {
			$this->super->xml->select_id( $key )->set_attr( "value", $value );
		}
		
		foreach ( $return as $key => $value ) {
			$this->super->xml->select_id( $key )->set_attr( "class", "input_error" );
			$this->super->xml->select_id( $key."_warning" )->set_text( $value );
			$this->super->xml->select_id( $key."_warning" )->set_attr( "style", "" );
		}
		
		if ( !$this->check_email_username() ) {
			$error = true;
		}
		
		return !$error;
	}
	
	/**
	 * Add a user to the database
	 */
	private function add_user()
	{
		$salt = uniqid( NULL, true );
		
		$db = &$this->super->db["users"];
		$result = false;
		
		$s = &$this->super->db["users"]->prepare( "INSERT INTO user (vorname, nachname, email, username, klasse, profilbild, hintergrundbild, salt, admin) ".
					   "VALUES( :first, :last, :email, :username, :klasse, 'default.png', 'default.png', :salt, 0);" );
		$s->bindParam( ":first", $_POST["firstname"], SQLITE3_TEXT );
		$s->bindParam( ":last", $_POST["lastname"], SQLITE3_TEXT );
		$s->bindParam( ":email", $_POST["email"], SQLITE3_TEXT );
		$s->bindParam( ":username", $_POST["username"], SQLITE3_TEXT );
		$s->bindParam( ":klasse", $_POST["klasse"], SQLITE3_TEXT );
		$s->bindParam( ":salt", $salt, SQLITE3_TEXT );
		
		$result = $s->execute();
		
		$s = $db->prepare( "SELECT id FROM user WHERE salt = :salt;" );
		$s->bindParam( ":salt", $salt, SQLITE3_TEXT );
		
		$result = $s->execute();
		
		$id = $result->fetchArray();
		
		$password = sha1( $salt.$_POST["password"].$salt );
		
		$s = $db->prepare( "INSERT INTO password (user_id, password) ".
				   "VALUES(:id, :password)" );
		$s->bindParam( ":id", $id, SQLITE3_INTEGER );
		$s->bindParam( ":password", $password, SQLITE3_TEXT );
		
		$result = $s->execute();
		
		if ( $result ) {
			ob_end_clean();
			
			$_SESSION["logged_in"] = true;
			$_SESSION["user_id"] = $id[ 0 ];
			
			$_SESSION["firstname"] = $_POST[ "firstname" ];
			$_SESSION["lastname"] = $_POST["lastname"];
			$_SESSION["email"] = $_POST["email"];
			$_SESSION["username"] = $_POST["username"];
			$_SESSION["klasse"] = $_POST["klasse"];
			$_SESSION["profilbild"] = "default.png";
			$_SESSION["hintergrundbild"] = "default.png";
			$_SESSION["admin"] = 0;
			
			header( "Location: /start" );
			die();
		} else {
			ob_flush();
			die( "Location: /error" );
			die();
		}
	}
	
	/**
	 * Check if the email or the username is already in use
	 * <hr />
	 * @return boolean
	 */
	private function check_email_username()
	{
		$return = true;
		
		$s = $this->super->db["users"]->prepare( "SELECT id FROM user WHERE email = :email;" );
		$s->bindParam( ":email", $_POST["email"], SQLITE3_TEXT );
		
		$r = $s->execute();
		
		if ( $r->fetchArray() ) {
			$this->super->xml->select_id( "email" )->set_attr( "class", "input_error" );
			$this->super->xml->select_id( "email_warning" )->set_text( "Email Adresse wird bereits verwendet!" );
			$this->super->xml->select_id( "email_warning" )->set_attr( "style", "" );
			$return = false;
		}
		
		$s = $this->super->db["users"]->prepare( "SELECT id FROM user WHERE username = :username;" );
		$s->bindParam( ":username", $_POST["username"], SQLITE3_TEXT );
		
		$r = $s->execute();
		
		if ( $r->fetchArray() ) {
			$this->super->xml->select_id( "username" )->set_attr( "class", "input_error" );
			$this->super->xml->select_id( "username_warning" )->set_text( "Nutzername wird bereits verwendet!" );
			$this->super->xml->select_id( "username_warning" )->set_attr( "style", "" );
			$return = false;
		}
		return $return;
	}
	
}