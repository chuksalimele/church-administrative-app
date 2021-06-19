<?php

require './base/app-util-base.php';

$app = new AppUtil();

registerParish($app);

function registerParish($app) {


    $username = $app->getInputPOST('register-parish-admin-username');
    $password = $app->getInputPOST('register-parish-admin-password');
    $first_name = $app->getInputPOST('register-parish-admin-first-name');
    $last_name = $app->getInputPOST('register-parish-admin-last-name');
    $email = $app->getInputPOST('register-parish-admin-email');
    $verification_code = $app->getInputPOST('register-parish-admin-email-verification-code');

    $parish_name = $app->getInputPOST('register-parish-name');
    $parish_address = $app->getInputPOST('register-parish-address');
    $parish_head_of = $app->getInputPOST('register-parish-head-of');
    $division_name = $app->getInputPOST('register-parish-division-name');
    $under_area = $app->getInputPOST('register-parish-under-area');
    $under_zone = $app->getInputPOST('register-parish-under-zone');
    $under_province = $app->getInputPOST('register-parish-under-province');
    $under_region = $app->getInputPOST('register-parish-under-region');
    $under_national = $app->getInputPOST('register-parish-under-national');


    if ($username === FALSE || $password === FALSE || $first_name === FALSE || $last_name === FALSE || $email === FALSE || $verification_code === FALSE || $parish_name === FALSE || $parish_address === FALSE || $parish_head_of === FALSE || $division_name === FALSE || $under_area === FALSE || $under_zone === FALSE || $under_province === FALSE || $under_region === FALSE || $under_national === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    //check if the username , email and verification code correspond to that 
    //saved in this user session

    if ($username !== $_SESSION["super_admin_username"] || !isset($_SESSION["super_admin_username"])) {
        $app->sendIgnoreJSON("Usernam not recognized in this session!");
        return;
    }

    if ($email !== $_SESSION["super_admin_email"] || !isset($_SESSION["super_admin_email"])) {
        $app->sendIgnoreJSON("Email not recognized in this session!");
        return;
    }

    if ($verification_code !== $_SESSION["super_admin_verification_code"] || !isset($_SESSION["super_admin_verification_code"])) {
        $app->sendIgnoreJSON("Verification code not recognized in this session!");
        return;
    }

    try {

        //check if user already exist .  DO NOT USE BINARY USERNAME!
        $stmt = $app->sqlSelect("register", "USERNAME", "USERNAME =?", array($username));
        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Username already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt->closeCursor();

        //check if parish name already exist.
        $stmt = $app->sqlSelect("parish_register", "PARISH_NAME", "PARISH_NAME =?", array($parish_name));
        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Parish name already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt->closeCursor();

        $stmt = $app->sqlSelect("parish_register", "PARISH_SN", "PARISH_NAME ='' OR PARISH_NAME=NULL", array($username));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $empty_parish_name_sn = -1;
        if ($stmt->rowCount() > 0) {
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

        $stmt->closeCursor();
        //The USER_GROUPS  is Super Admin,Member

        $app->conn->beginTransaction();

        $stmt = $app->conn->prepare("INSERT INTO "
                . " register "
                . "(USER_GROUPS, FIRST_NAME, LAST_NAME, EMAIL, EMAIL_VERIIFY_HASH,  VERIFIED_EMAIL , USERNAME, PASSWORD,DATE_REGISTERED, PARISH_SN)"
                . "VALUES('Member',?,?,?,?,1,?, SHA('$password'),now(),?) ");

        $stmt->execute(array($first_name, $last_name, $email, $verification_code, $username, $empty_parish_name_sn));
        if ($stmt->rowCount() <= 0) {
            $app->sendErrorJSON("Could not create user!");
            $stmt->closeCursor();
            return;
        }

        //$under_area = "";
        //echo $username." ".$parish_name." ". $parish_address." ". $under_area." ". $under_zone." ". $under_province." ". $under_region." ". $under_national;

        $stmt = $app->conn->prepare("INSERT INTO "
                . " parish_register "
                . " (PARISH_SN, PARISH_SUPER_ADMIN, PARISH_NAME, PARISH_ADDRESS, UNDER_AREA, UNDER_ZONE,UNDER_PROVINCE,UNDER_REGION,UNDER_NATIONAL)"
                . "VALUES(0,?,?,?,?,?,?,?,?)");

        $stmt->execute(array($username, $parish_name, $parish_address, $under_area, $under_zone, $under_province, $under_region, $under_national));

        $success = false;
        if ($stmt->rowCount() > 0) {
            $stmt->closeCursor();

            //update the parish sn in the register to reflect the real one
            $stmt = $app->conn->prepare("UPDATE "
                    . " register "
                    . " SET "
                    . " PARISH_SN = LAST_INSERT_ID()"
                    . " WHERE "
                    . " USERNAME = ?");

            $stmt->execute(array($username));
            $stmt->closeCursor();
            
            If ($parish_head_of != null && $parish_head_of != '') {
                $success = false;
                $stmt = $app->conn->prepare("INSERT INTO "
                        . " head_parishes "
                        . " (SN, PARISH_NAME, HEAD_CATEGORY, DIVISION_NAME,PARISH_SN)"
                        . "VALUES(0,?,?,?,LAST_INSERT_ID())");

                $stmt->execute(array($parish_name, $parish_head_of, $division_name));
                if ($stmt->rowCount() > 0) {
                    $success = true;
                    $stmt->closeCursor();
                }
            } else {
                $success = true;
            }
        }

        if ($success) {
            $app->conn->commit();
            $app->sendSuccessJSON("Parish was successfully registered!", null);
        } else {
            $app->conn->rollback();
            $app->sendErrorJSON("Parish not registered!");
        }
    } catch (Exception $exc) {

        $app->sendErrorJSON("Could not register parish.\nPlease try again later!" . $exc);
        return;
    }
}
