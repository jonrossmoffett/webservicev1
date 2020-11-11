<?php

class Validator {

    public function validateParameter($fieldName, $value, $dataType, $required = true) {
        if($required == true && empty($value) == true){
            $this->response(403,"paramater is required");
        }
        switch($dataType){
            case BOOLEAN:
                if(!is_bool($value)){
                    $this->response(403, "data type is not valid for " .$fieldName);
            }
            break;
            case INTEGER:
                if(!is_numeric($value)){
                    $this->response(403,"data type is not valid for " . $fieldName);
                }
            break;
            case STRING:
                if(!is_string($value)){
                    $this->response(403, "data type is not valid for" . $fieldName);
                }
            break;

            default:
            $this->response(403, "data type is not valid for" . $fieldName);
            break;
        }	
        return $value;
    }

    public function response($code,$message){
        http_response_code($code);
        $response = json_encode(['response' => $message]);
        echo $response;exit;
    }


}