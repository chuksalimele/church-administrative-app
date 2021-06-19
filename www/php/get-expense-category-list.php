<?php

require './base/app-util-base.php';

$app = new AppUtil();

qetExpenseCategorys($app);

function qetExpenseCategorys($app) {

    try {

        $stmt = $app->conn->prepare("SELECT EXPENSE_CATEGORY "
                . " FROM "
                . " add_expense_category ");

        $stmt->execute(array());

        $category = array();
        $index = -1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $category[$index] = $row["EXPENSE_CATEGORY"];
        }

        $app->sendSuccessJSON("Successful", $category);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
