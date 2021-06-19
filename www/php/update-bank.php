<?php

require './base/app-util-base.php';

$app = new AppUtil();

updateBank($app);

function updateBank($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $bank_name = $app->getInputPOST('BANK_NAME');
    $sn = $app->getInputPOST('SN');
    
    if ( $sn === FALSE || $bank_name === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    try {
        
        $stmt = $app->sqlUpdate("add_bank", "BANK_NAME=?", "SN=? AND ENTRY_USER_ID=?", array($bank_name, $sn, $app->userSession->getSessionUsername()));

        if ($stmt->rowCount() > 0) {
            $app->sendSuccessJSON("Bank name updated successfully!", null);
        } else {
                //check if the reason is because the user was not the one who added the record in the first place.
                
                if ($app->checkAuthorizedOperation("add_bank", "SN", $sn)) {
                    $app->sendIgnoreJSON("Nothing updated!");
                } else {
                    $app->sendUnauthorizedOperationJSON("You cannot update a record that does not originate from you!");
                }
        }
        $stmt->closeCursor();
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again!" . $exc);
    }
}
