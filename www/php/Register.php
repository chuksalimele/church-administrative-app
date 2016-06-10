<?php

include './base/app-util-base.php';

$app = new AppUtil();

signUpUser($app);

function signUpUser($app) {

    try {
        $first_name = $app->getInputPOST('txt_sign_up_first_name');
        $last_name = $app->getInputPOST('txt_sign_up_last_name');
        $email = $app->getInputPOST('txt_sign_up_email');
        $username = $app->getInputPOST('txt_sign_up_username');
        $password = $app->getInputPOST('txt_sign_up_password');

        if ($first_name === FALSE || $last_name === FALSE || $email === FALSE || $username === FALSE || $password === FALSE) {
            return $app->sendErrorJSON("Could not register you.\nPlease try again!");
        }

        //check if user already exist .  DO NOT USE BINARY USERNAME!
        $stmt = $app->sqlSelect("register", "USERNAME", "USERNAME =?", array($username));
        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Username already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt = $app->sqlSelect("parish_register", "PARISH_SN", "PARISH_NAME ='' OR PARISH_NAME=NULL", array($username));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $empty_parish_name_sn = -1;
        if ($stmt->rowCount() > 0) {
            //$column_name = "PARISH_SN";
            $empty_parish_name_sn = $row["PARISH_SN"]; //maps the colum name in result set
        } else {
            //DESIGN REMINDER!!! PLEASE CREATE A RECORD IN parish_register
            //TABLE WITH EMPTY OR NULL PARISH_NAME FOR SIGNED UP USERS
            // WITHOUT PARISH ASSIGNED TO THEM YET! BY DESIGN THE USERS SHOULD
            //SPECIFY THIER PARISH AFTER SIGN UP SO AN EMPTY PARISH NAME MUST
            //BE PROVIDER TO ALLOW SIGN UP WITHOUT PARISH ASSIGNED.
            $app->sendErrorJSON("PARISH NOT FOUND! Please try again later!");
            $stmt->closeCursor();
            return;
        }

        $stmt2 = $app->conn->prepare("INSERT INTO "
                . "register"
                . " (FIRST_NAME, LAST_NAME, EMAIL, USERNAME, PASSWORD,DATE_REGISTERED, PARISH_SN)"
                . "VALUES(?,?,?,?, SHA('$password'),now(),?) ");

        $stmt2->execute(array($first_name, $last_name, $email, $username, $empty_parish_name_sn));

        if ($stmt2->rowCount() > 0) {
            $app->sendSuccessJSON("Registration was successful!", null);
            $stmt2->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        $app->sendErrorJSON("Could not register you.\nPlease try again later! ");
        return;
    }
}
