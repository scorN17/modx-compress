$redirect= array(
	1 => array(
		'/index.html' => '/',
		'/index.php' => '/',
	)
);


//=================================================================

if( $redirect[ 1 ][ $_SERVER[ 'REQUEST_URI' ] ] )
{
	header( 'HTTP/1.1 301 Moved Permanently' );
	header( 'location: '. $redirect[ 1 ][ $_SERVER[ 'REQUEST_URI' ] ] );
	exit();
}
