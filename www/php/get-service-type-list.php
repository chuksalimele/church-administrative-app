<?php

require './base/app-util-base.php';

$app = new AppUtil();

qetServiceTypes($app);

function qetServiceTypes($app) {

    try {

        $stmt = $app->conn->prepare("SELECT SERVICE "
                . " FROM "
                . " add_service_type ");

        $stmt->execute(array());

        $banks = array();
        $index = -1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $banks[$index] = $row["SERVICE"];
        }

        $app->sendSuccessJSON("Successful", $banks);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
