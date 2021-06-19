<?php

require './base/app-util-base.php';

$app = new AppUtil();

qetIncomeCategorys($app);

function qetIncomeCategorys($app) {

    try {

        $stmt = $app->conn->prepare("SELECT INCOME_CATEGORY "
                . " FROM "
                . " add_income_category ");

        $stmt->execute(array());

        $category = array();
        $index = -1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $category[$index] = $row["INCOME_CATEGORY"];
        }

        $app->sendSuccessJSON("Successful", $category);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
