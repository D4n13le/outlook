<?php
    require_once("settings.php");

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

    $db = NULL;
    function get_db()
    {
        global $dbLocation, $dbUser, $dbPassword, $dbName, $db;

        if(!$db)
            $db = new mysqli($dbLocation, $dbUser, $dbPassword, $dbName);
        return $db;
    }

    //return an array with multiple object, one for each row of the result
    //each object has as many fields as the fields in the select part of the query
    function exec_query_multiple_results($query_string, $types = '')
    {
        

        $result = array();
        $db = get_db();

        if(!$db)
            return array();
    
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
            $result = array();

            $temp_result = $query->get_result();
            while($row = $temp_result->fetch_object())
                $result[] = $row;

            $temp_result->close();
            $query->close();
        }

        //$db->close(); //shouldn't be necessary

        return $result;
    }

    //returns the first object of the result
    //or NULL if there is none
    function exec_query($query_string, $types = '')
    {
        $result = call_user_func_array('exec_query_multiple_results', func_get_args());
        if(count($result) > 0)
            return $result[0];
        else
            return NULL;
    }

    function user_has_completed_the_survey()
    {
        if(!user_is_logged_in())
            return false; //user not logged in

        $id_user = get_user_id();

        return exec_query("SELECT completed FROM users WHERE id_user = ?",
                          "i",
                          $id_user)->completed;
    }

    function user_has_not_completed_the_survey()
    {
        return !user_has_completed_the_survey();
    }
?>