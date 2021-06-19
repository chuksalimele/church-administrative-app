<?php

require './base/app-util-base.php';

$app = new AppUtil();

$sth = $app->conn->prepare("SELECT * "
                                    . " FROM "
                                    . " test ");

$sth->execute();

print_r($sth->rowCount());

$n = $sth->rowCount();
$result = $sth->fetch(PDO::FETCH_ASSOC);
while ( ($fruit_name = current($result)) !== FALSE ) {
    
    print_r(json_encode(key($result)));
    next($result);//advance to the next element of the array
    print_r("<br/>");
}

