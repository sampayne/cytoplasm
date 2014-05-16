<?php 

class Error{
    
    public $error;
    
    public function __construct($aError = ''){
        
        $this->error = $aError;
        
    }
    
    public function json(){
        
        $json = array();
               foreach ($this as $key => $value) {
                    if (isset($value)) {
                         $json[$key] = $value;
                    }
               }
               return $json;
        
        
    }
    
}?>