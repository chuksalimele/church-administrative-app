<?php

require './base/app-util-base.php';

$app = new AppUtil();

accountReconcile($app);

class TranRecords{
   public $cash_book =null;
   public $bank_statement =null;
   
   public function __construct() {
        
    }
}

function accountReconcile($app) {


    $bank_name = $app->getInputPOST('account_reconcile_bank');
    $account_name = $app->getInputPOST('account_reconcile_account_name');
    $account_no = $app->getInputPOST('account_reconcile_account_no');
    $month_year = $app->getInputPOST('account_reconcile_month_year');

    if ($bank_name === FALSE || $account_name === FALSE || $account_no === FALSE || $month_year === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    $m_arr = explode(" ", $month_year);

    $month = toIntMonth(trim($m_arr[0]));
    $year = trim($m_arr[1]);

    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    $start_date = '01-' . ($month < 10 ? '0' . $month : $month) . '-' . $year;
    $end_date = $num_days . '-' . ($month < 10 ? '0' . $month : $month) . '-' . $year;

    $tranRec = new TranRecords();

    $tranRec->bank = $bank_name;
    $tranRec->account_name = $account_name;
    $tranRec->account_no = $account_no;
    $tranRec->statement_period = $month_year;
    $tranRec->source_columns = getBankStatementSourceColumns($app, $bank_name, $account_name, $account_no, $month, $year, $start_date, $end_date);
    $tranRec->cash_book = getCashBookTableView($app, $bank_name, $account_name, $account_no, $start_date, $end_date);
    $tranRec->bank_statement = getBankStatementTableView($app, $bank_name, $account_name, $account_no, $month, $year, $start_date, $end_date);
    
    if(!$tranRec->cash_book || !$tranRec->bank_statement){
        $app->sendErrorJSON("Please try again!");
    }else{
        $app->sendSuccessJSON("Successful", $tranRec);
    }
}

function getCashBookTableView($app, $bank_name, $account_name, $account_no, $start_date, $end_date) {
    
    try {
        //columns must be in this sequence : TRANDATE, TRANCODE, REMARKS, DEBIT, CREDIT
        
        //NOTE: we are only intrested in the report of the individual parish.
        //So we will ensure we retrieve the cash book of the parish of this user
        //by using sub query 
        
        $stmt = $app->conn->prepare("SELECT TRANDATE, TRANCODE, REMARKS, DEBIT, CREDIT"
                . " FROM "
                . " cash_book "
                . " WHERE"
                . " TRANDATE BETWEEN ? AND ?"
                . " AND "
                . " BANK =?"
                . " AND "
                . " ACCOUNT_NAME =?"
                . " AND "
                . " ACCOUNT_NO =?"
                . " AND "
                . " ENTRY_USER_ID "
                . " IN (SELECT "
                . " FROM register "
                . " WHERE PARISH_SN = ?)");

        $parishID = $app->userSession->getSessionUserParishID();
        
        $stmt->execute(array($start_date, $end_date, $bank_name, $account_name, $account_no, $parishID));
        $table = $app->createDBTableView($stmt);
        return $table;
    } catch (Exception $exc) {
        return false;
    }
}

function getBankStatementTableView($app, $bank_name, $account_name, $account_no, $month, $year, $start_date, $end_date) {
    try {
        
        //columns must be in this sequence : TRANDATE, TRANCODE, REMARKS, DEBIT, CREDIT
        
        $stmt = $app->conn->prepare("SELECT TRANDATE, TRANCODE, REMARKS, DEBIT, CREDIT"
                . " FROM "
                . " bank_statement_records "
                . " INNER JOIN bank_statement_records.RECORD_SN ON bank_statement_info.SN"
                . " WHERE"
                . " TRANDATE BETWEEN ? AND ?"
                . " AND "
                . " BANK =?"
                . " AND "
                . " ACCOUNT_NAME =?"
                . " AND "
                . " ACCOUNT_NO =?"
                . " AND "
                . " ENTRY_USER_ID = ?");

        $userID = $app->userSession->getSessionUsername();
        $stmt->execute(array($start_date, $end_date, $bank_name, $account_name, $account_no, $userID));
        $table = $app->createDBTableView($stmt);
        return $table;
    } catch (Exception $exc) {
        return false;
    }    
}

function getBankStatementSourceColumns($app, $bank_name, $account_name, $account_no, $month, $year, $start_date, $end_date){
        try {
        $stmt = $app->conn->prepare("SELECT SOURCE_COLUMNS "
                . " FROM "
                . " bank_statement_info "
                . " WHERE"
                . " TRANDATE BETWEEN ? AND ?"
                . " AND "
                . " BANK =?"
                . " AND "
                . " ACCOUNT_NAME =?"
                . " AND "
                . " ACCOUNT_NO =?"
                . " AND "
                . " ENTRY_USER_ID = ?");

        $userID = $app->userSession->getSessionUsername();
        $stmt->execute(array($start_date, $end_date, $bank_name, $account_name, $account_no, $userID));
        
        if ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $column_name = "SOURCE_COLUMNS";
            return $row->$column_name; //maps the colum name in result set   
        }        
    } catch (Exception $exc) {
        return false;
    }
}

function toIntMonth($month) {

    switch ($month) {
        case "January":
            return 1;
        case "Jan":
            return 1;
            
        case "February":
            return 2;
        case "Feb":
            return 2;
            
        case "March":
            return 3;
        case "Mar":
            return 3;
            
        case "April":
            return 4;
        case "Apr":
            return 4;
        
        case "May":
            return 5;
            
        case "June":
            return 6;
        case "Jun":
            return 6;
            
        case "July":
            return 7;
        case "Jul":
            return 7;
            
        case "August":
            return 8;    
        case "Aug":
            return 8;
            
        case "September":
            return 9;
        case "Sep":
            return 9;
            
        case "October":
            return 10;
        case "Oct":
            return 10;
            
        case "November":
            return 11;
        case "Nov":
            return 11;
            
        case "December":
            return 12;
        case "Dec":
            return 12;
            
        default:
            return $month;
    }
}
