<?php

class Validator {

    public $ValidationErrors;
    public $isValidationError = false;

    public function validateParameter($fieldName, $value, $dataType, $required = true, $password = false) {

        if($required == true && empty($value) == true){
            array_push($this->ValidationErrors,"paramaters missing ");
            $isValidationError = true;
            //$this->response(403,"paramaters missing ");
        }
        switch($dataType){
            case BOOLEAN:
                if(!is_bool($value)){
                    //$this->response(403, "data type is not valid for " .$fieldName);
                    array_push($this->ValidationErrors,"data typeis not valid for " .$fieldName);
                    $isValidationError = true;
            }
            break;
            case INTEGER:
                if(!is_numeric($value)){
                    //$this->response(403,"data type is not valid for " . $fieldName);
                    array_push($this->ValidationErrors,"data typeis not valid for " .$fieldName);
                    $isValidationError = true;
                }
            break;
            case STRING:
                if(!is_string($value)){
                    //$this->response(403, "data type is not valid for" . $fieldName);
                    array_push($this->ValidationErrors,"data typeis not valid for " .$fieldName);
                    $isValidationError = true;
                }
            break;

            default:
            array_push($ValidationErrors,"data typeis not valid for " .$fieldName);
            $isValidationError = true;
            break;
        }
        
        if($password = true){
            $this->validatePassword($value);
        }

        if($isValidationError){
            $this->response(403, $this->$ValidationErrors);
        }


        return $value;
    }

    public function validatePassword($password){

        if (strlen($password) > 50) {
            $isValidationError = true;
            array_push($this->ValidationErrors,"name needs to be less than 80 characters");
        }

        if( strlen($password ) > 20 ) {
            $isValidationError = true;
            array_push($this->ValidationErrors,"Password too long, needs to be less than 20 characters");
        }

        if( strlen($password ) < 8 ) {
            $isValidationError = true;
            array_push($this->ValidationErrors,"Password too short, need to be more than 5 characters");
        }

    }

    public function response($code,$message){
        http_response_code($code);
        $response = json_encode(['errors' => $this->ValidationErrors ]);
        echo $response;exit;
    }


}