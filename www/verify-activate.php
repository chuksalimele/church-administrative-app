<?php
require './php/base/app-util-base.php';
$app = new AppUtil();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>

        <meta charset="UTF-8">
        <!--<meta http-equiv="Content-Security-Policy" content="default-src 'self' data: gap: https://ssl.gstatic.com 'unsafe-eval'; style-src 'self' 'unsafe-inline'; media-src *">-->
        <meta name="format-detection" content="telephone=no">
        <meta name="msapplication-tap-highlight" content="no">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width">
        <title>Church Mega App</title>

        <!--<link rel="stylesheet"  href="css/jquery.mobile-1.4.5.min.css">-->
        <link rel="stylesheet"  href="css/redeem-theme.css">	
        <link rel="stylesheet"  href="css/jquery.mobile.icons-1.4.5.min.css">
        <link rel="stylesheet"  href="css/jquery.mobile.structure-1.4.5.min.css">

        <link rel="stylesheet"  href="css/church_app_main.css">	
        <link rel="stylesheet"  href="css/entry_validator.css">	

        <link rel="stylesheet" href="css/jquery.mobile.datepicker.css" />
        <link rel="stylesheet" href="css/jquery.mobile.datepicker.theme.css" />
        <link rel="stylesheet" type="text/css" href="./slick/slick.css">
        <link rel="stylesheet" type="text/css" href="./slick/slick-theme.css">

        <script type="text/javascript" src="cordova.js"></script><!-- Important -->
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/jquery.mobile-1.4.5.min.js"></script>
        <script src="js/jquery-ui/datepicker.min.js"></script>
        <script src="js/jquery.mobile.datepicker.min.js"></script>
        <script src="js/libs/jquery-validate/jquery.validate.min.js"></script>
        <script src="js/libs/jquery-validate/additional-methods.min.js"></script>
        <script src="slick/slick.min.js"></script>

        <script type="text/javascript" src="js/index.js"></script>

        <script type="text/javascript" src="js/app/church_app_main.js"></script>
        <script type="text/javascript" src="js/app/controls.js"></script>
        <script type="text/javascript" src="js/app/entry_validator.js"></script>
        <script type="text/javascript" src="js/app/view_record.js"></script>

        <style>
            .custom-corners .ui-bar {
                -webkit-border-top-left-radius: inherit;
                border-top-left-radius: inherit;
                -webkit-border-top-right-radius: inherit;
                border-top-right-radius: inherit;
            }
            .custom-corners .ui-body {
                border-top-width: 0;
                -webkit-border-bottom-left-radius: inherit;
                border-bottom-left-radius: inherit;
                -webkit-border-bottom-right-radius: inherit;
                border-bottom-right-radius: inherit;
            }
        </style>
    </head>

    <body>

        <div data-role="header">
            <h1>MercyApp</h1>
            <a data-rel="back" class="ui-btn ui-btn-left ui-alt-icon ui-nodisc-icon ui-corner-all ui-btn-icon-notext ui-icon-carat-l">Back</a>
        </div><!-- /header -->
        <?php
        $login_page = "";
         $config = new Config;
        if ($config->server_host == "localhost") {
            //when locally hosted
            $login_page = "'/churchmegaapp/www/index.html#login-page'";
        } else {
            //when internet hosted
            $login_page = "'/index.html#login-page'";
        }

        $success_content = '<div data-role="main" class="ui-content">
            <h1>Congratulations!!!</h1>
            <p>Your email address has been verified and account activated.</p>
            <p>Your sign-up process is complete!</p>
            <p>
                If you wish to login at this time you may click below.
                <a id="verify-activite-login" data-role="button">Login</a>
            </p>
        </div>
        <script>
            $(document).ready(function () {
                $("#verify-activite-login").on("click", function () {
                    window.location =  ' . $login_page . ';
                });
            });
        </script>';


        $error_content = '<div data-role="main" class="ui-content">
            <h1>Sorry!</h1>
            <p>There was a problem activating your account. Please try again later.</p>            
        </div>';


        $already_activated_content = '<div data-role="main" class="ui-content">
            <h1>Already Activated!</h1>
            <p>Your email is already verified and account activated.</p>            
            <p>
            <a id="verify-already-activite-login" data-role="button">Want to login?</a>
            </p>            
        </div>
        <script>
            $(document).ready(function () {
                $("#verify-already-activite-login").on("click", function () {
                    window.location = ' . $login_page . ';
                });
            });
        </script>';


        $illegal_access_content = '<div data-role="main" class="ui-content">
            <h1>Wrong approach!!!</h1>
            <p>Please access this page via your email. Use the email we sent to you.</p>            
        </div>';

        //echo $error_content;
        //echo $illegal_access_content;
        //echo $already_activated_content;
        //echo $success_content;
        $email = $app->getInputGET("email");
        $username = $app->getInputGET("username");
        $activation_hash = $app->getInputGET("activation-hash");

        if ($email === FALSE || empty($email) || $username === FALSE || empty($username) || $activation_hash === FALSE || empty($activation_hash)) {
            echo $illegal_access_content;
            return;
        }

        try {

            $stmt = $app->sqlSelect("register", "VERIFIED_EMAIL, EMAIL_VERIIFY_HASH", "USERNAME =?", array($username));

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row["EMAIL_VERIIFY_HASH"] != $activation_hash) {
                    echo $illegal_access_content;
                } else if ($row["VERIFIED_EMAIL"] == 1) {
                    echo $already_activated_content;
                } else {
                    $stmt2 = $app->sqlUpdate("register", "VERIFIED_EMAIL=1", "USERNAME=?", array($username));

                    if ($stmt2->rowCount() > 0) {
                        echo $success_content;
                    } else {
                        echo $error_content;
                    }
                    $stmt2->closeCursor();
                }
                $stmt->closeCursor();
            } else {
                echo "<h2>Unknown user</h2>";
            }
        } catch (Exception $exc) {
            echo $error_content;
        }
        ?>

    </body>

</html>
