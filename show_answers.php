<?php
	require_once("settings.php");
	$db = new mysqli($dbLocation, $dbUser, $dbPassword, $dbName);

	if(!isset($_GET['u']))
	{
	    $query = "SELECT DISTINCT users.id_user, name, surname
	    		  FROM given_answers, users
	    		  WHERE given_answers.id_user = users.id_user";

	    $result = $db->query($query);
	    
	    if($result && $result->num_rows != 0)
	    {
	    	echo "<ul>";
	    	while($row = $result->fetch_object())
	    	{
	    		echo "<li><a href='show_answers.php?u={$row->id_user}'>{$row->name} {$row->surname}</a></li>";
	    	}
	    	echo "</ul>";
	    }	
	}
	else
	{
		$id_user = $_GET['u'];

	    $query = "SELECT questions.text AS qtext, answers.text AS atext
	    		  FROM given_answers, questions, answers
	    		  WHERE given_answers.id_answer = answers.id_answer
	    		  AND questions.id_question = answers.id_question
	    		  AND given_answers.id_user={$id_user}";

	   	$result = $db->query($query);

	  	if($result->num_rows != 0)
	    {
	    	echo "<ul>";
	    	while($row = $result->fetch_object())
	    	{
	    		echo "<li>{$row->qtext} - {$row->atext}</li>";
	    	}
	    	echo "</ul>";
	    }
	}
?>