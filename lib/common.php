<?php
    //require_once("settings.php");

    function open_session()
    {
        if(session_status() == PHP_SESSION_NONE)
            session_start();
    }

    function logout()
    {
        open_session();
        session_unset();
        session_destroy();
    }

    function get_user_id()
    {
        open_session();
        if(empty($_SESSION['user']))
            return NULL;
        else
            return $_SESSION['user'];
    }

    function user_is_logged_in()
    {
        open_session();
        return get_user_id() !== NULL;
    }

    function user_is_not_logged_in()
    {
        return !user_is_logged_in();
    }

    function exec_query($query_string, $types = '')
    {
        require_once("settings.php");

        $result = NULL;
        $db = new mysqli($dbLocation, $dbUser, $dbPassword, $dbName);

        if(!$db)
            return NULL;
    
        $query = $db->prepare($query_string);

        if(func_num_args() > 2)
        {
            $args = func_get_args();
            array_shift($args);

            $refs = array();
                foreach($args as $key => $value)
            $refs[$key] = &$args[$key];
            
            call_user_func_array(array($query, "bind_param"), $refs);
        }

        if($query->execute())
        {
            $query->bind_result($result);
            if(!$query->fetch())
                return NULL;
            $query->close();
        }

        $db->close();

        return $result;
    }

    function user_has_completed_the_survey()
    {
        if(!user_is_logged_in())
            return false; //user not logged in

        $id_user = get_user_id();

        return exec_query("SELECT completed FROM users WHERE id_user = ?",
                          "i",
                          $id_user);
    }

    function user_has_not_completed_the_survey()
    {
        return !user_has_completed_the_survey();
    }
?>