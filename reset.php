<?php
    require_once("settings.php");
	$db = new mysqli($dbLocation, $dbUser, $dbPassword, $dbName);

	$query = "UPDATE users SET completed=0";
	$db->query($query);

	$query = "TRUNCATE given_answers";
	$db->query($query);
?>

<h1>Risposte resettate</h1>