<?php

require './base/app-util-base.php';

$app = new AppUtil();

updateIncomeCategory($app);

function updateIncomeCategory($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $category = $app->getInputPOST('INCOME_CATEGORY');
    $sn = $app->getInputPOST('SN');
    
    if ( $sn === FALSE || $category === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }
    try {

        $stmt = $app->sqlUpdate("add_income_category", "INCOME_CATEGORY=?", "SN=? AND ENTRY_USER_ID=?", array($category, $sn, $app->userSession->getSessionUsername()));

        if ($stmt->rowCount() > 0) {
            $app->sendSuccessJSON("Income category updated successfully!", null);
        } else {
                //check if the reason is because the user was not the one who added the record in the first place.
                
                if ($app->checkAuthorizedOperation("add_income_category", "SN", $sn)) {
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
