<?php

require './base/app-util-base.php';

$app = new AppUtil();

updateBankAccount($app);

function updateBankAccount($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $bank_name = $app->getInputPOST('BANK_NAME');
    $account_name = $app->getInputPOST('ACCOUNT_NAME');
    $account_no = $app->getInputPOST('ACCOUNT_NO'); 
    $sn = $app->getInputPOST('SN');
    
    if ( $sn === FALSE || $bank_name === FALSE || $account_name === FALSE || $account_no === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    try {
        $stmt = $app->sqlUpdate("add_bank_account", "BANK_NAME=? , ACCOUNT_NAME=? , ACCOUNT_NO=?", "SN=? ENTRY_USER_ID=?",
                array($bank_name, $account_name, $account_no, $sn, $app->userSession->getSessionUsername()));

        if ($stmt->rowCount() > 0) {
            $app->sendSuccessJSON("Bank account updated successfully!", null);
        } else {
                //check if the reason is because the user was not the one who added the record in the first place.
                
                if ($app->checkAuthorizedOperation("add_bank_account", "SN", $sn)) {
                    $app->sendIgnoreJSON("Nothing updated!");
                } else {
                    $app->sendUnauthorizedOperationJSON("You cannot update a record that does not originate from you!");
                }
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again!");
    }
}
