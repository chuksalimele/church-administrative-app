<?php

require './base/app-util-base.php';

$app = new AppUtil();

addDenomination($app);

function addDenomination($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $denomination = $app->getInputPOST('monetary-add-denomination');

    if ($denomination === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    //first check if the denomination is already added

    try {

        $stmt = $app->sqlSelect("add_denomination", "DENOMINATION", "DENOMINATION =?", array($denomination));

        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Denomination '" . $denomination . "' already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt2 = $app->sqlInsert("add_denomination", "SN, DENOMINATION, ENTRY_USER_ID, ENTRY_DATETIME", "0,?,?,now()", array($denomination, $app->userSession->getSessionUsername()));

        if ($stmt2->rowCount() > 0) {
            $app->sendSuccessJSON("Denomination added successfully!", null);
            $stmt2->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}