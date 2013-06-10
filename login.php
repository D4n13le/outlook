<?php
    require("lib/common.php");

    //user can't see this page if he's already logged in
    if(user_is_logged_in())
      header("location:questions.php") || die();
?>
<!DOCTYPE html>

<html>
<head>
    <title>Login - Outlook</title>
    <link href="style/login.css" rel="stylesheet">
    <link rel='shortcut icon' type='image/x-icon' href='images/world_icon.png' />
</head>

<body>
    <div id="container">
        <div id="container_right">
            <div id="login_container">
                <form action="verify.php" method="post">
                    <p class="input_name">Password</p><input class="input_text" name="password" type="password"><br>
                    <br>
                    <button style="margin-top: 10px;" type="submit">Accedi</button>
                </form>
                <?php
		            if(isset($_GET['error']))
		                echo '<p id="login_failed"> La password inserita risulta invalida, riprovi. </p>';
		        ?>
            </div>

            <div id="description">
                Qui puoi compilare il questionario<br>
                per l'iniziativa <strong>outlook</strong><br>
                dell'ITIS Galilei di Livorno.<br>
            </div>
        </div>

        <div id="container_left">
            <div id="logo_container">
                <div id="logo"></div>

                <p id="phrase">Effettua il login.</p>
            </div>

            <p id="bg_quotes"></p>
        </div>
    </div>

    <div id="footer">
        <div id="links">
            <img alt="" id="iti_logo" src="images/iti_logos.png">

            <div id="link_c">
                <a href="http://galileilivorno.it" style=" margin-left: 20px;">Informazioni</a> <a href=
                "http://galileilivorno.it">ITIS Galilei</a>
            </div>
        </div>
    </div>
</body>
</html>