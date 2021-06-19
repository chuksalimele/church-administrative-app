<?php

require './base/app-util-base.php';

$app = new AppUtil();

addBank($app);

function addBank($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $bank_name = $app->getInputPOST('monetary-add-bank');

    if ($bank_name === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    //first check if the bank is already added

    try {

        $bank_name = strtoupper($bank_name); // convert bank name to upper case for purpose of uniformity

        $stmt = $app->sqlSelect("add_bank", "BANK_NAME", "BANK_NAME =?", array($bank_name));

        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Bank name '" . $bank_name . "' already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt2 = $app->sqlInsert("add_bank", "SN, BANK_NAME, ENTRY_USER_ID, ENTRY_DATETIME", "0,?,?,now()", array($bank_name, $app->userSession->getSessionUsername()));

        if ($stmt2->rowCount() > 0) {
            $app->sendSuccessJSON("Bank added successfully!", null);
            $stmt2->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}