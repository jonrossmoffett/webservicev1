<?php
include_once('constants.php');

class Validator {

    public $ValidationErrors = [];
    public $isValidationError = false;

    public function validateParameter($fieldName, $value, $dataType, $max = 0, $min = 0, $required = true) {

        if($required == true && empty($value) == true){
            array_push($this->ValidationErrors,"paramaters missing ");
            $this->isValidationError = true;
            $this->response(200, $this->ValidationErrors);
            //$this->response(403,"paramaters missing ");
        }
        switch($dataType){
            case BOOLEAN:
                if(!is_bool($value)){
                    array_push($this->ValidationErrors,"data typeis not valid for " .$fieldName);
                    $this->isValidationError = true;
            }
            break;
            case INTEGER:
                if(!is_numeric($value)){
                    array_push($this->ValidationErrors,"data type is not valid for " .$fieldName);
                    $this->isValidationError = true;
                }
            break;
            case STRING:
                if(!is_string($value)){
                    array_push($this->ValidationErrors,"data type is not valid for " .$fieldName);
                    $this->isValidationError = true;
                }
            break;
            case PASSWORD:
                    $this->validatePassword($value);
                    if(!is_string($value)){
                        array_push($this->ValidationErrors,"data type is not valid for " .$fieldName);
                        $this->isValidationError = true;
                    }
            break;
            case EMAIL:
                $this->validateEmail($value);
                if(!is_string($value)){
                    array_push($this->ValidationErrors,"data type is not valid for " .$fieldName);
                    $this->isValidationError = true;
                }
            break;

            default:
            array_push($this->ValidationErrors,"data typeis not valid for " .$fieldName);
            $this->isValidationError = true;
            break;
        }

        if ($min !== 0 && $max !== 0){
            if(strlen($value) > $max){
                array_push($this->ValidationErrors,"Max length for field " .$fieldName . " is: " . $max);
                $this->isValidationError = true;
            }
    
            if(strlen($value) < $min){
                array_push($this->ValidationErrors,"Min length for field " .$fieldName . " is: " . $min);
                $this->isValidationError = true;
            }
        }

        if ($min > 0 && $max == 0){
            if(strlen($value) < $min){
                array_push($this->ValidationErrors,"Min length for field " .$fieldName . " is: " . $min);
                $this->isValidationError = true;
            }
        }

        if ($min == 0 && $max > 0){
            if(strlen($value) < $min){
                array_push($this->ValidationErrors,"Max length for field " .$fieldName . " is: " . $min);
                $this->isValidationError = true;
            }
        }
    
        if($this->isValidationError == true){
            $this->response(400, $this->ValidationErrors);
        }


        return $value;
    }

    public function validatePassword($password){

        if( !preg_match("#[0-9]+#", $password ) ) {
            $this->isValidationError = true;
            array_push($this->ValidationErrors,"Password must include at least one number!");
        }

        if( !preg_match("#[a-z]+#", $password ) ) {
            $this->isValidationError = true;
            array_push($this->ValidationErrors, "Password must include at least one letter!");
        }

    }

    public function validateEmail($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($this->ValidationErrors,"Email is invalid ");
            $this->isValidationError = true;
         }
    }

    public function response($code,$message){
        http_response_code($code);
        $response = json_encode(['errors' => $message]);
        echo $response;exit;
    }

    public function validateRequestType($requestType){

        if($_SERVER['REQUEST_METHOD'] !== $requestType){
            array_push($this->ValidationErrors,"Request type is not ". $requestType );
            $this->isValidationError = true;
        }
        
        if($this->isValidationError == true){
            $this->response(200, $this->ValidationErrors);
        }

    }


}