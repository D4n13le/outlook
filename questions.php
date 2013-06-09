<?php
    session_start();

    if(empty($_SESSION['user']))
      header("location:login.php") || die(); //user not logged in

    $id_user = $_SESSION['user'];   

    require_once("settings.php");
    $db = new mysqli($dbLocation, $dbUser, $dbPassword, $dbName);

    //check if user has already completed the survey
    $query = "SELECT completed FROM users WHERE id_user={$id_user}";
    $result = $db->query($query);

    $completed = $result->fetch_object()->completed;
?>

<!DOCTYPE html>
<html>

<!-- Page designed by Carlo Varriale http://carlos-way.deviantart.com !-->

<head>
  <meta charset="iso-8859-1">

  <title> Outlook - Questionario</title>

  <link rel="stylesheet" type="text/css" href="style/questions.css"/>
  <link rel="stylesheet" type="text/css" href="style/reveal.css" >

  <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500' rel='stylesheet' type='text/css'>

   
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/jquery.reveal.js"></script>
  <script type="text/javascript" src="js/questions.js"></script>
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

      <?php if(!$completed): ?>
        <input type="submit" id="submit_button" value="Invia questionario"> </input>
      <?php endif ?>
    </div>
  </div>

  <div id="guide">

    <div id="guide_content">

      <span id="guide_text">
        <?php if(!$completed): ?>
            Ci siamo! Compila il questionario Outlook, e clicca su "Invia questionario" quando hai finito.
        <?php else: ?>
            Hai gi&agrave inviato il questionario Outlook, ma puoi controllare le risposte che hai dato.
        <?php endif ?>
        
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

        $data_query = "SELECT name, surname, sex, graduation_year, specializations.description, grade, laud
                       FROM users, specializations
                       WHERE users.id_user={$id_user}
                       AND users.id_specialization=specializations.id_specialization";
        $data_result = $db->query($data_query);
        $data = $data_result->fetch_object();
      ?>

      <div id="right_usersection_side">
          <span id="user_name"><?php echo "{$data->name} {$data->surname}" ?></span><br><br>
          <span class="user_data_span"> anno diploma: </span> </span><span id="user_grade_date"><?php echo $data->graduation_year ?></span><br>
          <span id="user_address"><?php echo $data->description ?></span><br>
          <span class="user_data_span"> voto maturit&agrave:</span>
          <span id="user_grade"><?php echo "{$data->grade}{$data->laud}" ?></span>
      </div>
    </div>


        <?php
        $sections_query = 'SELECT id_section, title, subtitle
                           FROM sections
                           ORDER BY section_order';

        $sections_result = $db->query($sections_query);

        while($section = $sections_result->fetch_object()):
        ?>
            <div class="section">
                <p class="section_title">
                    <?php echo mb_strtoupper($section->title, "iso-8859-1"); ?>
                </p>
                <?php
                    $questions_query = "SELECT id_question, text, id_question_type, id_question in (
                                                SELECT DISTINCT answers.id_question
                                                FROM questions, answers, sections
                                                WHERE answers.id_answer = questions.dependency
                                                   OR answers.id_answer = sections.dependency
                                            ) AS has_dependencies
                                        FROM questions
                                        WHERE questions.id_section={$section->id_section}
                                        ORDER BY question_order";
                    $questions_result = $db->query($questions_query);
                    while($question = $questions_result->fetch_object()):
                ?>

                <div id="question<?php echo $question->id_question?>" data-question_id="<?php echo $question->id_question?>" class="question">
                    <?php if($question->text != null): ?>
                    <p class="question_title">
                        <?php echo $question->text ?>
                    </p>
                    <?php endif ?>

                    <div class="answers">
                    <?php
                        $answers_query = "SELECT id_answer, text,
                                            id_answer IN (SELECT id_answer FROM given_answers WHERE id_user={$id_user}) AS selected
                                          FROM answers
                                          WHERE id_question={$question->id_question}
                                          ORDER BY answer_order";
                        $answers_result = $db->query($answers_query);
                        
                        switch($question->id_question_type): 
                        case 1:
                            while($answer = $answers_result->fetch_object()): ?>
                            <input type="radio" name="<?php echo $question->id_question ?>"
                                value="<?php echo $answer->id_answer ?>"
                                <?php if($question->has_dependencies):?> data-side-effects="true" <?php endif?>
                                <?php if($completed):?> disabled <?php endif?>
                                <?php if($answer->selected):?> checked <?php endif?> >
                                <?php echo $answer->text; ?>
                            </input>
                            <br>
                            <?php endwhile ?>
                        <?php break;
                        case 2: ?>
                            <select <?php if($question->has_dependencies):?> data-side-effects="true" <?php endif?>
                                    name="<?php echo $question->id_question ?>"
                                    <?php if($completed):?> disabled <?php endif?>>
                            <option value="" disabled selected/>
                            <?php while($answer = $answers_result->fetch_object()): ?>
                                <option value="<?php echo $answer->id_answer?>"
                                    <?php if($answer->selected):?> selected <?php endif?>>
                                    <?php echo $answer->text?>
                                </option>
                            <?php endwhile ?>
                            </select>
                        <?php break;
                        case 3: 
                            while($answer = $answers_result->fetch_object()): ?>
                            <input type="checkbox" name="<?php echo $question->id_question ?>[]"
                                value="<?php echo $answer->id_answer ?>"
                                <?php if($question->has_dependencies):?> data-side-effects="true" <?php endif?>
                                <?php if($completed):?> disabled <?php endif?>
                                <?php if($answer->selected):?> checked <?php endif?> >
                                <?php echo $answer->text; ?>
                            </input>
                            <br>
                            <?php endwhile ?>
                        <?php endswitch ?>
                    </div>
                </div>
            <?php endwhile ?>
            </div>
        <?php endwhile ?>

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