<?php

require './base/app-util-base.php';

$app = new AppUtil();

class Divison {

    public $national = array();
    public $region = array();
    public $province = array();
    public $zone = array();
    public $area = array();

    public function __construct() {
        
    }

}

divisions($app);

function divisions($app) {

    try {
        $stmt = $app->conn->prepare("SELECT * "
                . " FROM "
                . " parish_register"
                . " WHERE "
                . " PARISH_NAME !='' AND PARISH_NAME !='NULL'");

        $stmt->execute(array());
        
        $d = new Divison();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $d->national = addUnique($d->national, $row["UNDER_NATIONAL"]);
            $d->region = addUnique($d->region, $row["UNDER_REGION"]);
            $d->province = addUnique($d->province, $row["UNDER_PROVINCE"]);
            $d->zone = addUnique($d->zone, $row["UNDER_ZONE"]);
            $d->area = addUnique($d->area, $row["UNDER_AREA"]);
        }
        $stmt->closeCursor();
        $app->sendSuccessJSON("Successful!", $d);
    } catch (Exception $exc) {
        $app->sendErrorJSON("Please try again later! ");
    }
}

function addUnique($cont, $c) {
    $t = count($cont);
    for ($i = 0; $i < $t; $i++) {
        if ($cont[$i] == $c) {
            return $cont;
        }
    }

    $cont[$t] = $c;
    return $cont;
}
