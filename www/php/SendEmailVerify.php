<?php

require './base/app-util-base.php';
require './phpmailer/PHPMailerAutoload.php';

$app = new AppUtil();

verifyEmail($app);

function verifyEmail($app) {

    $first_name = $app->getInputPOST('firstName');
    $last_name = $app->getInputPOST('lastName');
    $email = $app->getInputPOST('email');
    $username = $app->getInputPOST('username');

    if ($first_name === FALSE || $last_name === FALSE || $email === FALSE || $username === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    $rand = rand(0, 10000);
    $hash = sha1($rand);

    //echo $hash.'<br/>';
    //echo $username.'<br/>';
    
    try {

        $app->conn->beginTransaction();

        $stmt = $app->sqlUpdate("register", "EMAIL_VERIIFY_HASH=?", ""
                . "(EMAIL_VERIIFY_HASH='' "
                . " OR EMAIL_VERIIFY_HASH='NULL')"
                . " AND USERNAME=?"
                . " AND VERIFIED_EMAIL = 0 "
                . " ", array($hash, $username));

        if ($stmt->rowCount() > 0) {
            $result = sendVerificationEmail($app, $first_name, $last_name, $email, $username, $hash);
            if ($result) {
                $app->conn->commit();
                $user = array();
                $user["username"] = $username;
                $user["firstName"] = $first_name;
                $user["lastName"] = $last_name;
                $user["email"] = $email;
                $app->sendSuccessJSON("Successful!",$user);
            } else {
                $app->conn->rollback();
                $app->sendErrorJSON("Verification email could not be sent!");
            }
        } else {
            $app->sendIgnoreJSON("Verification email aleady sent or account already activated!");
            return false;
        }

        $stmt->closeCursor();
    } catch (Exception $exc) {
        $app->sendErrorJSON("Please try again!");
        return false;
    }
}

function sendVerificationEmail($app, $first_name, $last_name, $email, $username, $hash) {

    //Please note: if you experience "timeouts" or 
    //problems connecting due to an unreliable internet 
    //connection, consider increasing the max_execution_time 
    //setting in the php.ini file on your server, to a value 
    //such as 120 (instead of the default value 30). 

    $mail = new PHPMailer;

    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP

    $config = new Config;

    //NOTE Some ISPs may block the use of other stmp mail servers - The want theirs to be used.
    $mail->Host = $config->smtp_main_server;  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = $config->smtp_username;                 // SMTP username
    $mail->Password = $config->smtp_password;                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->setFrom($config->smtp_username, $config->app_name);
    $recipientFullName = $first_name . ' ' . $last_name;
    $mail->addAddress($email, $recipientFullName);     // Add a recipient
    //$mail->addAddress('ellen@example.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    if($config->server_host == "localhost"){
        //when locally hosted
        $path = "/churchmegaapp/www/verify-activate.php";
    }else{
        //when internet hosted
        $path = "/verify-activate.php";
    }
    //NOTE SERVER HOST MUST BE PRECEEDED BY 
    //THE PROTOCOL 'http://' OR 'https://'
    //TO AVOID YAHOO WAHALA WHEN SENDING 
    //EMAIL TO YAHOO ACCOUNT - NEVER FORGET THIS!!!
    $url = 'http://'.$config->server_host
            . $path
            . '?'
            . 'email='.$email.'&'
            . 'username='.$username.'&'
            . 'activation-hash='.$hash;
    
    $mail->Subject = 'Email Verification';
    $main_body_desc = 'Your account has been successfully created. In order for your account to be activated for use '
            . 'we have to verify that the email address you provided to us is valid.'
            . '<p>'
            . 'Please note that if a newer verification email has been sent then this one becomes invalid.'
            . '</p>';

    $mail->Body = '<h3>Thanks, ' . $recipientFullName . '</h3>'
            . $main_body_desc
            . '<p>'
            . 'Please click <a href="' . $url . '">here</a> to activate your account.'
            . '</p>';

    //AltBody for the case of non-HTML mail clients
    $mail->AltBody = '<h3>Thanks, ' . $recipientFullName . '</h3>'
            . $main_body_desc
            . '<p>'
            . 'Please copy the link below to open in your brower so as to'
            . 'activate your account.'
            . '</p>'
            . '<p>'
            . $url
            . '</p>';

    if (!$mail->send()) {
        //echo 'Message could not be sent.';
        //echo 'Mailer Error: ' . $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}
