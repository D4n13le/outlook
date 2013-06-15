<?php
	if(!isset($_GET['q']) || strlen($_GET['q']) == 0)
        $answers_list = array(-1); //invalid value
    else
    {
        $answers_list = explode(',', $_GET['q']);
    }

    require_once('lib/common.php');

    $n = count($answers_list);

    $question_marks_string = build_question_marks_string($n);
    $query = 
    "SELECT id_question
	    FROM questions, sections
	    WHERE questions.id_section=sections.id_section
	    AND (sections.dependency IS NULL
              OR sections.dependency IN ({$question_marks_string})
	    	  OR sections.first_question = questions.id_question)
		AND (questions.dependency IS NULL
              OR questions.dependency IN ({$question_marks_string}))
		ORDER BY sections.section_order, questions.question_order";
    $types = str_repeat('i', $n * 2);
    $args = array_merge(array($query, $types), $answers_list, $answers_list);
    $result = call_user_func_array('exec_query_many_results', $args);

    $questions = array();
    foreach($result as $row)
        $questions[] = $row->id_question;

    echo json_encode($questions);
?>
