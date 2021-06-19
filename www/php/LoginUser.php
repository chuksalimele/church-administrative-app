<?php

require './base/app-util-base.php';

$app = new AppUtil();

loginUser($app);

function loginUser($app) {

    $username = $app->getInputPOST('txt_login_username');
    $password = $app->getInputPOST('txt_login_password');
    $group = $app->getInputPOST('cbo_login_group');
    $role = $app->getInputPOST('cbo_login_role');

    if ($username === FALSE || $password === FALSE || $group === FALSE || $role === FALSE) {
        return $app->sendErrorJSON("Could not login.\nPlease try again!");
    }

    try {

        $stmt = $app->sqlSelect("register", "*", "BINARY USERNAME =? AND BINARY PASSWORD=SHA('$password')", array($username));


        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User();

            $parish_sn = $row["PARISH_SN"];

            $stmt2 = $app->sqlSelect("parish_register", "*", "PARISH_SN =?", array($parish_sn));

            if ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $user->parishName = $row2["PARISH_NAME"];
                $user->parishSuperAdmin = $row2["PARISH_SUPER_ADMIN"];
                $user->parishAddress = $row2["PARISH_ADDRESS"];
                $user->parishLogitutude = $row2["LONGITUDE"];
                $user->parishLatitude = $row2["LATITUDE"];
                $user->area = $row2["UNDER_AREA"];
                $user->zone = $row2["UNDER_ZONE"];
                $user->province = $row2["UNDER_PROVINCE"];
                $user->region = $row2["UNDER_REGION"];
                $user->national = $row2["UNDER_NATIONAL"];
            }

            //first check user priveledge
            
            if ($user->parishSuperAdmin === $username
                    && $group === "Super Admin") {
                //here it is the parish super admin loging in with the Super Admin privilege
                
            } else {
                $group_arr = explode(',', $row["USER_GROUPS"]);
                if (!checkUserPriveledge($group_arr, $row["ROLE"], $group, $role)) {
                    $app->sendErrorJSON("Invalid access level!");
                    return;
                }
            }


            $user->blockedAccount = $row["BLOCKED_ACCOUNT"];

            //check if the account is blocked
            if ($user->blockedAccount != '0') {
                $app->sendErrorJSON("Your account is blocked! Please contact your parish administrator.");
                return;
            }

            $user->username = $row["USERNAME"];
            $user->firstName = $row["FIRST_NAME"];
            $user->lastName = $row["LAST_NAME"];
            $user->middleName = $row["MIDDLE_NAME"];
            $user->dob = $row["DOB"];
            $user->birthday = $row["BIRTHDAY"];
            $user->ageRange = $row["AGE_RANGE"];
            $user->sex = $row["SEX"];
            $user->address = $row["ADDRESS"];
            $user->email = $row["EMAIL"];
            $user->verifiedEmail = $row["VERIFIED_EMAIL"];
            $user->phoneNumbers = $row["PHONE_NUMBERS"];
            $user->dateConverted = $row["DATE_CONVERTED"];
            $user->membershipDate = $row["MEMBERSHIP_DATE"];
            $user->photoUrl = $row["PROFILE_PHOTO"];
            $user->designation = $row["DESIGNATION"];
            $user->dept = $row["DEPARTMENT"];
            $user->group = $group;
            $user->role = $row["ROLE"];
            $user->uneditableFeaturesJson = $row["UNEDITABLE_FEATURES_JSON"];
            $user->unviewableFeaturesJson = $row["UNVIEWABLE_FEATURES_JSON"];

            $data = array();
            $data["user"] = $user;

            $data["carouselImages"] = array(); //come back
            $data["newsContent"] = array(); //come back
            //save the login session
            $app->userSession->setSessionUserParishID($parish_sn);
            $app->userSession->setSessionUserParishSuperAdmin($user->parishSuperAdmin);
            $app->userSession->setSessionUsername($user->username);
            $app->userSession->setSessionUserHashPassword($row["PASSWORD"]);
            $app->userSession->setSessionUserGroup($user->group);
            $app->userSession->setSessionUserRole($user->role);

            $app->sendSuccessJSON("Successfully!", $data);
        } else {
            $app->sendErrorJSON("Invalid username or pasword!");
        }
    } catch (Exception $e) {
        $app->sendErrorJSON("Please try again later!" . $e);
    }
}

function checkUserPriveledge($user_store_groups_arr, $user_stored_role, $group, $role) {
    $len = count($user_store_groups_arr);

    for ($i = 0; $i < $len; $i++) {

        if ($user_store_groups_arr[$i] == $group && $user_stored_role == $role) {
            return true;
        }
    }

    return false;
}
