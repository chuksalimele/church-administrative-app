<?php

//------ NetworkMarket class

class NetworkMarket {

    private $config_param = null;
    private $last_error_msg = "";
    private $conn = null;
    private $JSON_EXT = ".json";

    function __construct($config_param) {
        /* if($config_param->db_port==null){
          $config_param->db_port = 3306;//default mysql port
          }
          $port = $config_param->db_port;
          $servername = $config_param->db_host.":".$port;//why not?
         */

        $this->config_param = $config_param;
        $servername = $config_param->db_host;
        $dbname = $config_param->db_name;
        $username = $config_param->db_username;
        $password = $config_param->db_password;
        $this->conn = new PDO("mysql:host=" . $servername . ";dbname=" . $dbname, $username, $password);

        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /* This is the main method used to build the tree nodes begining from the root node.
     * If the node is root then referral id need not to be provided. A value of null or empty
     * string passed as the referral id indicates the node is a root.

     * However if the referral id is provided then a new node is attached to that referral id in the tree.
     *
     */

    public function addNode($user_id, $referral_id) {

        if ($referral_id == null || $referral_id == '') {
            if ($this->createNewTree($user_id)) {
                return true; //root node created successfully for user				
            } else {
                return false;
            }
        }

        //at this point the user will not be at root node.

        if ($this->alreadyAsReferral($user_id)) {
            return false; //error msg already set in the function
        }

        $root_id = $this->getRootIDFromDB($referral_id);
        if ($root_id == null) {
            $this->last_error_msg = "unknown referral - '" . $referral_id . "'"; //unknown because the referral must have a root id atleast.
            return false;
        }

        $root_json_str = $this->getRootJSON($root_id);

        if ($root_json_str == null) {
            return false;
        }

        $json_obj = json_decode($root_json_str);

        $new_root_json_obj = $this->addChild($json_obj, $referral_id, $user_id);

        if (!$new_root_json_obj) {
            return false;
        }

        return $this->updateChildAdded($new_root_json_obj, $root_id, $referral_id, $user_id);
    }

    private function createNewTree($user_id) {

        $root_obj = $this->createRootNode($user_id);

        //first check if the root already exist
        try {
            $stmt = $this->conn->prepare("SELECT "
                    . $this->config_param->column_user_id
                    . " FROM "
                    . $this->config_param->db_table_name
                    . " WHERE "
                    . $this->config_param->column_root_id . " =?");

            $stmt->execute(array($user_id));

            if ($stmt->rowCount() > 0) {
                //here the root already exist so leave
                $this->last_error_msg = $user_id . " already exist as root";
                return false;
            }

            //at this point the root does not exist so update the user.
            //the user becomes the root meaning he will not have referral id assigned.
            $this->conn->beginTransaction();

            $stmt2 = $this->conn->prepare("INSERT INTO "
                    . $this->config_param->db_table_name
                    . "("
                    . $this->config_param->column_root_id . ","
                    . $this->config_param->column_user_id
                    . ")"
                    . " VALUES(?,?)");

            $stmt2->execute(array($user_id, $user_id));

            if ($stmt2->rowCount() > 0) {
                //save the file
                if (!$this->saveRootJSON($root_obj)) {
                    $this->conn->rollback();
                    return false;
                }
            } else {
                $this->conn->rollback();
                return false;
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->last_error_msg = "add root failed - " . $e;
        }

        return true;
    }

    private function alreadyAsReferral($user_id) {

        $stmt = $this->conn->prepare("SELECT "
                . $this->config_param->column_referral_id
                . " FROM "
                . $this->config_param->db_table_name
                . " WHERE "
                . $this->config_param->column_user_id . "=?");

        $stmt->execute(array($user_id));

        if ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $column_name = $this->config_param->column_referral_id;
            $ref_id_found = $row->$column_name; //maps the colum name in result set

            if ($ref_id_found != null && $ref_id_found != '') {
                $this->last_error_msg = $user_id . " already has a referral";
                return true;
            }
        }

        return false;
    }

    private function updateChildAdded($new_root_json_obj, $root_id, $referral_id, $user_id) {

        $success = false;

        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("INSERT INTO "
                    . $this->config_param->db_table_name
                    . "("
                    . $this->config_param->column_root_id . ","
                    . $this->config_param->column_referral_id . ","
                    . $this->config_param->column_user_id
                    . ")"
                    . " VALUES(?,?,?)");

            $stmt->execute(array($root_id, $referral_id, $user_id));

            if ($stmt->rowCount() > 0) {
                if ($this->saveRootJSON($new_root_json_obj)) {
                    $success = true;
                    $this->conn->commit();
                } else {
                    $this->conn->rollback();
                    $success = false;
                }
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->last_error_msg = "add child failed - " . $e;
        }

        return $success;
    }

    private function createPathIfNotExist($file_dir) {
        if (!file_exists($file_dir)) {
            if (!mkdir($file_dir, 0777, true)) {
                return false; //failed				
            }
        }
        return true;
    }

    private function saveRootJSON($new_root_node_obj) {//must be private.
        if (!$this->createPathIfNotExist($this->config_param->trees_json_path)) {
            return false;
        }

        $root_id = $new_root_node_obj->node_id; // root id same as node_id (user_id)
        $str_json = json_encode($new_root_node_obj);
        $file = $this->config_param->trees_json_path . $root_id . $this->JSON_EXT;
        //LOCK_EX flag to prevent anyone else writing to the file at the same time	
        $result = file_put_contents($file, $str_json, LOCK_EX);
        if ($result === FALSE) {// see doc for reason of using this '===' operator. we want to be sure of the success or failure of the file operation
            return false;
        } else {
            return true;
        }
    }

    public function getRootIDFromDB($referral_id) {

        $stmt = $this->conn->prepare("SELECT "
                . $this->config_param->column_root_id
                . " FROM "
                . $this->config_param->db_table_name
                . " WHERE "
                . $this->config_param->column_user_id . " =?");

        $stmt->execute(array($referral_id));

        if ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $column_name = $this->config_param->column_root_id;
            return $row->$column_name; //come back
        }

        return null;
    }

