<?php

require './base/app-util-base.php';

$app = new AppUtil();

updateCashBook($app);

function updateCashBook($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }


    $sn = $app->getInputPOST('SN');
    $date = $app->getInputPOST('TRANDATE');
    $bank_name = $app->getInputPOST('BANK');
    $account_name = $app->getInputPOST('ACCOUNT_NAME');
    $account_no = $app->getInputPOST('ACCOUNT_NO');
    $trancode = $app->getInputPOST('TRANCODE');
    $remarks = $app->getInputPOST('REMARKS');
    $debit = $app->getInputPOST('DEBIT');
    $credit = $app->getInputPOST('CREDIT');

    if ($sn === FALSE || $date === FALSE || $bank_name  === FALSE|| $account_name === FALSE
            || $account_no === FALSE || $trancode === FALSE
            || $remarks === FALSE || $debit  === FALSE|| $credit === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    try {
        
        $stmt = $app->sqlUpdate("cash_book", "TRANDATE =?, "
                . "TRANCODE =?, BANK =?, ACCOUNT_NAME =?, ACCOUNT_NO =?, "
                . " DEBIT =?, CREDIT =?, REMARKS =?, ENTRY_DATETIME=now()",
                /* where */ "SN =? AND ENTRY_USER_ID=?", "?,?,?,?,?,?,?,?,?,?",
                array($date, $trancode, $bank_name, $account_name, $account_no, $debit, $credit, $remarks,
                            $sn, $app->userSession->getSessionUsername()));

        if ($stmt->rowCount() > 0) {
            $app->sendSuccessJSON("The operation was successfully!", null);
        } else {
            //check if the reason is because the user was not the one who added the record in the first place.
            
            if ($app->checkAuthorizedOperation("cash_book", "SN", $sn)) {
                $app->sendIgnoreJSON("Nothing updated!");
            } else {
                $app->sendUnauthorizedOperationJSON("You cannot update a record that does not originate from you!");
            }
        }
        $stmt->closeCursor();
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again!");
        //echo $exc->getTraceAsString();
    }
}
