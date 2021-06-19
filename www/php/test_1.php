<?php

class Divison {

    public $name = null;
    public $age = null;

    public function __construct() {
        
    }

}

require './base/app-util-base.php';

$b = array();
$b["name"] = 'okore';
$b["age"] = 32;

$d = new Divison;
$d->name = 'okora';
$d->age = 33;

//echo json_encode($b).'<br/>';
//echo json_encode($d);

$app = new AppUtil();

json_encode($b);

$stmt = $app->conn->prepare("UPDATE"
        . " test "
        . " SET"
        . " col1=?");

$stmt->execute(array(json_encode($b)));

$stmt = $app->conn->prepare("SELECT"
        . " col1 "
        . " FROM "
        . " test ");

$stmt->execute(array());
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $p = $row["col1"];
            //echo $p;
            echo '<html>'
            . '<script>'
            .'var json = JSON.parse('.json_encode($p).');'
            .'alert(json.name);'        
            . '</script>'
                    . '</html>';
        }
