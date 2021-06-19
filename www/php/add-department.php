<?php

require './base/app-util-base.php';

$app = new AppUtil();

addDepartment($app);

function addDepartment($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    $dept = $app->getInputPOST('department');

    if ($dept === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    //first check if the department is already added

    try {

        $dept = strtoupper($dept); // convert bank name to upper case for purpose of uniformity

        $stmt = $app->sqlSelect("add_department", "DEPARTMENT", "DEPARTMENT =?", array(dept));

        if ($stmt->rowCount() > 0) {
            $app->sendIgnoreJSON("Department '" . $dept . "' already exist!");
            $stmt->closeCursor();
            return;
        }

        $stmt2 = $app->sqlInsert("add_department", "SN, DEPARTMENT, ENTRY_USER_ID, ENTRY_DATETIME", "0,?,?,now()", array($dept, $app->userSession->getSessionUsername()));

        if ($stmt2->rowCount() > 0) {
            $app->sendSuccessJSON("Department added successfully!", null);
            $stmt2->closeCursor();
            return;
        }
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}