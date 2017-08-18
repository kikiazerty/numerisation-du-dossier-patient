<?php
@session_start();

// suppression des sessions en cours
 if (isset($_SESSION['serveur']) || isset($_SESSION['login']) || isset($_SESSION['password'])) {

	@session_unset($_SESSION['serveur']);
	@session_unset($_SESSION['login']);
	@session_unset($_SESSION['password']);

// renomage du dossier setup et rafraichir sur page d'authentification
	@rename("../setup", "../set_up");	
	echo "<meta http-equiv=\"refresh\" content=\"0;url=../index.php\" />";   
}

?>
