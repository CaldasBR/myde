<?php
	$GLOBALS['con']=new mysqli("159.203.93.209","usersql","SQLqsenha123","makebit");
	if(mysqli_connect_errno()){
		echo "Falha ao conectar ao MySql: ". mysqli_connect_error();
	}
?>
