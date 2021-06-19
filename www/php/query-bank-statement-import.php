<?php

require './base/app-util-base.php';

$app = new AppUtil();

queryBankStatements($app);

function queryBankStatements($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }

    try {

        $stmt = $app->conn->prepare("SELECT "
                . " SN, MONTH, YEAR, BANK_NAME,"
                . " ACCOUNT_NAME, ACCOUNT_NO, ENTRY_DATETIME "
                . " FROM "
                . " bank_statement_info "
                . " WHERE "
                . " ENTRY_USER_ID=?"
                . " ORDER BY SN DESC ");


        $stmt->execute(array($app->userSession->getSessionUsername()));
        //customize the table view
        $table = new TableView(array("SN","STATEMENT_PERIOD","ACCOUNT_DETAILS","TIME_IMPORTED"));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row_data = array();
            //SN
            $row_data[count($row_data)] = $row["SN"];//the pointer for tracking the record
            //STATEMENT_PERIOD
            $row_data[count($row_data)] = $app->numericToFullMonth($row["MONTH"]).", ".$row["YEAR"];
            //ACCOUNT_DETAILS
            $row_data[count($row_data)] = $row["BANK_NAME"]."<br/>" 
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
