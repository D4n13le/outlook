<?php
	/*
			logout.php
		Logs out
	*/

	session_start();
	session_destroy();

	header('location: login.php');
?>