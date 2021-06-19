<?php

require './base/app-util-base.php';

$app = new AppUtil();

authRecordsModify($app);

function authRecordsModify($app) {

    try {
        $record_modify_username = $app->getInputPOST('record_modify_username');
        $record_modify_password = $app->getInputPOST('record_modify_password');

        if ($record_modify_username === FALSE || $record_modify_password === FALSE) {
            return $app->sendErrorJSON("Please try again!");
        }
        //sha1($str)
        $stored_username = $app->userSession->getSessionUsername();
        $stored_hash_pasword = $app->userSession->getSessionUserHashPassword();

        if (!$app->userSession->isBasicSessionAvailable()) {
            $app->sendSessionNotAvaliableJSON(null);
            return;
        }

        if ($stored_username == $record_modify_username && $stored_hash_pasword == sha1($record_modify_password)) {
            $app->sendSuccessJSON("Authentication was successful!",null);
        } else {
            $app->sendErrorJSON("Invalid username or password!");
        }
    } catch (Exception $exc) {
        $app->sendErrorJSON("Could not register you.\nPlease try again later!");
        return;
    }
}
