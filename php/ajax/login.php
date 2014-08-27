<?php

class ajax_script extends ajax {
	
	public function main()
	{
		$return = array();
		$error = false;
		
		foreach ( $_POST as $key => $value ) {
			if ( preg_replace( '/\s{2,}/', " ", $value) == " " || $value == "" ) {
				$return[$key] = "Feld darf nicht leer sein!";
				$error = true;
			}
		}
		
		if ( $error ) {
			print json_encode( $return );
		} else {
			$result = $this->get_id_salt();
			if ( !result ) {
				print json_encode( array( "password" => "Passwort oder Nutzername falsch!" ) );
				return;
			}
			if ( !$this->check_password( $result["id"], $result["salt"] ) ) {
				print json_encode( array( "password" => "Passwort oder Nutzername falsch!" ) );
			}
		}
	}
	
	private function get_id_salt()
	{
		$s = &$this->super->db["users"]->prepare( "SELECT id, salt FROM user WHERE username = :username OR email = :email; " );
		$s->bindParam( ":username", $_POST["username"], SQLITE3_TEXT );
		$s->bindParam( ":email", $_POST["username"], SQLITE3_TEXT );
		
		$r = $s->execute();
		$result = $r->fetchArray();
		
		return $result;
	}
	
	private function check_password( $id, $salt )
	{
		$s = &$this->super->db["users"]->prepare( "SELECT password FROM password WHERE id = :id;" );
		
		$s->bindParam( ":id", $id, SQLITE3_INTEGER );
		$r = $s->execute();
		$password = $r->fetchArray()["password"];
		
		if ( sha1($salt.$_POST["password"].$salt) == $password ) {
			return true;
		} else {
			return false;
		}
	}
	
};