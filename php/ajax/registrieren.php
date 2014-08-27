<?php

class ajax_script extends ajax {
	
	public function main()
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
		
		if ( strlen( $_POST["password"] ) < 5 ) {
			$return["password"] = "Das Passwort ist zu kurz!";
			$error = true;
		}
		
		if ( $_POST["password"] != $_POST["password_check"] ) {
			$return["password_check"] = "Passwörter stimmen nicht überein!";
			$error = true;
		}
		
		$s = $this->super->db["users"]->prepare( "SELECT id FROM user WHERE email = :email;" );
		$s->bindParam( ":email", $_POST["email"], SQLITE3_TEXT );
		
		$r = $s->execute();
		
		if ( $r->fetchArray() ) {
			$return["email"] = "Email Adresse wird bereits verwendet!";
			$error = true;
		}
		
		$s = $this->super->db["users"]->prepare( "SELECT id FROM user WHERE username = :username;" );
		$s->bindParam( ":username", $_POST["username"], SQLITE3_TEXT );
		
		$r = $s->execute();
		
		if ( $r->fetchArray() ) {
			$return["username"] = "Nutzername wird bereits verwendet!";
			$error = true;
		}
		
		
		if ( $error ) {
			print json_encode( $return );
		} else {
			print "";
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
	
		if ( $r->fetchArray ) {
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
	
};