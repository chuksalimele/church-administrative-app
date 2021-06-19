<?php

require './base/app-util-base.php';

$app = new AppUtil();

addServiceIncomeCategory($app);

function addServiceIncomeCategory($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $category = $app->getInputPOST('monetary-add-service-income-category');

    if ($category === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    //first check if the service income category is already added

    try {

        $category = strtoupper($category); // convert $category to upper case for purpose of uniformity

        $stmt = $app->sqlSelect("add_service_income_category", "SERVICE_INCOME_CATEGORY", "SERVICE_INCOME_CATEGORY =?", array($category));

        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Service income category '" . $category . "' already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt2 = $app->sqlInsert("add_service_income_category", "SN, SERVICE_INCOME_CATEGORY, ENTRY_USER_ID, ENTRY_DATETIME", "0,?,?,now()", array($category, $app->userSession->getSessionUsername()));

        if ($stmt2->rowCount() > 0) {
            $app->sendSuccessJSON("Service income category added successfully!", null);
            $stmt2->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}