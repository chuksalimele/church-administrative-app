<?php

require './base/app-util-base.php';

$app = new AppUtil();

queryCashBookImport($app);

function queryCashBookImport($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    try {

        $stmt = $app->conn->prepare("SELECT "
                . " IDENTIFIER, BANK, ACCOUNT_NAME, "
                . " ACCOUNT_NO, ENTRY_DATETIME "
                . " FROM "
                . " cash_book "
                . " WHERE "
                . " ENTRY_METHOD='IMPORT'"
                . " AND ENTRY_USER_ID=? "
                . " GROUP BY IDENTIFIER"
                . " ORDER BY SN DESC ");


        $stmt->execute(array($app->userSession->getSessionUsername()));

      //customize the table view
        $table = new TableView(array("IDENTIFIER","ACCOUNT_DETAILS","TIME_IMPORTED"));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row_data = array();
            //IDENTIFIER
            $row_data[count($row_data)] = $row["IDENTIFIER"];//the pointer for tracking the record
            //ACCOUNT_DETAILS
            $row_data[count($row_data)] = $row["BANK"]."<br/>" 
                                            .$row["ACCOUNT_NAME"]."<br/>"
                                            .$row["ACCOUNT_NO"];
            //TIME_IMPORTED
            $row_data[count($row_data)] = $row["ENTRY_DATETIME"];
            
            $table->addRowData($row_data);
        }
                
        $app->sendSuccessJSON("Successful", $table);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!");
        //echo $exc->getTraceAsString();
    }
    
}
