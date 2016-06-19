<?php

require './base/app-util-base.php';

$app = new AppUtil();

updateExpenseCategory($app);

function updateExpenseCategory($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $category = $app->getInputPOST('EXPENSE_CATEGORY');
    $sn = $app->getInputPOST('SN');
    
    if ( $sn === FALSE || $category === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }
    try {


        $stmt = $app->sqlUpdate("add_expense_category", "EXPENSE_CATEGORY=?", "SN=? AND ENTRY_USER_ID=?", array($category, $sn, $app->userSession->getSessionUsername()));

        if ($stmt->rowCount() > 0) {
            $app->sendSuccessJSON("Expense category updated successfully!", null);
        } else {
                //check if the reason is because the user was not the one who added the bank in the first place.
                //to know that we can check if the bank name exists
                if ($app->checkAuthorizedOperation("add_expense_category", "SN", $sn)) {
                    $this->sendIgnoreJSON("Nothing updated!");
                } else {
                    $this->sendUnauthorizedOperationJSON("You cannot update a record that does not originate from you!");
                }
        }
        $stmt->closeCursor();
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again!");
    }
}
