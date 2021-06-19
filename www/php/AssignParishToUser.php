<?php

require './base/app-util-base.php';

$app = new AppUtil();

assignParishToUser($app);

function assignParishToUser($app) {


    $parish_sn = $app->getInputPOST('parish_sn');
    $username = $app->getInputPOST('username');
    $first_name = $app->getInputPOST('first_name');
    $last_name = $app->getInputPOST('last_name');
    $email = $app->getInputPOST('email');

     if ($parish_sn === FALSE
             || $username === FALSE
             || $first_name === FALSE 
             || $last_name === FALSE 
             || $email === FALSE) {
            return $app->sendErrorJSON("Please try again!");
      }
      try {


          $stmt = $app->sqlUpdate("register", "PARISH_SN=?", "USERNAME=?", array($parish_sn, $username));

          if ($stmt->rowCount() > 0) {
              $data = array();
              $data["firstName"] = $first_name;
              $data["lastName"] = $last_name;
              $data["email"] = $email;
              $data["username"] = $username;
              
              $app->sendSuccessJSON("Successfully!", $data);
          } else {
              $app->sendErrorJSON("Could not assign parish! Possibly parish already assigned.");
          }
          $stmt->closeCursor();
      } catch (Exception $exc) {
          $app->sendErrorJSON("Please try again later! ".$exc);
      }

}
