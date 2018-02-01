<?php
	// Initialize the session.
	session_start();

	// Finally, destroy the session.
	session_destroy();
	
	//Aqui deve depois remover os cookies também;
	
	//exibe session;
	//echo "<pre>";
		//var_dump($_SESSION);
	//echo "</pre>";
	
	echo "Sessão destruída";
?>