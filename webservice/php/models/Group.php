<?php

    
    class Group extends Model
    {
    protected $name;
        protected $super;
        protected $additional;
        protected $created;


        protected $subgroups;

        protected $articles;
        
        public function initWithSQLRow($SQLRow, $idsOnly = 0){
    
                $this->id          = isset($SQLRow['id'])         ? $SQLRow['id']         : NULL;
                $this->id          = isset($SQLRow['groupID'])    ? $SQLRow['groupID']    : $this->id;
                $this->name        = isset($SQLRow['name'])       ? $SQLRow['name']       : NULL;
                $this->additional  = isset($SQLRow['additional']) ? $SQLRow['additional'] : NULL;
                $this->super       = (isset($SQLRow['super']) && $SQLRow['super'] > 0) ? GroupFactory::LoadWithID($SQLRow['super'],0) : NULL;
                $this->created     = isset($SQLRow['created'])    ? $SQLRow['created']    : NULL;
                
                
        }
        
        
        public function name(){return ucfirst($this->name);}
        public function super(){return $this->super;}
        public function additional(){return $this->additional;}
        public function created(){return $this->created;}
        
        public function setName($name){$this->name = $name;}
        public function setSuper($super){$this->super = $super;}
        public function setSubgroups($subgroups){$this->subgroups = $subgroups;}
        public function setArticles($articles){$this->articles = $articles;}
        
        
        public function fullNameReversed(){
            if(isset($this->super)){
                
                return ucfirst($this->name).'-'.$this->super->fullNameReversed();
                
            }else{
                
                return ucfirst($this->name);
            }

            
            
        }  
                
        public function fullName(){
            
            if(isset($this->super)){
                
                return $this->super->fullName().'-'.ucfirst($this->name);
                
            }else{
                
                return ucfirst($this->name);
            }
        }
        
        public function SQLFields(){
            
            return array(
            $this->id,$this->name,$this->super,$this->additional
           );
            
        }
        
  
        

        
        public function articles($idsOnly = 0){
            
            if(!isset($this->articles)){
                
                $this->loadArticles($idsOnly);
                
            }
            
            return $this->articles;
            
            
        }
        
        private function loadArticles($idsOnly = 0){
            
            $this->articles = ArticleFactory::LoadArticlesForGroup($this,$idsOnly);
            
                if(count($this->articles) < 1){
             
             $this->articles = NULL;
             
         }
            
        }
  
        public function subgroups($idsOnly = 0){
    
            if(!isset($this->subgroups)){
                
                $this->loadSubgroups($idsOnly); 
            }
            
            return $this->subgroups;      
        }
  
        private function loadSubgroups($idsOnly = 0)
        {
         
         $this->subgroups = GroupFactory::FindSubgroupsOfGroup($this,0);  
         
         if(count($this->subgroups) < 1){
             
             $this->subgroups = NULL;
             
         }
    
    
        }
  
    }
?>