    private function getRootJSON($root_id) {
        $file = $this->config_param->trees_json_path . $root_id . $this->JSON_EXT;
        $json = file_get_contents($file);
        if ($json === FALSE) {// see doc for reason of using this '===' operator. we want to be sure of the success or failure of the file operation
            return null; //fails
        }
        return $json;
    }

    private function createRootNode($user_id) {//used internally only
        $rootNode = new Node($user_id, $user_id);
        return $rootNode;
    }

    private function addChild($root_json_obj, $direct_parent_id, $child_id) {//used internally only
        $this->last_error_msg = "";

        $root_id = $root_json_obj->root_id;

        $level = 0;
        $prev_level_nodes = array($root_json_obj);

        while (true) {

            $level++;
            $level_nodes = array();
            $index = -1;
            $has_next_level = false;
            $prev_level_nodes_count = count($prev_level_nodes);

            for ($i = 0; $i < $prev_level_nodes_count; $i++) {

                if ($prev_level_nodes[$i] == null) {//come back
                    continue;
                }

                if ($prev_level_nodes[$i]->node_id == $direct_parent_id) {
                    $prev_level = level - 1;

                    if ($prev_level_nodes[$i]->firstChild == null) {
                        //but ensure the two children will not be the same
                        if ($prev_level_nodes[$i]->secondChild != null) {
                            if ($prev_level_nodes[$i]->secondChild->node_id == $child_id) {
                                //haha avoid this
                                $this->last_error_msg = "'" . $child_id . "' already added to '" . $direct_parent_id . "'";
                                return false; //just leave
                            }
                        }

                        $prev_level_nodes[$i]->firstChild = new Node($root_id, $child_id);

                        return $root_json_obj;
                    } else if ($prev_level_nodes[$i]->secondChild == null) {

                        //but ensure the two children will not be the same
                        if ($prev_level_nodes[$i]->firstChild != null) {
                            if ($prev_level_nodes[$i]->firstChild->node_id == $child_id) {
                                //haha avoid this
                                $this->last_error_msg = "'" . $child_id . "' already added to '" . $direct_parent_id . "'";
                                return false; //just leave
                            }
                        }

                        $prev_level_nodes[$i]->secondChild = new Node($root_id, $child_id);

                        return $root_json_obj;
                    } else {
                        $this->last_error_msg = "'" . $direct_parent_id . "' already has two direct children";
                        return false;
                    }
                }

                $direct_children_count = $this->directChildrenCount($prev_level_nodes[$i]);

                if ($direct_children_count > 0) {
                    $has_next_level = true;
                }

                $index++;
                $level_nodes[$index] = $prev_level_nodes[$i]->firstChild;
                $index++;
                $level_nodes[$index] = $prev_level_nodes[$i]->secondChild;
            }

            if (!$has_next_level) {
                break;
            }

            $prev_level_nodes = $level_nodes;
        }

        return false;
    }

    /*     * returns a json object representing a node in a tree.
     * This node can even be the root node or a sud node down
     * the tree.
     * NOTE: the first parameter must be the root_id. 
     *
     * returns null if the operation fails - ie could not find
     * a node in the tree of the given root id 
     * or an io operation failure occured
     *
     */

    public function getTree($root_id, $node_id) {
        $result = $this->getRootJSON($root_id);
        if ($result == false)
            return null;

        $json_obj = json_decode($result);

        $node_result = $this->findChildById($json_obj, $node_id);

        if ($node_result == null)
            return null;

        return $node_result->getNode();
    }

