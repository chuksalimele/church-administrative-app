<?php

include './base/app-util-base.php';

$app = new AppUtil();

queryBank($app);

function queryBank($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }
    
    try {

        $stmt = $app->conn->prepare("SELECT * "
                . " FROM "
                . " add_bank ");

        $stmt->execute(array());

        $table = $app->createDBTableView($stmt);
        
        $app->sendSuccessJSON("Successful", $table);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again!");
        //echo $exc->getTraceAsString();
    }
}
