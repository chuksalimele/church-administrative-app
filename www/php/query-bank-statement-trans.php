<?php

require './base/app-util-base.php';

$app = new AppUtil();

queryBankStatementTrans($app);

function queryBankStatementTrans($app) {

    if (!$app->userSession->isBasicSessionAvailable()) {
        $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
        return;
    }


    $selected_serial_no = $app->getInputPOST('selected_serial_no');

    if ($selected_serial_no === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    try {

        $stmt = $app->conn->prepare("SELECT"
                . " MONTH, YEAR, BANK_NAME,"
                . " ACCOUNT_NAME, ACCOUNT_NO, BAL_BF, "
                . " TRANDATE, TRANCODE,"
                . " REMARKS, DEBIT, CREDIT, BALANCE "
                . " FROM "
                . " bank_statement_records "
                . " INNER JOIN bank_statement_info"
                . " ON bank_statement_records.RECORD_SN = bank_statement_info.SN"
                . " WHERE "
                . " RECORD_SN=?"
                . " AND "
                . " ENTRY_USER_ID=?");

        $stmt->execute(array($selected_serial_no, $app->userSession->getSessionUsername()));

        //$table = $app->createDBTableView($stmt);
        //customize the table view
        $table = new TableView(array("TRANDATE","TRANCODE","REMARKS","DEBIT","CREDIT", "BALANCE"));
        $arr = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row_data = array();
            
            $arr["bank"] = $row["BANK_NAME"];
            $arr["accountName"] = $row["ACCOUNT_NAME"];
            $arr["accountNo"] = $row["ACCOUNT_NO"];
            $arr["month"] = $app->numericToFullMonth($row["MONTH"]);
            $arr["year"] = $row["YEAR"];
            $arr["balanceBF"] = $row["BAL_BF"];
            
            $row_data[count($row_data)] = $row["TRANDATE"];
            $row_data[count($row_data)] = $row["TRANCODE"];
            $row_data[count($row_data)] = $row["REMARKS"];
            $row_data[count($row_data)] = $row["DEBIT"];
            $row_data[count($row_data)] = $row["CREDIT"];
            $row_data[count($row_data)] = $row["BALANCE"];
            
            $table->addRowData($row_data);
        }
        
        $arr["transactions"] = $table;
        
        $app->sendSuccessJSON("Successful", $arr);
        
    } catch (Exception $exc) {
        return $app->sendErrorJSON("Please try again later!".$exc);
        //echo $exc->getTraceAsString();
    }
}
