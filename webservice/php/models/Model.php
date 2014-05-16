<?php
     abstract class Model {
          protected $id;
          public function json() {
               $json = array();
               foreach ($this as $key => $value) {
                    if (isset($value)) {
                         $json[$key] = $value;
                    }
               }
               return $json;
          }
          public abstract function initWithSQLRow($SQLRow);
         
          public abstract function SQLFields();
         
          public function id() {
               return $this->id;
          }
          public function setID($id) {
               $this->id = $id;
          }
     }
     
     
?>