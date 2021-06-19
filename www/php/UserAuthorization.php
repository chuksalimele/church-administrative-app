<?php

require './base/app-util-base.php';

$app = new AppUtil();

userAuthorization($app);

function userAuthorization($app) {

    try {

        if (!$app->userSession->isBasicSessionAvailable()) {
            $app->sendSessionNotAvaliableJSON(null); //client side should ask the user to login or redirect the user to login page
            return;
        }
                        
        $username = $app->getInputPOST('username');
        $block_account = $app->getInputPOST('block_account');
        $user_groups = $app->getInputPOST('user_groups');
        $unviewable_features_json = $app->getInputPOST('unviewable_features_json');

        //echo $username . " <-----> ".$block_account 
          //      . " <-----> ".$user_groups 
            //    . " <-----> ".$unviewable_features_json ;//TESTING!!!
        
        //return; //TESTING!!!
        
        if ($username === FALSE 
                || $block_account === FALSE 
                || $user_groups === FALSE 
                || $unviewable_features_json === FALSE 
        ) {
            return $app->sendErrorJSON("Please try again!");
        }


        $stmt = $app->conn->prepare("UPDATE "
                . " register "
                . " SET "
                . " USER_GROUPS = ?"
                . " , "
                . " UNVIEWABLE_FEATURES_JSON = ?"
                . " , "
                . " BLOCKED_ACCOUNT = ?"
                . " , "
                . " ENTRY_USER_ID = ?"
                . " , "
                . " ENTRY_DATETIME = NOW()"
                . " WHERE "
                . " USERNAME = ?");

        $stmt->execute(array($user_groups, $unviewable_features_json, $block_account, $app->userSession->getSessionUsername(), $username));

        if ($stmt->rowCount() > 0) {
            $stmt->closeCursor();
            $app->sendSuccessJSON("Successful!", null);
        } else {
            $app->sendIgnoreJSON("Nothing updated!");
        }
    } catch (Exception $exc) {
        $app->sendErrorJSON("Please try again later!".$exc);
        return;
    }
}
