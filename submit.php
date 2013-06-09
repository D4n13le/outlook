<?php
	session_start();


	if(empty($_SESSION['user']))
		header("location:login.php") || die(); //user not logged in

	if(!isset($_POST))
		header("location:questions.php");

	$id_user = $_SESSION['user'];

	$answers = array();
	foreach($_POST as $id_question => $id_answer)
	{
		if(is_array($id_answer))
			$answers[] = implode(",",$id_answer);
		else
			$answers[] = $id_answer;
	}

	if($answers.length == 0)
		header("location:questions.php");

	$answers_string = implode(",",$answers);

	require_once("settings.php");
	$db = new mysqli($dbLocation, $dbUser, $dbPassword, $dbName);

	//check if user has already completed the survey
	$query = "SELECT completed FROM users WHERE id_user={$id_user}";
	$result = $db->query($query);

	if($result->fetch_object()->completed)
		header("location:questions.php") || die(); //questionario già inviato

	//filtering out invalid answers
	$query = "SELECT answers.id_answer
			  FROM answers, questions
			  WHERE answers.id_question = questions.id_question
			  AND answers.id_answer IN ({$answers_string}) 
			  AND ( questions.dependency IS NULL 
			  		OR questions.dependency IN ({$answers_string}))";
	$result = $db->query($query);

	$answers = array();
	while($answer = $result->fetch_object())
		$answers[] = $answer->id_answer;

	//populate answers_date
	foreach($answers as $answer)
	{
		$query = "INSERT INTO given_answers
			      (id_given_answer, id_user, id_answer)
			      VALUES
			      (DEFAULT, {$id_user}, {$answer})";
		$result = $db->query($query);		
	}

	//user has completed the survey
	$query = "UPDATE users
			  SET completed=1
			  WHERE id_user={$id_user}";
	$result = $db->query($query);
	

	header("location:completed.php") || die();
?>