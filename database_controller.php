<?php
require './form_loader.php';

class DatabaseAccess{
    private $connection;

    function __construct()
    {
        require 'config/db_config.php';
        $database = "mysql:host=".$db_config["server"].";dbname=".$db_config["database"];
        $user = $db_config["login"];
        $password = $db_config["password"];
        $this->connection = new PDO($database, $user, $password);
    }

    function GetTableData(){
        $query = $this->connection->query("SELECT * FROM list LEFT JOIN items ON list.item_id = items.id ORDER BY position DESC, list.id DESC");
        $data = $query->fetchAll();
        return $data;
    }

    function GetSuggestionData(){
        $query = $this->connection->query("SELECT name FROM items");
        $data = $query->fetchAll();
        return $data;
    }

    function InsertNewItem(){
        //validate inputs
        $formLoader = new FormLoader(); 
        $data = $formLoader->GetData();
        if($data === null){
            $errors = $formLoader->GetErrors();
            //send message to error handler
            return "error";
            //return $errors;
        }
        //sanitize
        $cleanData = Sanitize($data);
        //check if item already in DB
        $query = $this->connection->prepare("SELECT * FROM items LEFT JOIN list ON items.id = list.item_id WHERE name = ?");
        $query->execute(array($cleanData["name"]));
        $result = $query->fetchAll(); 
        
        //0 records -> create item and then record in list
        //1 record:
        //item_id === null -> new record in list
        //item_id !== null -> already in list (=error)
        
        //SELECT * FROM items LEFT JOIN list ON items.id = list.item_id WHERE name = "existing item & in list";           --> 1 record + item_id not NULL
        //SELECT * FROM items LEFT JOIN list ON items.id = list.item_id WHERE name = "existing item & not in list";       --> 1 record + item_id NULL
        //SELECT * FROM items LEFT JOIN list ON items.id = list.item_id WHERE name = "non existing item";                 --> 0 records
        


        if(count($result) === 0){
            //create new item and add with newID
            //create item
            $newItemQuery = $this->connection->prepare("INSERT INTO items (name) VALUES (?);");
            $newItemQuery->execute(array($cleanData["name"]));
            $insertItemResult = $newItemQuery->fetchAll(); 
            $itemId = $this->connection->lastInsertId();
        }
        else{
            if(isset($result[0]["item_id"])){
                //already in list (=error)
                return "error";
            }
            else{
                //new record in list
                //return $result;
                $itemId = $result[0][0];
            }
        }
        $insertIntoListQuery = $this->connection->prepare("
        SELECT MAX(position) INTO @maxPos FROM list;
        INSERT INTO list (item_id, amount, position) VALUES (?, ?, (@maxPos+1));
        "); 
        $insertIntoListQuery->execute(array($itemId, $cleanData["amount"]));
        $insertListResult = $insertIntoListQuery->fetchAll();
        return "success";

    }

    function EditAmount(){
        /*UPDATE list SET amount=? WHERE item_id=?*/
        //get id from url
        //validate if number
        if(isset($_POST['item_id'])&&isset($_POST['amount'])){
            $item_id = $_POST['item_id'];
            $amount = $_POST['amount'];
        }
        else{
            //error
            return "error";
        }

        if (!filter_var($item_id, FILTER_VALIDATE_INT)
            || !filter_var(
                            $amount,
                            FILTER_VALIDATE_INT,
                            array(
                                "options" => array("min_range"=>1)
                            )
                )
        ) {
            //error
            return "error";
        }

        $editQuery = $this->connection->prepare("UPDATE list SET amount=? WHERE item_id=?");
        $editQuery->execute(array($amount,$item_id));
        $result = $editQuery->fetchAll(); 
        return $result;
    }

    function Swap(){
        /*
        SELECT position INTO @pos1 FROM list WHERE item_id = 5;
        SELECT position INTO @pos2 FROM list WHERE item_id = 27;

        UPDATE list SET position = @pos1 WHERE item_id = 27;
        UPDATE list SET position = @pos2 WHERE item_id = 5 ;
        */ 
        if(isset($_POST["item_id_one"]) && isset($_POST["item_id_two"])){
            $item_id_one = $_POST["item_id_one"];
            $item_id_two = $_POST["item_id_two"];
        }

        if (!filter_var($item_id_one, FILTER_VALIDATE_INT) || !filter_var($item_id_two, FILTER_VALIDATE_INT)) {
            //error
            return "error";
        }

        $swapQuery = $this->connection->prepare("
        SELECT position INTO @pos1 FROM list WHERE item_id = ?;
        SELECT position INTO @pos2 FROM list WHERE item_id = ?;
        UPDATE list SET position = @pos1 WHERE item_id = ?;
        UPDATE list SET position = @pos2 WHERE item_id = ?;
        ");

        $swapQuery->execute(array($item_id_one,$item_id_two,$item_id_two,$item_id_one));
    }

    function DeleteItemFromList(){
        /*DELETE FROM `list` WHERE `list`.`id` = 14"?*/
        //get id from url
        //validate if number
        if(isset($_GET['id'])){
            $id = $_GET['id'];
        }
        else{
            //error
            return;
        }

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            //error
            return;
        }    

        $deleteQuery = $this->connection->prepare("DELETE FROM list WHERE item_id = ?");
        $deleteQuery->execute(array($id));
        $result = $deleteQuery->fetchAll(); 
        return $result; 
    }
}


function Sanitize($array){
    $cleanData = array();
    foreach ($array as $key => $string) {
        $string = strip_tags($string);
        $string = trim($string);
        $string = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $string);
        $cleanData[$key] = $string;
    }
    
    return $cleanData;
}