<?php
    require_once(__DIR__.'\..\settings.php');

    function open_session()
    {
        if(session_id() == '')
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
    //returns FALSE if it fails
    function exec_query_multiple_results($query_string, $types = '')
    {
        $result = array();
        $db = get_db();

        if(!$db) //check connection
            return FALSE;

        $query = $db->prepare($query_string);

        if(!$query) //error while preparing the statement
            return FALSE;

        if(func_num_args() > 2)
        {
            $args = func_get_args();
            array_shift($args);

            $refs = array();
                foreach($args as $key => $value)
            $refs[$key] = &$args[$key];
            
            call_user_func_array(array($query, 'bind_param'), $refs);
        }


        $params = array();
        $row = array();
        $hits = array();

        $meta = $query->result_metadata(); 

        if($meta)
        {
            while ($field = $meta->fetch_field())
            { 
                $row[$field->name] = NULL;
                $params[] = &$row[$field->name];
            }

            call_user_func_array(array($query, 'bind_result'), $params);
        }   

        if($query->execute())
        {
            $obj = new stdClass();
            while ($query->fetch())
            {
                foreach($row as $key => $val)
                { 
                    $obj->{$key} = $val; 
                }
                $hits[] = clone $obj;
            }
        }

        $query->close();

        //$db->close(); //shouldn't be necessary
        return $hits;
    }

    function disable_autocommit()
    {
        $db = get_db();
        if($db)
            $db->autocommit(FALSE);
    }

    function enable_autocommit()
    {
        $db = get_db();
        if($db)
            $db->autocommit(TRUE);
    }

    function commit()
    {
        $db = get_db();
        if($db)
            $db->commit();
    }

    function rollback()
    {
        $db = get_db();
        if($db)
            $db->rollback();
    }

    //returns the first object of the result
    //or NULL if there is none
    //or FALSE if the query fails
    function exec_query($query_string, $types = '')
    {
        $args = func_get_args();
        $result = call_user_func_array('exec_query_multiple_results', $args);
        if($result === FALSE)
            return FALSE;
        if(count($result) == 0)
            return NULL;
        return $result[0];
    }

    function user_has_completed_the_survey()
    {
        if(!user_is_logged_in())
            return FALSE; //user not logged in

        $id_user = get_user_id();

        $result = exec_query("SELECT completed FROM users WHERE id_user = ?", "i", $id_user);
        if($result)
            return $result->completed;
        else
            return FALSE; //an error happened/invalid id
    }

    function user_has_not_completed_the_survey()
    {
        return !user_has_completed_the_survey();
    }

    function build_question_marks_string($n)
    {
        return implode(',', array_fill(0, $n, '?'));
    }
?>