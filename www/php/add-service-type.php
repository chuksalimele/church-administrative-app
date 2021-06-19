<?php

require './base/app-util-base.php';

$app = new AppUtil();

addServiceType($app);

function addServiceType($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $service = $app->getInputPOST('service-type');

    if ($service === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    //first check if the service is already added

    try {

        $service = strtoupper($service); // convert bank name to upper case for purpose of uniformity

        $stmt = $app->sqlSelect("add_service_type", "SERVICE", "SERVICE =?", array($service));

        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Service type '" . $service . "' already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt2 = $app->sqlInsert("add_service_type", "SN, SERVICE, ENTRY_USER_ID, ENTRY_DATETIME", "0,?,?,now()", array($service, $app->userSession->getSessionUsername()));

        if ($stmt2->rowCount() > 0) {
            $app->sendSuccessJSON("Service type added successfully!", null);
            $stmt2->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}