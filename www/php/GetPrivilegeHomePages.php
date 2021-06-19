<?php

require './base/app-util-base.php';

$app = new AppUtil();
class GroupHomePagePair {

    public $group;
    public $homePage;

    public function __construct($group,  $homePage) {
        $this->group = $group;
        $this->homePage = $homePage;
    }

}
getPrivilegeHomePages($app);

function getPrivilegeHomePages($app) {

    try {

        $home_pages = array();
        $path = "";
        $config = new Config;
        if ($config->server_host == "localhost") {
            //when locally hosted
            $path = $_SERVER["DOCUMENT_ROOT"]."churchmegaapp/www/";
        } else {
            //when internet hosted
            $path = $_SERVER["DOCUMENT_ROOT"];
        }
        
        $admin = file_get_contents($path . "home-page-admin.html");
        $accountant= file_get_contents($path . "home-page-accountant.html");
        $pastor = file_get_contents($path . "home-page-pastor.html");
        $worker = file_get_contents($path . "home-page-worker.html");
        $men_exco = file_get_contents($path . "home-page-men-exco.html");
        $women_exco = file_get_contents($path . "home-page-women-exco.html");
        $youth_exco = file_get_contents($path . "home-page-youth-exco.html");
        $member = file_get_contents($path . "home-page-member.html");
        $children = file_get_contents($path . "home-page-children.html");

        if ($admin === FALSE || $accountant === FALSE || $pastor === FALSE || $worker === FALSE || $men_exco === FALSE || $women_exco === FALSE || $youth_exco === FALSE || $member === FALSE || $children === FALSE) {// see doc for reason of using this '===' operator. we want to be sure of the success or failure of the file operation
            $app->sendErrorJSON("Could not retrieve privileges! Please report this error!");

            return; //fails
        }
        
        $arr = array();
        $arr[count($arr)] = new GroupHomePagePair("admin", $admin);
        $arr[count($arr)] = new GroupHomePagePair("accountant", $accountant);
        $arr[count($arr)] = new GroupHomePagePair("pastor", $pastor);
        $arr[count($arr)] = new GroupHomePagePair("worker", $worker);
        $arr[count($arr)] = new GroupHomePagePair("men_exco", $men_exco);
        $arr[count($arr)] = new GroupHomePagePair("women_exco", $women_exco);
        $arr[count($arr)] = new GroupHomePagePair("youth_exco", $youth_exco);
        $arr[count($arr)] = new GroupHomePagePair("member", $member);
        $arr[count($arr)] = new GroupHomePagePair("children", $children);
        
       $app->sendSuccessJSON("Successful!", $arr);
       
    } catch (Exception $exc) {
        $app->sendErrorJSON("Please try again later! ");
    }
}
