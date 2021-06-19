<?php

require './base/app-util-base.php';

$app = new AppUtil();

addBankAccount($app);

function addBankAccount($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }


    $bank_name = $app->getInputPOST('monetary-add-bank-account-bank');
    $account_name = $app->getInputPOST('monetary-add-bank-account-name');
    $account_no = $app->getInputPOST('monetary-add-bank-account-no');

    if ($bank_name  === FALSE|| $account_name  === FALSE|| $account_no === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    
    //first check if the bank accont is already added
    try {

        $stmt = $app->sqlSelect("add_bank_account", "ACCOUNT_NO", "BANK_NAME =? AND ACCOUNT_NO =?", array($bank_name, $account_no));

        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("$bank_name bank account '" . $account_no . "' already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt2 = $app->sqlInsert("add_bank_account", "SN, BANK_NAME, ACCOUNT_NAME,ACCOUNT_NO, ENTRY_USER_ID, ENTRY_DATETIME", "0,?,?,?,?,now()", array($bank_name, $account_name, $account_no, $app->userSession->getSessionUsername()));

        if ($stmt2->rowCount() > 0) {
            $app->sendSuccessJSON("Bank account added successfully!", null);
            $stmt2->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!".$exc);
        //echo $exc->getTraceAsString();
    }
}