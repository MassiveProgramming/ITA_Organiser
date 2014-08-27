function clear_inputs() {
	var fields = new Array( "username", "password" );
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
			$.post( "/ajax/login", 
					{
						username: $( "#username" ).val(),
						password: $( "#password" ).val()
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
	
} );