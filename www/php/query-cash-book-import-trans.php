<?php

require './base/app-util-base.php';

$app = new AppUtil();

queryCashBookImportTrans($app);

function queryCashBookImportTrans($app) {
    $identifier = $app->getInputPOST('$identifier');
    $bank = $app->getInputPOST('$bank');
    $account_name = $app->getInputPOST('$account_name');
    $account_no = $app->getInputPOST('$account_no');

    if ($identifier === FALSE 
            ||$bank === FALSE 
            ||$account_name === FALSE 
            ||$account_no === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    try {

        $stmt = $app->conn->prepare("SELECT TRANDATE, TRANCODE, REMARKS, DEBIT, CREDIT "
                . " FROM "
                . " cash_book "
                . " WHERE "
                . " ENTRY_METHOD='IMPORT'"
                . " AND IDENTIFIER=?"
                . " AND ENTRY_USER_ID=?");


        $stmt->execute(array($identifier, $app->userSession->getSessionUsername()));

        $table = $app->createDBTableView($stmt);

        $arr = array();
        $arr["bank"] = $bank;
        $arr["accountName"] = $account_name;
        $arr["accountNo"] = $account_no;
        $arr["transactions"] = $table;

        $app->sendSuccessJSON("Successful", $arr);
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
