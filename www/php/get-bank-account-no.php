<?php

require './base/app-util-base.php';

$app = new AppUtil();

getBankAccountNoList($app);

function getBankAccountNoList($app) {

    try {
        $bank = $app->getInputPOST('bank');
        $account_name = $app->getInputPOST('account_name');

        if ($bank === FALSE || $account_name === FALSE) {
            return $app->sendErrorJSON("Please try again!");
        }

        $stmt = $app->conn->prepare("SELECT ACCOUNT_NO "
                . " FROM "
                . " add_bank_account "
                . " WHERE "
                . " BANK_NAME=?"
                . " AND "
                . " ACCOUNT_NAME=?");

        $stmt->execute(array($bank, $account_name));

        $account_no ='';
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $account_no = $row["ACCOUNT_NO"];
        }

        $app->sendSuccessJSON("Successful", $account_no);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
