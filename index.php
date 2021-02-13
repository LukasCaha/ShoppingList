<?php
require './database_controller.php';

$db = new DatabaseAccess();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    print_r($_POST);
    if($_POST["action"] === "insert"){
        //process request
        $result = $db->InsertNewItem();

        if($result === "success"){
            header("Location: ".$_SERVER['PHP_SELF']."?success=1");
            die;
        }
        if($result === "error"){
            $errorMessage = "something went wrong";
            header("Location: ".$_SERVER['PHP_SELF']."?errorMessage=$errorMessage");
            die;
        }
    }
    if($_POST["action"] === "edit"){
        $result = $db->EditAmount();
        if($result == "error"){
            http_response_code(400); 
            die;
        }
        return "ok";
    }
    if($_POST["action"] === "swap"){
        $result = $db->Swap();
        if($result == "error"){
            http_response_code(400); 
            die;
        }
        return "ok";
    }
}
else if($_SERVER['REQUEST_METHOD'] === 'DELETE')
{
    $db->DeleteItemFromList();
    return 0;
}
else{
    //get shopping list data
    $tableData = $db->GetTableData();

    //get suggestions data
    $suggestionData = $db->GetSuggestionData();

    require './shopping_list.template.php';
}