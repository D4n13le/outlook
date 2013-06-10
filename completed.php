<?php
  require_once('lib/common.php');

  //to show this page user has to be logged in and have completed the survey
  //if he's not, redirect to login page
  if(user_is_not_logged_in() || user_has_not_completed_the_survey())
    header('location:questions.php') || die();
?>
<!DOCTYPE html>

<html>
<head>
    <title>Outlook - Questionario completato</title>
    <link href="style/questions.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500" rel="stylesheet" type="text/css">
    
    <link rel='shortcut icon' type='image/x-icon' href='images/world_icon.png' />

</head>

<body style="background-color:#f7f7f7;">
    <div id="result_container">
        <div id="result_container_left_side"></div>

        <div id="result_container_right_side">
            <p id="result">Grazie per aver compilato il questionario!</p><br>
        </div>
    </div>

    <p id="footer_result"><a href="questions.php" id="questions_link">rivedi il questionario</a> <a href="logout.php"
    id="logout_link">logout</a></p>
</body>
</html>