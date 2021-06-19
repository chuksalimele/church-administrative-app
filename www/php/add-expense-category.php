<?php

require './base/app-util-base.php';

$app = new AppUtil();

addExpenseCategory($app);

function addExpenseCategory($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $category = $app->getInputPOST('monetary-add-expense-category');

    if ($category === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    //first check if the expense category is already added

    try {

        $category = strtoupper($category); // convert $category to upper case for purpose of uniformity

        $stmt = $app->sqlSelect("add_expense_category", "EXPENSE_CATEGORY", "EXPENSE_CATEGORY =?", array($category));

        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Expense category '" . $category . "' already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt2 = $app->sqlInsert("add_expense_category", "SN, EXPENSE_CATEGORY, ENTRY_USER_ID, ENTRY_DATETIME", "0,?,?,now()", array($category, $app->userSession->getSessionUsername()));

        if ($stmt2->rowCount() > 0) {
            $app->sendSuccessJSON("Expense category added successfully!", null);
            $stmt2->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}