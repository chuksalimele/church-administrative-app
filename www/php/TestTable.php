<?php

require './base/app-util-base.php';

$app = new AppUtil();

$colums = array("COS_1","COL_2","col_3","col_2","col_3","col_2","col_3","col_2","col_3");

$row1 =   array("a1","a2","a3","a2","a3","a2","a3","a2","a3");
$row2 =   array("b1","b2","b3","a2","a3","a2","a3","a2","a3");
$row3 =   array("c1","c2","c3","a2","a3","a2","a3","a2","a3");

$t = new TableView($colums);

$t->setTitle("This Table Title");

$t->addRowData($row1);
$t->addRowData($row2);
$t->addRowData($row3);

$app->sendSuccessJSON("Successful", $t);

//echo json_encode($t);