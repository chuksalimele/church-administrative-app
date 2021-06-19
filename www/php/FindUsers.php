<?php

require './base/app-util-base.php';

$app = new AppUtil();

findUsers($app);

function findUsers($app) {

    $name_of_user = $app->getInputPOST('name_of_user');
    $search_limit = $app->getInputPOST('search_limit');
    $next_search = $app->getInputPOST('next_search');

    //$name_of_user = 'ss';//testing!!!
    //$search_limit = 19;//testing!!!
    //$next_search = 1;//testing!!!

    if ($name_of_user === FALSE || $search_limit === FALSE || $next_search === FALSE) {
        return $app->sendErrorJSON("Please try again!");
    }

    $nth_pos = ($next_search - 1) * $search_limit; //ok

    $name_of_user = str_replace("  ", " ", $name_of_user);

    $name_split = explode(" ", $name_of_user);

    try {
        $stmt = $app->conn->prepare("SELECT"
                . " * "
                . " FROM "
                . " register "
                . " WHERE "
                . " (USERNAME LIKE '%$name_of_user%' "
                . " OR "
                . " FIRST_NAME LIKE '%$name_of_user%' "
                . " OR "
                . " LAST_NAME LIKE '%$name_of_user%' "
                . ((count($name_split) > 1) ?
                        (" OR FIRST_NAME LIKE '%" . $name_split[1] . "%'"
                        . " OR LAST_NAME LIKE '%" . $name_split[1] . "%'") : ""
                ) . " OR "
                . " MIDDLE_NAME LIKE '%$name_of_user%' )"
                . " AND"
                . " PARISH_SN = ? "
                . " LIMIT $nth_pos, $search_limit");

        //$app->userSession->getSessionUserParishID()

        $stmt->execute(array($app->userSession->getSessionUserParishID()));

        $found_users_arr = array();
        $index = -1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $users = array();
            $users["username"] = $row["USERNAME"];
            $users["firstName"] = $row["FIRST_NAME"];
            $users["middleName"] = $row["MIDDLE_NAME"];
            $users["lastName"] = $row["LAST_NAME"];
            $users["dob"] = $row["DOB"];
            $users["birthday"] = $row["BIRTHDAY"];
            $users["ageRange"] = $row["AGE_RANGE"];
            $users["sex"] = $row["SEX"];
            $users["address"] = $row["ADDRESS"];
            $users["phoneNumbers"] = $row["PHONE_NUMBERS"];
            $users["email"] = $row["EMAIL"];
            $users["nationality"] = $row["NATIONALITY"];
            $users["dateConverted"] = $row["DATE_CONVERTED"];
            $users["membershipDate"] = $row["MEMBERSHIP_DATE"];
            $users["profilePhotoUrl"] = $row["PROFILE_PHOTO"];
            $users["designation"] = $row["DESIGNATION"];
            $users["department"] = $row["DEPARTMENT"];
            $users["blockedAccount"] = $row["BLOCKED_ACCOUNT"];
            $users["userGroups"] = $row["USER_GROUPS"];
            $users["role"] = $row["ROLE"];
            $users["uneditableFeaturesJson"] = $row["UNEDITABLE_FEATURES_JSON"];
            $users["unviewableFeaturesJson"] = $row["UNVIEWABLE_FEATURES_JSON"];

            $found_users_arr[$index] = $users;
        }

        $stmt->closeCursor();

        $app->sendSuccessJSON("Successful!", $found_users_arr);
    } catch (Exception $exc) {
        $app->sendErrorJSON("Please try again later! " . $exc);
    }
}
