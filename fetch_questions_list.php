<?php
	if(!isset($_GET['q']) || strlen($_GET['q']) == 0)
        $answers_list = array(-1); //invalid value
    else
    {
        $answers_list = explode(',', $_GET['q']);
    }

    require_once('lib/common.php');

    $n = count($answers_list);

    $query = 
    'SELECT id_question
	    FROM questions, sections
	    WHERE questions.id_section=sections.id_section
	    AND (sections.dependency IS NULL
              OR sections.dependency IN ('.build_question_marks_string($n).')
	    	  OR sections.first_question = questions.id_question)
		AND (questions.dependency IS NULL
              OR questions.dependency IN ('.build_question_marks_string($n).'))
		ORDER BY sections.section_order, questions.question_order';

    $types = str_repeat('i', $n * 2);
    $args = array_merge(array($query, $types),
                                      $answers_list, $answers_list);
    $result = call_user_func_array('exec_query_multiple_results', $args);

    $questions = array();
    foreach($result as $row)
        $questions[] = $row->id_question;

    echo json_encode($questions, JSON_NUMERIC_CHECK);
?>
