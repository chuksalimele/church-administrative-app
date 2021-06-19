<?php

require './base/app-util-base.php';

$app = new AppUtil();

getBankAccountNameList($app);

function getBankAccountNameList($app) {

    try {

        $stmt = $app->conn->prepare("SELECT ACCOUNT_NAME "
                . " FROM "
                . " add_bank_account ");

        $stmt->execute(array());

        $account_names = array();
        $index = -1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $account_names[$index] = $row["ACCOUNT_NAME"];
        }

        $app->sendSuccessJSON("Successful", $account_names);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
