<?php 
    //called in a POST request
    //parameter expected:
    //  password: the user id to log with

    require_once("lib/common.php");

    //logged users can't see this page
    if(user_is_logged_in())
        header("Location:questions.php") || die();

    $user_id = $_POST['password'];

    //check if user exists
    $result = exec_query('SELECT id_user FROM users WHERE id_user = ?',
                         'i',
                         $user_id);
    if($result)
    {
        //if it exists, redirect to the survey

        logout(); //clean any old sessions
        open_session(); //start a new session
        
        $_SESSION['user'] = $user_id;
        header("Location:questions.php") || die();
    }
    else
        //if it doesn't exist, show an error
        header("Location:login.php?error") || die();
?>