<?php
  session_start();

  if(empty($_SESSION['user']))
    header("location:login.php") || die(); //user not logged in

  require_once("settings.php");

  $completed = false;
  $db = new mysqli($dbLocation, $dbUser, $dbPassword, $dbName);

  if($db)
  {
    $query = $db->prepare("SELECT completed FROM users WHERE id_user = ?");
    
    $query->bind_param('i', $_SESSION['user']);

    if($query->execute())
    {
        $query->bind_result($completed);
        $query->fetch();
        $query->close();
    }
    
    $db->close();
  }
  
  if(!$completed)
    header("Location: login.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Outlook - Questionario completato</title>
    <link rel="stylesheet" type="text/css"  href="style/questions.css"/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500' rel='stylesheet' type='text/css'>
  </head>
  <body style="background-color:#f7f7f7; ">
    <div id="result_container">
      <div id="result_container_left_side">
      </div>
      <div id="result_container_right_side">
        <p id="result"> Grazie per aver compilato il questionario!</p> <br>
      </div>
    </div>
      <p id="footer_result">
        <a id="questions_link" href="questions.php"> rivedi il questionario </a>
        <a id="logout_link" href="logout.php"> logout </a>
      </p>
    </div>
  </body>
</html>