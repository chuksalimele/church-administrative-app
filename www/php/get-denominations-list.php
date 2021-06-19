<?php

require './base/app-util-base.php';

$app = new AppUtil();

qetBankNames($app);

function qetBankNames($app) {

    try {

         $stmt = $app->conn->prepare("SELECT DENOMINATION "
                . " FROM "
                . " add_denomination ");

        $stmt->execute(array());

        $denominations = array();
        $index = -1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $denominations[$index] = $row["DENOMINATION"];
        }

        $app->sendSuccessJSON("Successful", $denominations);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
