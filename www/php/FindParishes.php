<?php

require './base/app-util-base.php';

$app = new AppUtil();

class ParishList {

    public $parish_sn = array();
    public $parish_names = array();

    public function __construct() {
        
    }

}

findParishs($app);

function findParishs($app) {

    $national = $app->getInputPOST('assign-to-parish-under-national');
    $region = $app->getInputPOST('assign-to-parish-under-region');
    $province = $app->getInputPOST('assign-to-parish-under-province');
    $zone = $app->getInputPOST('assign-to-parish-under-zone');
    $area = $app->getInputPOST('assign-to-parish-under-area');

    if ($national === FALSE || $region === FALSE || $province === FALSE || $zone === FALSE || $area === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    try {
        $stmt = $app->conn->prepare("SELECT PARISH_SN, PARISH_NAME "
                . " FROM "
                . " parish_register"
                . " WHERE "
                . " UNDER_AREA =? "
                . " AND"
                . " UNDER_ZONE =? "
                . " AND"
                . " UNDER_PROVINCE =? "
                . " AND"
                . " UNDER_REGION =? "
                . " AND"
                . " UNDER_NATIONAL =? "
                . " AND"
                . " PARISH_NAME !='' "
                . " AND"
                . " PARISH_NAME != 'NULL'");


        $stmt->execute(array($area, $zone, $province, $region, $national));

        $parish_names = array();
        $index = -1;
        $p = new ParishList();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $p->parish_sn[$index] = $row["PARISH_SN"];
            $p->parish_names[$index] = $row["PARISH_NAME"];
        }
        $stmt->closeCursor();
        
        $app->sendSuccessJSON("Successful!", $p);
    } catch (Exception $exc) {
        $app->sendErrorJSON("Please try again later! ");
    }
}
