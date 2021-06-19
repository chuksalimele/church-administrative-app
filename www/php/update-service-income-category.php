<?php

require './base/app-util-base.php';

$app = new AppUtil();

updateServiceIncomeCategory($app);

function updateServiceIncomeCategory($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $category = $app->getInputPOST('SERVICE_INCOME_CATEGORY');
    $sn = $app->getInputPOST('SN');
    
    if ( $sn === FALSE || $category === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }
    try {


        $stmt = $app->sqlUpdate("add_service_income_category", "SERVICE_INCOME_CATEGORY=?", "SN=? AND ENTRY_USER_ID=?", array($category, $sn, $app->userSession->getSessionUsername()));

        if ($stmt->rowCount() > 0) {
            $app->sendSuccessJSON("Service income category updated successfully!", null);
        } else {
                //check if the reason is because the user was not the one who added the record in the first place.
                
                if ($app->checkAuthorizedOperation("add_service_income_category", "SN", $sn)) {
                    $app->sendIgnoreJSON("Nothing updated!");
                } else {
                    $app->sendUnauthorizedOperationJSON("You cannot update a record that does not originate from you!");
                }
        }
        $stmt->closeCursor();
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
    }

}
