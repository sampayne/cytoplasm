<?php class Taxonomy extends Model {
    
        protected $name;
        protected $super;
        protected $created;
        
           public function initWithSQLRow($SQLRow){
               
                $this->id                = isset($SQLRow['id'])?  $SQLRow['id'] : NULL;
                $this->name              = isset($SQLRow['name'])? $SQLRow['name'] : NULL   ;
                $this->super             = isset($SQLRow['super']) && $SQLRow['super'] > 0 ? TaxonomyFactory::LoadWithID($SQLRow['super'],0) : NULL;
                $this->created           = isset($SQLRow['created'])? $SQLRow['created'] : NULL;
        

               
           }
         
          public function SQLFields(){ 
          
          return array($this->id, $this->name, $this->super, $this->created);
              
          }
          
          public function fullname(){
              
              
              if(isset($this->super)){
              return $this->super->fullName().'-'.ucfirst($this->name);
              
              }else{
                  
                  return ucfirst($this->name);
                  
                  
                  
              }
              
              
          }
          public function name(){return ucfirst($this->name);}
          public function super(){return $this->super;}
          public function created(){return $this->created;}
          
    
}?>