<?php
    require_once('lib/common.php');
?>

<!DOCTYPE html>
<html>

<!-- Page designed by Carlo Varriale http://carlos-way.deviantart.com !-->

<head>
  <meta charset="iso-8859-1">

  <title> Outlook - Questionario</title>

  <link rel='shortcut icon' type='image/x-icon' href='images/world_icon.png' />

  <link rel="stylesheet" type="text/css" href="style/questions.css"/>
  <link rel="stylesheet" type="text/css" href="style/reveal.css" >

  <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500' rel='stylesheet' type='text/css'>

  
  <script type="text/javascript" src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script type="text/javascript" src="js/jquery.reveal.js"></script>
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript" src="js/questions.js"></script>
  <script type="text/javascript">
  // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {
        $(function() {
          var options = {
               'width':600,
               'height':400,
               'backgroundColor': { fill:'transparent' },
               'pieSliceText': 'none'
              };

          $('input').closest('div.question').each( function() {
            // Create the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Opzione');
            data.addColumn('number', 'Voti');
            $(this).find('input').each(function() {
              data.addRow([$(this).parent().find('label[for='+$(this).attr('id')+']').text().trim(), parseInt($(this).attr('data-raw-count'))]);
            });


            div = $(this).find('.chart_div');

            var chart = new google.visualization.PieChart(div[0]);
            chart.draw(data, options);
          });
        });
      }

  </script>
</head>

<body>

  <div id="myModal" class="reveal-modal small">
    <p>Non hai risposto a tutte le domande!</p>
    <a class="close-reveal-modal">&#215;</a>
  </div>

  <div id="up_side">

    <div id="dashboard">

      <img src="images/logo2.png" id="logo"/>
      <span id="des_logo"> questionario </span>
    </div>
  </div>

  <div id="guide">

    <div id="guide_content">

      <span id="guide_text">
        Riepilogo risposte date.        
      </span>
    </div>
  </div>

<form id="myform" method="POST" action="submit.php">
  <div id="questions_body_container">


    <div id="user_section">

      <div id="left_usersection_side">

          <p id="user_section_title"> Questionario progetto Outlook</p>
          <span id="user_section_title_sub"> iniziativa dell'ITIS Galilei di Livorno per la raccolta di informazioni sui diplomati della scuola.</span>

      </div>

      <?php

        $data_query = 'SELECT COUNT(*) AS count FROM (SELECT DISTINCT id_user FROM given_answers) AS T';

        $data = exec_query($data_query);
      ?>

      <div id="right_usersection_side">
          <span id="user_name">Questionari completati: <?php echo "{$data->count}" ?></span><br><br>
      </div>
    </div>


        <?php
        $sections_query = 'SELECT id_section, title, subtitle
                           FROM sections
                           ORDER BY section_order';

        $sections = exec_query_many_results($sections_query);
        foreach($sections as $section):
        ?>
            <div class="section">
                <p class="section_title">
                    <?php echo mb_strtoupper($section->title, "iso-8859-1"); ?>
                </p>
                <?php
                    $questions_query = 'SELECT questions.id_question, text, id_question_type, T.count, questions.id_question in (
                                                SELECT DISTINCT answers.id_question
                                                FROM questions, answers, sections
                                                WHERE answers.id_answer = questions.dependency
                                                   OR answers.id_answer = sections.dependency
                                            ) AS has_dependencies
                                        FROM questions,
                                          (SELECT answers.id_question, COUNT(answers.id_question) as count
                                          FROM given_answers, answers
                                          WHERE given_answers.id_answer = answers.id_answer
                                          GROUP BY answers.id_question) AS T
                                        WHERE questions.id_section=?
                                        AND questions.id_question = T.id_question
                                        ORDER BY question_order';
                    $questions = exec_query_many_results($questions_query, 'i', $section->id_section);
                    foreach($questions as $question):
                ?>

                <div id="question<?php echo $question->id_question?>" data-question_id="<?php echo $question->id_question?>" class="question">
                    <?php if($question->text != null): ?>
                    <p class="question_title">
                        <?php echo $question->text ?>
                    </p>
                    <?php endif ?>

                    <div class="answers">
                    <?php
                        $answers_query = "SELECT answers.id_answer, answers.text, T.count
                        FROM answers LEFT JOIN
                          (SELECT id_answer, COUNT(id_answer) as count
                            FROM given_answers
                            GROUP BY id_answer) AS T ON answers.id_answer = T.id_answer
                        WHERE id_question=?
                        ORDER BY T.count DESC, answer_order";
                        $answers = exec_query_many_results($answers_query, 'i', $question->id_question);
                        
                        switch($question->id_question_type): 
                        case 1:
                            foreach($answers as $answer): ?>
                            <input type="radio" id="a<?php echo $answer->id_answer ?>"
                                name="<?php echo $question->id_question ?>"
                                value="<?php echo $answer->id_answer ?>"
                                data-raw-count="<?php echo $answer->count ?>"
                                <?php if($question->has_dependencies):?> data-side-effects="true" <?php endif?>>
                                <label for="a<?php echo $answer->id_answer ?>">
                                  <?php echo $answer->text; ?>
                                </label>
                                 - <?php echo number_format($answer->count/$question->count * 100, 0); ?>%
                            <br>
                            <?php endforeach ?>
                        <?php break;
                        case 2: ?>
                            <select <?php if($question->has_dependencies):?> data-side-effects="true" <?php endif?>
                                    id="a<?php echo $answer->id_answer ?>" name="<?php echo $question->id_question ?>"
                            <option value="" disabled selected/>
                            <?php foreach($answers as $answer): ?>
                                <option value="<?php echo $answer->id_answer?>" data-raw-count="<?php echo $answer->count ?>">
                                    <?php echo $answer->text?> - <?php echo number_format($answer->count/$question->count * 100, 0); ?>%
                            <?php endforeach ?>
                            </select>
                        <?php break;
                        case 3: 
                            foreach($answers as $answer): ?>
                            <input type="checkbox" id="a<?php echo $answer->id_answer ?>"
                                name="<?php echo $question->id_question ?>[]"
                                value="<?php echo $answer->id_answer ?>"
                                data-raw-count="<?php echo $answer->count ?>"
                                <?php if($question->has_dependencies):?> data-side-effects="true" <?php endif?>>
                                <label for="a<?php echo $answer->id_answer ?>">
                                  <?php echo $answer->text; ?>
                                </label>                      
                                 - <?php echo number_format($answer->count/$question->count * 100, 0); ?>%      </input>
                            <br>
                            <?php endforeach ?>
                        <?php endswitch ?>
                    </div>
                    <div class="chart_div"> </div>
                </div>
            <?php endforeach ?>
            </div>
        <?php endforeach ?>

        </div>
        <input type="submit" id="hidden_submit"/>
    </form>

    <div id="footer">

      <div style="text-align:center width:100%;">

        <input type="submit" value="logout" id="logout_button" onclick="location.href='logout.php'"/>

      </div>

      <div id="footer_content">

          <!--p id="links" da scrivere-->

          <p id="links"> visita il sito della scuola - <a style="color:white;" href="http://galileilivorno.it">galileilivorno.it</a>
              
              
            <span id="credits" >  realizzato dalla 5A INA AS 2012/2013 </span></p>


      </div>

  </div>
</body>
</html>