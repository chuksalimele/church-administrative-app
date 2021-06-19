<?php

require './base/app-util-base.php';

$app = new AppUtil();

postCashBook($app);

function postCashBook($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }


    $date = $app->getInputPOST('cash_book_date');
    $bank_name = $app->getInputPOST('cash_book_bank');
    $account_name = $app->getInputPOST('cash_book_account_name');
    $account_no = $app->getInputPOST('cash_book_account_no');
    $trancode = $app->getInputPOST('cash_book_tran_code');
    $description = $app->getInputPOST('cash_book_description');
    $tran_type = $app->getInputPOST('cash_book_tran_type');
    $amount = $app->getInputPOST('cash_book_amount');

    if ($date === FALSE || $bank_name  === FALSE|| $account_name === FALSE
            || $account_no === FALSE || $trancode === FALSE 
            || $description === FALSE || $tran_type === FALSE || $amount === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    $credit = 0;
    $debit = 0;

    if (strtolower($tran_type) == "credit") {
        $credit = $amount;
    } else {
        $debit = $amount;
    }
    
    //first check if the bank accont is already added
    try {

        $stmt = $app->sqlInsert("cash_book", "SN, TRANDATE, TRANCODE, BANK, ACCOUNT_NAME, ACCOUNT_NO,  DEBIT, CREDIT, REMARKS, ENTRY_USER_ID, ENTRY_DATETIME",
                "0,?,?,?,?,?,?,?,?,?,now()", 
                array($date, $trancode, $bank_name, $account_name, $account_no, $debit, $credit, $description,
                                    $app->userSession->getSessionUsername()));

        if ($stmt->rowCount() > 0) {
            $app->sendSuccessJSON("The operation was successfully!", null);
            $stmt->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again!");
        //echo $exc->getTraceAsString();
    }
}
