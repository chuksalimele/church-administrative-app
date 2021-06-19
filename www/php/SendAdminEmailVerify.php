<?php

require './base/app-util-base.php';
require './phpmailer/PHPMailerAutoload.php';

$app = new AppUtil();

verifyAdmimEmail($app);

function verifyAdmimEmail($app) {

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

        $result = sendVerificationEmail($app, $first_name, $last_name, $email, $username, $hash);

        if ($result) {
            
            $_SESSION["super_admin_username"] = $username;
            $_SESSION["super_admin_email"] = $email;
            $_SESSION["super_admin_verification_code"] = $hash;
            
            $user = array();
            $user["username"] = $username;
            $user["firstName"] = $first_name;
            $user["lastName"] = $last_name;
            $user["email"] = $email;
            $user["verificationCode"] = $hash;
            $app->sendSuccessJSON("Successful!", $user);
        } else {
            $app->sendErrorJSON("Verification code could not be sent!");
        }
    } catch (Exception $exc) {
        $app->sendErrorJSON("Please try again later!" . $exc);
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

    $mail->Subject = 'Admin Email Verification';
    $main_body_desc = 'In order for your account to be activated for use '
            . 'we have to verify that the email address you provided to us is valid.';

    $mail->Body = '<h3>Thanks, ' . $recipientFullName . '</h3>'
            . $main_body_desc
            . '<p>'
            . 'Below is the verification code required for creating the parish administrator account.'
            . ' Please note that if a newer verification code has been sent then this one becomes invalid.'
            . '</p>'
            . '<br/>'
            . '<p>'
            . 'Username: ' . $username
            . '<br/>Verification code: ' . $hash
            . '</p>';

    //AltBody for the case of non-HTML mail clients
    $mail->AltBody = $mail->Body;

    if (!$mail->send()) {
        //echo 'Message could not be sent.';
        //echo 'Mailer Error: ' . $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}
