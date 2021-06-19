<?php

//require './base/app-util-base.php';
require './phpmailer/PHPMailerAutoload.php';

//$app = new AppUtil();

//Please note: if you experience "timeouts" or 
//problems connecting due to an unreliable internet 
//connection, consider increasing the max_execution_time 
//setting in the php.ini file on your server, to a value 
//such as 120 (instead of the default value 30). 

//

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
//NOTE Some ISPs may block the use of other stmp mail servers - The want theirs to be used.
$mail->Host = 'smtp.gmail.com;smtp.mail.yahoo.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'chuksalimele@gmail.com';                 // SMTP username
$mail->Password = 'Kachukwu1234';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

$mail->setFrom('chuksalimele@yahoo.com', 'Mailer');
$mail->addAddress('chuksalimele@yahoo.com', 'Chukzee for life');     // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here php email test';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

echo 'Sending...';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}