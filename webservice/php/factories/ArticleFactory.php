<?php abstract class ArticleFactory extends ModelFactory{
              
    public    static function Create($model){return -1;}

    public    static function Edit($model){return -1;}
    public    static function Delete($id){
        
        $sqlString = 'DELETE FROM cyArticle WHERE id = ?;
                      DELETE FROM cyUserCreator WHERE articleID = ?;
                      DELETE FROM cyGroupCreator WHERE articleID = ?;
                      DELETE FROM cyEntry WHERE id IN (SELECT entryID FROM cyArticleEntry WHERE articleID = ?);
                      DELETE FROM cyArticleEntry WHERE articleID = ?';
                      
        $params = array($id, $id, $id, $id, $id);
        
        DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);              
                      
            
    }

              
       public static function LoadGroupCreatorOfArticle($article, $idsOnly = 0){
           
            $sqlString          = 'SELECT groupID FROM cyGroupCreator WHERE articleID = ? LIMIT 1';
            $groupCreator       = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($article->id()));
            

            
            if($groupCreator){
            
                $group = GroupFactory::LoadWithID($groupCreator[0]['groupID'], $idsOnly);
 
                return $group;            
            
            }
            
            return NULL;
        
        }
        
        public static function GetAllArticles(){
            
            $sqlString = 'SELECT * FROM cyArticle ORDER BY name ASC';
            $results       = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);

            $articles = array();
            
               foreach($results as $result){
                   
                   
                   $article = new Article();
                   $article->initWithSQLRow($result);
                   array_push($articles, $article);
                   
                   
               }
               
             return $articles;  
            
        }
        
        public static function LoadUserCreatorOfArticle($article, $idsOnly = 0){

            $sqlString         = 'SELECT userID FROM cyUserCreator WHERE articleID =  ? LIMIT 1';
            $userCreator       = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($article->id()));
            
            if($userCreator){
            
                 return UserFactory::LoadWithID($userCreator[0]['userID'], $idsOnly);
            
            
            }
            
            return NULL;
       }
       
       public static function LoadArticlesForGroup($group, $idsOnly = 0){
           
            $sqlString = 'SELECT articleID FROM cyGroupCreator WHERE groupID =?';
            $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($group->id()));
                    
                $articleArray = array();  
                  
                    foreach($results as $result){
                        array_push($articleArray, $result['articleID']);
                    }
                    
                    if(count($articleArray)){
                    
                    $sqlString = 'SELECT * FROM cyArticle WHERE id in ('.implode(',', $articleArray).')';
                    
                    if($idsOnly){
                        $sqlString = 'SELECT id FROM cyArticle WHERE id in ('.implode(',', $articleArray).')';
                    }
                    
                    $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
                    
                    $articleArray = array();
                    foreach($results as $result){
                        $article = new Article();
                        $article->initWithSQLRow($result);
                        array_push($articleArray, $article);   
                    }
                    
                    return $articleArray;}
       }
       
    public static function LoadWithID($id, $idsOnly = NULL){
        
        $sqlString = 'SELECT * FROM cyArticle WHERE id = ?';
        
        if($idsOnly){
        
        $sqlString = 'SELECT id FROM cyArticle WHERE id = ?';
        
        }

        $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString,array($id));
        
        $article = new Article();
        $article->initWithSQLRow($results[0]);
        return $article;
    }
    
    
          public static function LoadGroupArticlesForUser($user, $idsOnly){
          
                    $groupArray = array();
                    foreach($user->groups() as $group){
                        array_push($groupArray, $group->id());
                    
                    
                    
                    
                    }
                           
                   /*
 $sqlString = 'SELECT articleID FROM cyGroupCreator WHERE groupID in ('.implode(',', $groupArray).')';
                    $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
                    
                    $articleArray = array();    
                    foreach($results as $result){
                        array_push($articleArray, $result['articleID']);
                    }
                    
*/
                    $sqlString = 'SELECT * FROM cyArticle WHERE id in (SELECT articleID FROM cyGroupCreator WHERE groupID in ('.implode(',', $groupArray).'))';
                    
                    if($idsOnly){
                        $sqlString = 'SELECT id FROM cyArticle WHERE id in (SELECT articleID FROM cyGroupCreator WHERE groupID in ('.implode(',', $groupArray).'))';
                    }
                    
                    $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
                    
                    $articleArray = array();
                    foreach($results as $result){
                        $article = new Article();
                        $article->initWithSQLRow($result);
                        array_push($articleArray, $article);   
                    }
                    
                    return $articleArray;

          }
          
          public static function LoadAdminArticlesForUser($user, $idsOnly){
          
                    if(!is_null($user->adminGroups())){
                    $groupArray = array();
                    foreach($user->adminGroups() as $group){
                        array_push($groupArray, $group->id());
                    }
    
                    $sqlString = 'SELECT * FROM cyArticle WHERE id IN (SELECT articleID FROM cyGroupCreator WHERE groupID in ('.implode(',', $groupArray).'))';
                    
                    if($idsOnly){
                        $sqlString = 'SELECT id FROM cyArticle WHERE id IN (SELECT articleID FROM cyGroupCreator WHERE groupID in ('.implode(',', $groupArray).'))';
                    }
                    
                    echo '<br>', $sqlString, '<br>';
                    
                    $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
                    
                    $articleArray = array();
                    foreach($results as $result){
                        $article = new Article();
                        $article->initWithSQLRow($result);
                        array_push($articleArray, $article);   
                    }
                    
                    return $articleArray;
                    
                    }
                    
                    return NULL;
          }
          
          public static function LoadUserArticlesForUser($user, $idsOnly){
          
                    $sqlString = 'SELECT * FROM cyArticle WHERE id in (SELECT articleID FROM cyUserCreator WHERE userID = ?)';
                    
                    if($idsOnly){
                        $sqlString = 'SELECT id FROM cyArticle WHERE id in (SELECT articleID FROM cyUserCreator WHERE userID = ?)';
                    }
                    
                    $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($user->id()));
                    
                    $articleArray = array();
                    foreach($results as $result){
                        $article = new Article();
                        $article->initWithSQLRow($result);
                        array_push($articleArray, $article);   
                    }
                    
                    return $articleArray;
          }         
}

?>