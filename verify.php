<?php 
    //called in a POST request
    //parameter expected:
    //  password: the user id to log with

    require_once("lib/common.php");

    //logged users can't see this page
    if(user_is_logged_in())
        header("Location:questions.php") || die();

    $user_id = $_POST['password'];

    $result = exec_query('SELECT id_user FROM users WHERE id_user = ?', 'i', $user_id);
    if($result)
    {
        //Rimuovo eventuali sessioni precedenti
        session_start();
        session_unset();
        session_destroy();

        //Inizializzo nuova sessione
        session_start();
        
        $_SESSION['user'] = $user_id;
        header("Location:questions.php") || die();
    }
    else
        header("Location:login.php?error") || die();
?>