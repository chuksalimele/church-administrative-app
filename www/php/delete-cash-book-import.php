<?php

require './base/app-util-base.php';

$app = new AppUtil();

deleteCashBookImport($app);

function deleteCashBookImport($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }
    
    $identifiers_seperated_by_comma = $app->getInputPOST('selected_identifiers');

    if($identifiers_seperated_by_comma==null
            || $identifiers_seperated_by_comma==''){
        return $app->sendIgnoreJSON("Nothing deleted!");
    }
    
    if ($identifiers_seperated_by_comma === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }
    
    try {

        $stmt = $app->conn->prepare("DELETE "
                . " FROM "
                . " cash_book "
                . " WHERE "
                . " IDENTIFIER IN('".$identifiers_seperated_by_comma."')"
                . " AND "
                . " ENTRY_USER_ID=?");

        $stmt->execute(array($app->userSession->getSessionUsername()));
        
        $app->sendSuccessJSON("Successful", null);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again!");
        //echo $exc->getTraceAsString();
    }
}
