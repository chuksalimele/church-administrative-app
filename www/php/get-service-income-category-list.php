<?php

require './base/app-util-base.php';

$app = new AppUtil();

qetServiceIncomeCategorys($app);

function qetServiceIncomeCategorys($app) {

    try {

        $stmt = $app->conn->prepare("SELECT SERVICE_INCOME_CATEGORY "
                . " FROM "
                . " add_service_income_category ");

        $stmt->execute(array());

        $category = array();
        $index = -1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $category[$index] = $row["SERVICE_INCOME_CATEGORY"];
        }

        $app->sendSuccessJSON("Successful", $category);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