    /*     * returns a json encoded string representing a node in a tree.
     * This node can even be the root node or a sud node down
     * the tree.
     * NOTE: the first parameter must be the root_id. 
     *
     * returns null if the operation fails - ie could not find
     * a node in the tree of the given root id 
     * or an io operation failure occured
     *
     */

    public function getTreeJSON($root_id, $node_id) {
        $json = $this->getTree($root_id, $node_id);
        if ($json == null) {
            return null;
        }
        return json_encode($json);
    }

    /**
     * get the first weak link in this tree
     *
     */
    public function findWeakChild($this_node) {

        $level = 0;

        $prev_level_nodes = array($this_node); //Node

        while (true) {

            $level++;

            $level_nodes = array();
            $index = -1;
            $has_next_level = false;
            $prev_level_nodes_count = count($prev_level_nodes);
            for ($i = 0; $i < $prev_level_nodes_count; $i++) {
                if ($prev_level_nodes[$i] == null) {//come back
                    continue;
                }

                $direct_children_count = $this->directChildrenCount($prev_level_nodes[$i]);

                if ($direct_children_count > 0) {
                    $has_next_level = true;
                }

                if ($direct_children_count < 2) {
                    $prev_level = $level - 1;
                    return new WeakNodeDesc($prev_level_nodes[$i]->root_id, $prev_level_nodes[$i]->node_id, $direct_children_count, $prev_level);
                }

                $index++;
                $level_nodes[$index] = $prev_level_nodes[$i]->firstChild;
                $index++;
                $level_nodes[$index] = $prev_level_nodes[$i]->secondChild;
            }

            if (!$has_next_level) {
                break;
            }

            $prev_level_nodes = $level_nodes;
        }

        return null;
    }

    private function directChildrenCount($node) {
        if ($node->firstChild != null && $node->secondChild != null) {
            return 2;
        } else if ($node->firstChild != null) {
            return 1;
        } else if ($node->secondChild != null) {
            return 1;
        } else {
            return 0;
        }
    }

    private function findChildById($parent_node, $child_id) {

        $level = 0;
        $prev_level_nodes = array($parent_node);
        while (true) {

            $level++;
            $level_nodes = array();
            $index = -1;
            $has_next_level = false;
            $prev_level_nodes_count = count($prev_level_nodes);
            for ($i = 0; $i < $prev_level_nodes_count; $i++) {
                if ($prev_level_nodes[$i] == null) {//come back
                    continue;
                }
                if ($prev_level_nodes[$i]->node_id == $child_id) {
                    $prev_level = $level - 1;
                    return new NodeResult($prev_level_nodes[$i], $prev_level);
                }

                $direct_children_count = $this->directChildrenCount($prev_level_nodes[$i]);

                if ($direct_children_count > 0) {
                    $has_next_level = true;
                }

                $index++;
                $level_nodes[$index] = $prev_level_nodes[$i]->firstChild;
                $index++;
                $level_nodes[$index] = $prev_level_nodes[$i]->secondChild;
            }

            if (!$has_next_level) {
                break;
            }

            $prev_level_nodes = $level_nodes;
        }

        return null;
    }

    public function getLastErrorMsg() {
        return $this->last_error_msg;
    }

}

//----------- Node class

class Node {

    var $firstChild = null;
    var $secondChild = null;
    var $node_id = null;
    var $root_id = null;

    function __construct($root_id, $node_id) {
        $this->node_id = $node_id;
        $this->root_id = $root_id;
    }

}

class WeakNodeDesc {

    private $node_id = null;
    private $root_id = null;
    private $direct_children_count = null;
    private $relative_level = null;

    function __construct($root_id, $node_id, $direct_children_count, $relative_level) {
        $this->root_id = $root_id;
        $this->node_id = $node_id;
        $this->direct_children_count = $direct_children_count;
        $this->relative_level = $relative_level;
    }

    public function getNodeID() {
        return $this->node_id;
    }

    public function getRootID() {
        return $this->root_id;
    }

    public function getWeakNodeChildCount() {
        return $this->direct_children_count;
    }

    public function getLevel() {
        return $this->relative_level;
    }

}

//----------- NodeResult class

class NodeResult {

    private $json_node = null;
    private $relative_level = null;

    function __construct($json_node, $relative_level) {
        $this->json_node = $json_node;
        $this->relative_level = $relative_level;
    }

    function getNode() {
        return $this->json_node;
    }

    function getRelativeLevelOnTree() {
        return $this->relative_level;
    }

}

class ConfigParam {

    //the following are the essential parameters to effectively
    //configure the networking market processor.
    var $trees_json_path = null; //directory where all the root trees are located
    var $db_name = null;
    var $db_host = null;
    var $db_port = null;
    var $db_username = null;
    var $db_password = null;
    var $db_table_name = null;
    var $column_user_id = null;
    var $column_referral_id = null;
    var $column_root_id = null;

}

?>