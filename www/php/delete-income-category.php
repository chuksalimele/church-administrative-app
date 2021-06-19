<?php

require './base/app-util-base.php';

$app = new AppUtil();

deleteIncomeCategory($app);

function deleteIncomeCategory($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $sn = $app->getInputPOST('SN');
    
    if ( $sn === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    try {
        $stmt = $app->sqlDelete("add_income_category", "SN=? AND ENTRY_USER_ID=?", array($sn, $app->userSession->getSessionUsername()));

        if ($stmt->rowCount() > 0) {
            $app->sendSuccessJSON("Income category deleted successfully!", null);
        } else {
            //check if the reason is because the user was not the one who added the record in the first place.


            if ($app->checkAuthorizedOperation("add_income_category", "SN", $sn)) {
                $this->sendIgnoreJSON("Nothing deleted!");
            } else {
                $this->sendUnauthorizedOperationJSON("You cannot delete a record that does not originate from you!");
            }
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
    }

}
