<?php 
    if(!isset($_POST['password']) )
        header("location:login.php") || die(); //user not logged in

    $pw = $_POST['password'];

    require_once("settings.php");
    $db = new mysqli($dbLocation, $dbUser, $dbPassword, $dbName);

    
    if($db->errno)
        die("Errore connessione al database!");

    $query = $db->prepare("SELECT id_user FROM users WHERE id_user = ?");
    
    $query->bind_param('i', $pw);

    $newlocation = "login.php?error";

    if($query->execute())
    {
        $query->bind_result($id_user);

        if($query->fetch())
        {
            $newlocation = "questions.php";
            //Rimuovo eventuali sessioni precedenti
            session_start();
            session_unset();
            session_destroy();
            //Inizializzo nuova sessione
            session_start();
            
            $_SESSION['user'] = $id_user;
            $_SESSION['start_time'] = time();
        }
        $query->close();
    }
    
    $db->close();
    header("Location: $newlocation");
?>