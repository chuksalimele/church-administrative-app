<?php

require './base/app-util-base.php';

$app = new AppUtil();

qetDepts($app);

function qetDepts($app) {

    try {

         $stmt = $app->conn->prepare("SELECT DEPARTMENT "
                . " FROM "
                . " add_department ");

        $stmt->execute(array());

        $dept = array();
        $index = -1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $dept[$index] = $row["DEPARTMENT"];
        }

        $app->sendSuccessJSON("Successful", $dept);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
}
