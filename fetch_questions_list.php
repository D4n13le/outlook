<?php
	if(isset($_GET['q']) && strlen($_GET['q']) > 0)
        $answers_string = $_GET['q'];
    else
        $answers_string = "-1"; //invalid value

    require_once("settings.php");
	$db = new mysqli($dbLocation, $dbUser, $dbPassword, $dbName);

    $query = 
    "SELECT id_question
	    FROM questions, sections
	    WHERE questions.id_section=sections.id_section
	    AND (sections.dependency IS NULL
              OR sections.dependency IN ({$answers_string})
	    	  OR sections.first_question = questions.id_question)
		AND (questions.dependency IS NULL
              OR questions.dependency IN ({$answers_string}))
		ORDER BY sections.section_order, questions.question_order
    ";

    $result = $db->query($query);
    $questions = array();
    if($result->num_rows != 0)
    	while($row = $result->fetch_object())
    	{
    		$questions[] = $row->id_question;
    	}

    echo json_encode($questions);
?>
