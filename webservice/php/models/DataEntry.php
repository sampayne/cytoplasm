<?php
     class DataEntry extends Model {
        
          protected $sensorID;
          protected $transmitterID;
          protected $taxonomyID;
          protected $reading_date;
          protected $hidden;
          protected $userID;
          protected $entryValues;
          protected $created;
          
          protected $articleID;
                    
          public function initWithSQLRow($SQLRow) {
               
               $this->id          = isset($SQLRow['id']) ? $SQLRow['id'] : NULL;
                $this->sensorID = isset($SQLRow['sensorID']) ? $SQLRow['sensorID'] : NULL;
               $this->transmitterID = isset($SQLRow['transmitterID']) ? $SQLRow['transmitterID'] : NULL;
               $this->taxonomyID = isset($SQLRow['taxonomyID']) ? $SQLRow['taxonomyID'] : NULL;
               $this->reading_date = isset($SQLRow['reading_date']) ? $SQLRow['reading_date'] : NULL;
               $this->hidden= isset($SQLRow['hidden']) ? $SQLRow['hidden'] : NULL;
               $this->userID = isset($SQLRow['userID']) ? $SQLRow['userID'] : NULL;
               $this->entryValues = isset($SQLRow['entryValues']) ? $SQLRow['entryValues'] : NULL;
               $this->created = isset($SQLRow['created']) ? $SQLRow['created'] : NULL;
               
               if (isset($SQLRow['key'])) {
                    $newData = '';
                    foreach ($this->data as $char) {
                         if (is_numeric($char)) {
                              $char = strpos($SQLRow['key'], $char);
                         }
                         $newData .= $char;
                    }
               }
          }
          
          public function SQLFields(){
              return array(
              $this->id,
              $this->sensorID,
              $this->transmitterID,
              $this->taxonomyID,
              $this->reading_date,
              $this->hidden,
              $this->userID,
              $this->entryValues
          );
              
              
              
              
              
          }
     
          public function reading_date(){return $this->reading_date;}
          public function entryValues(){return $this->entryValues;}
     
     
     
     }
     
     
     
?>