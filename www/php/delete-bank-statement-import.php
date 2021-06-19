<?php

require './base/app-util-base.php';

$app = new AppUtil();

deleteBankStatements($app);

function deleteBankStatements($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }
    
    $serial_numbers_seperated_by_comma = $app->getInputPOST('selected_serial_nos');

    if($serial_numbers_seperated_by_comma==null
            || $serial_numbers_seperated_by_comma==''){
        return $app->sendIgnoreJSON("Nothing deleted!");
    }
    
    if ($serial_numbers_seperated_by_comma === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }
    
    try {

        $stmt = $app->conn->prepare("DELETE "
                . " FROM "
                . " bank_statement_info "
                . " WHERE "
                . " SN IN('".$serial_numbers_seperated_by_comma."')"
                . " AND "
                . " ENTRY_USER_ID=?");

        $stmt->execute(array($app->userSession->getSessionUsername()));
        
        $app->sendSuccessJSON("Successful", null);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!".$exc);
        //echo $exc->getTraceAsString();
    }
}
