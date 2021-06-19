<?php

require './base/app-util-base.php';

$app = new AppUtil();

checkUsername($app);

function checkUsername($app) {

    $username = $app->getInputPOST('txt_sign_up_username');

    try {
        $stmt = $app->conn->prepare("SELECT USERNAME "
                . " FROM "
                . "register"
                . " WHERE "
                . " USERNAME =?");// DO NOT USE BINARY USERNAME!

        $stmt->execute(array($username));

        if ($stmt->rowCount() > 0) {
            echo 'false'; //invalid
            return;
        }
    } catch (Exception $exc) {
        echo 'false'; //invalid
        return;
    }


    echo 'true'; //valid
}