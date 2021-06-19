<?php

require './base/app-util-base.php';

$app = new AppUtil();

queryCashBook($app);

function queryCashBook($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }


    $start_date = $app->getInputPOST('cash_book_start_date');
    $end_date = $app->getInputPOST('cash_book_end_date');
    $bank_name = $app->getInputPOST('cash_book_bank_name');
    $bank_account_no = $app->getInputPOST('cash_book_bank_account_no');

    if ($start_date === FALSE || $end_date  === FALSE
            || $bank_name === FALSE || $bank_account_no === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    $parishID = $app->userSession->getSessionUserParishID();

    //first check if the bank accont is already added
    try {

        //NOTE: we are only intrested in the report of the individual parish.
        //So we will ensure we retrieve the cash book of the parish of this user
        //by using sub query 

        $stmt = $app->conn->prepare("SELECT * "
                . " FROM "
                . " cash_book "
                . " WHERE "
                . " TRANDATE BETWEEN ? AND ?"
                . " AND "
                . " ENTRY_USER_ID "
                . " IN (SELECT "
                . " FROM register "
                . " WHERE PARISH_SN = ?)");

        $stmt->execute(array($start_date, $end_date, $parishID));

        $table = $app->createDBTableView($stmt);
        
        $app->sendSuccessJSON("Successful", $table);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again!");
        //echo $exc->getTraceAsString();
    }
}
