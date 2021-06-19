<?php

require './base/app-util-base.php';

$app = new AppUtil();

addDesignation($app);

function addDesignation($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $designation = $app->getInputPOST('designation');

    if ($designation === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    //first check if the designation is already added

    try {

        $designation = strtoupper($designation); // convert bank name to upper case for purpose of uniformity

        $stmt = $app->sqlSelect("add_designation", "DESIGNATION", "DESIGNATION =?", array($designation));

        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Designation '" . $designation . "' already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt2 = $app->sqlInsert("add_designation", "SN, DESIGNATION, ENTRY_USER_ID, ENTRY_DATETIME", "0,?,?,now()", array($designation, $app->userSession->getSessionUsername()));

        if ($stmt2->rowCount() > 0) {
            $app->sendSuccessJSON("Designation added successfully!", null);
            $stmt2->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}