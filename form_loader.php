<?php

class FormLoader{
    private $errors = [];

    private $name;
    private $amount;

    //PUBLIC:
    public function __construct(){
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $this->errors = array();

            $this->LoadValuesFromPost(array("name","amount"));
            $this->Validate();
            $this->ErrorHandling();
        }
    }

    public function GetData(){
        if(count($this->errors) > 0){
            return null;
        }
        else{
            return array("name" => $this->name, "amount" => $this->amount);
        }
    }

    public function GetErrors(){
        return $this->errors;
    }

    //PRIVATE:
    private function LoadValuesFromPost($fields){
        foreach ($fields as $field) {
            if(isset($_POST[$field])){
                $this->$field = $_POST[$field];
            }
        }
    }

    private function Validate(){
        $this->Required(array("name", "amount"));
        $this->CheckRange("amount", 1);
        $this->MaxLength(array("name"=>100));
        $this->CheckFilter("amount", FILTER_VALIDATE_INT);
    }
    //VALIDATION METHODS
    private function Required($fields){
        foreach ($fields as $field) {
            //field not present in form -> error
            if(!isset($this->$field)){
                array_push($this->errors, $field." required (not set)");
                continue;
            }
            //field is empty -> error
            if($this->$field === ""){
                array_push($this->errors, $field." required (empty)");
            }
        }
        
    }

    private function MaxLength($fields){
        foreach ($fields as $field => $maxLen) {
            //if !isset, error is already recorded
            if(!isset($this->$field)){
                continue;
            }
            //if length > maxLength -> error
            if(strlen($this->$field) > $maxLen){
                array_push($this->errors, $field." too long");
            }
        }
    }

    private function CheckRange($field, $min=null, $max=null){
        //if !isset, error is already recorded
        if(!isset($this->$field)){
            return;
        }
        //if !filter -> error
        if($min !== null && $this->$field < $min){
            array_push($this->errors, $field." to low");
        }
        if($max !== null && $this->$field > $max){
            array_push($this->errors, $field." to high");
        }
    } 

    private function CheckFilter($field, $filter){
        //if !isset, error is already recorded
        if(!isset($this->$field)){
            return;
        }
        //if !filter -> error
        if(!filter_var($this->$field, $filter)){
            array_push($this->errors, $field." not number");
        }
    } 
    //-----------------
    private function ErrorHandling(){
        $errors = array_unique($this->errors);
    }
}

$formLoader = new FormLoader();

