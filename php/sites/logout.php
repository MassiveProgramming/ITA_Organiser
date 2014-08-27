<?php

class site_script extends site {
	
	public function main()
	{
		
		if ( isset( $_COOKIE[ "uid" ] ) ) {
			$this->delete_ses_id();
			setcookie( "uid", "",time() - 3600  );
		}
		$this->super->ses->close();
		header( "Location: /start" );
		die();
	}
	
	private function delete_ses_id()
	{
		$s = $this->super->db[ "users" ]->prepare( "UPDATE password SET session_id = '' WHERE user_id = :uid;" );
		$s->bindParam( ":uid", $_SESSION[ "user_id" ], SQLITE3_INTEGER );
		$r = $s->execute();
	}
};