<?php

require './base/app-util-base.php';

$app = new AppUtil();

qetBankNames($app);

function qetBankNames($app) {

    try {

        $stmt = $app->conn->prepare("SELECT BANK_NAME "
                . " FROM "
                . " add_bank ");

        $stmt->execute(array());

        $banks = array();
        $index = -1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $banks[$index] = $row["BANK_NAME"];
        }

        $app->sendSuccessJSON("Successful", $banks);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
