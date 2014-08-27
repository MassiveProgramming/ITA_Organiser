function is_email( email ) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test( email );
}

function clear_inputs() {
	var fields = new Array( "firstname", "lastname", "password", "password_check",
							"email", "username", "klasse" );
	for ( i in fields ) {
		$( "#"+fields[i] ).attr( "class", "" );
		$( "#"+fields[i]+"_warning" ).fadeOut( "medium", function() {
			$( "#"+fields[i]+"_warning").html( "" );
		} );
	}
}

$( document ).ready( function() {
	
	$( "#submit" ).click( function( e, data ) {
		if ( data ) {
			return;
		} else {
			e.preventDefault();
			$.post( "/ajax/registrieren", 
					{
						firstname: $( "#firstname" ).val(),
						lastname: $( "#lastname" ).val(),
						password: $( "#password" ).val(),
						password_check: $( "#password_check" ).val(),
						email: $( "#email" ).val(),
						username: $( "#username" ).val(),
						klasse: $( "#klasse" ).val()
					},
					function ( data, status ) {
						if ( data == "" ) {
							$( "#submit" ).trigger( "click", true );
						} else {
							clear_inputs();
							var d = JSON.parse( data );
							for ( i in d ) {
								$( "#"+i ).attr( "class", "input_error" );
								$( "#"+i+"_warning" ).html( d[i] );
								$( "#"+i+"_warning" ).fadeIn( "medium" );
							}
						}
					});
		}
	} );
	
	
	
	$( "#password" ).keyup( function( e ) {
		var warning = $( "#password_warning" );
		if ( $( this ).val().length < 5 ) {
			warning.text( "Das Passwort ist zu kurz!" );
			warning.fadeIn( "medium" );
		} else {
			warning.fadeOut( "medium" );
		}
	} );
	
	$( "#password_check" ).keyup( function( e ) {
		var warning = $( "#password_check_warning" );
		if ( $( this ).val() != $( "#password" ).val() ) {
			warning.text( "Die Passwörter stimmen nicht überein!" );
			warning.fadeIn( "medium" );
		} else {
			warning.fadeOut( "medium" );
		}
	} );
	
	$( "#email" ).keyup( function( e ) {
		var warning = $( "#email_warning" );
		if ( !is_email( $( this ).val() ) ) {
			warning.text( "Keine gültige Email Adresse!" );
			warning.fadeIn( "medium" );
		} else {
			warning.fadeOut( "medium" );
		}
	} );
	
	$( "#username" ).keypress( function( e ) {
		var warning = $( "#username_warning" );
		if ( e.which == 47 ) {
			e.preventDefault();
			warning.text( "\"/ \" Zeichen sind nicht erlaubt!" );
			warning.fadeIn( "medium" );
		} else if( e.which == 32 || e.which == 22 ) {
			e.preventDefault();
			$( this ).val( $( this ).val() + '_' );
		} else if ( e.which == 45 ) {
			e.preventDefault();
		} else {
			warning.fadeOut( "medium" );
		}
	} );
	
}
);