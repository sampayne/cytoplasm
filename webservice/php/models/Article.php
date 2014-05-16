<?php

class Article extends Model
{
    // SQL Fields
    protected $name;
    protected $description;
    protected $hidden;
    protected $secure;
    protected $creation_date;
    protected $additional_fields;
    
    //Additonal Fields
    protected $groupCreator;
    protected $userCreator;
    protected $dataStreams;

    //SQL Field Getters 
    public function name(){return $this->name;}
    public function description(){return $this->description;}
    public function additional(){return $this->additional_fields;}
    public function secure(){return $this->secure;}
    public function hidden(){return $this->hidden;}
    
    //SQL Field Setters
    public function setName($name = ''){$this->name = $name;}
    public function setDescription($description = ''){$this->description = $description;}
    public function setAdditionalFields($additionalFields = ''){$this->additional_fields = $additional_fields;}
    public function setSecure($secure){$this->secure = $secure;}
    public function setHidden($hidden){$this->hidden = $hidden;}
    
    //Additional Getters
    public function groupCreator(){
    
    if(!isset($this->groupCreator) && !isset($this->userCreator)){
        
        $this->loadCreator();
    }
    
    

    
    return $this->groupCreator;
    
    }
    public function userCreator(){
    
    return $this->userCreator;
    
    }
    public function dataStreams(){return $this->dataStreams;}
    
    //Additional Setters
    public function setGroupCreator($groupCreator){$this->groupCreator = $groupCreator;}
    public function setUserCreator($userCreator){$this->userCreator = $userCreator;}
    public function setDataStreams($dataStreams){$this->dataStreams = $dataStreams;}
    
    //parent abstract initWithSQLRow override
    public function initWithSQLRow($SQLRow){
        
                $this->id                = isset($SQLRow['id'])?  $SQLRow['id'] : NULL;
                $this->id                = isset($SQLRow['articleID'])?  $SQLRow['articleID'] : $this->id;
                $this->name              = isset($SQLRow['name'])? $SQLRow['name'] : NULL   ;
                $this->description       = isset($SQLRow['description'])? $SQLRow['description'] : NULL;
                $this->hidden            = isset($SQLRow['hidden'])? $SQLRow['hidden'] : NULL;
                $this->secure            = isset($SQLRow['secure'])? $SQLRow['secure'] : NULL;
                $this->creation_date     = isset($SQLRow['creation_date'])? $SQLRow['creation_date'] : NULL;
                $this->additional_fields = isset($SQLRow['additional_fields'])? $SQLRow['additional_fields'] : NULL;
                $this->groupCreator      = isset($SQLRow['groupID'])? GroupFactory::LoadWithID($SQLRow['groupID'],0) : NULL;
                $this->userCreator       = isset($SQLRow['userID'])? UserFactory::LoadWithID($SQLRow['userID'],0) : NULL;
                
    }
    
    public function SQLFields(){
        
        return array(
            $this->id,  
            $this->name,
            $this->description,
            $this->hidden,
            $this->secure,
            $this->additional_fields
        );
        
        
    }

    //Model Behaviour
    private function loadCreator()
    {
        
        $this->userCreator  = ArticleFactory::LoadUserCreatorOfArticle($this);
        $this->groupCreator = ArticleFactory::LoadGroupCreatorOfArticle($this);
        
    }
    
    public function addDataStream($stream){
        
        if(is_null($this->dataStreams)){
            
            $this->dataStreams = array();
        }
    
        array_push($this->dataStreams,$stream);    
    }
    
    public function loadDataStreamForTaxonomy($taxonomy){
        
        if(is_null($this->dataStreams)){
            
            $this->dataStreams = array();
        }
        
        $stream = DataStreamFactory::LoadDataStreamForTaxonomyArticle($taxonomy,$article);    
        
        array_push($this->dataStreams,$stream);
        
        
        }
    
    public function loadAllDataStreams($idsOnly = 0){
              
        $this->dataStreams = DataStreamFactory::LoadAllStreamsForArticle($this,$idsOnly);
        
        
    }
    
    public function dataStreamOfTaxonomy($taxonomy){
        
        foreach($this->dataStreams as $stream){
            if( ($taxonomy->id() == $stream->id() && !is_null($stream->id())) || ($taxonomy->fullName() == $stream->fullName() && !is_null($stream->fullName()))){
                return $stream;   
            }    
        }
        
    }

}


?>