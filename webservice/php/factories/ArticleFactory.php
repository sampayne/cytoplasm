
<?php
abstract class ArticleFactory extends ModelFactory
{
    
    public static function Create($name = NULL, $description = NULL, $hidden = 0, $secure = 0, $additional_fields = NULL, $owner = NULL)
    {
        
        $errors = array();
        
        if (count($errors)) {
            return $errors;
            
        } else {
            
            $sqlString = 'INSERT INTO cyArticle (name,description,hidden,secure,additional_fields) VALUES(?,?,?,?,?)';
            $params    = array(
                $name,
                $description,
                $hidden,
                $secure,
                $additional_fields
            );
            DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
            $id = DatabaseFactory::getFactory()->getDatabase()->lastID();
            
            if (substr($owner, 0, 1) == 'u') {
                
                $sqlString = 'INSERT INTO cyUserCreator (userID, articleID) VALUES(?,?)';
                $params    = array(
                    substr($owner, 1),
                    $id
                );
                DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                
            } else {
                
                $sqlString = 'INSERT INTO cyGroupCreator (userID, articleID) VALUES(?,?)';
                $params    = array(
                    substr($owner, 1),
                    $id
                );
                DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                
            }
            
        }
        
    }
    
    
    public static function Edit($id, $name = NULL, $description = NULL, $hidden = 0, $secure = 0, $additional_fields = NULL, $owner = NULL)
    {
        
        $errors = array();
        
        if (count($errors)) {
            return $errors;
            
        } else {
            
            $sqlString = 'UPDATE cyArticle SET name=? description=? hidden=? secure=? additional_fields= ? WHERE id = ?';
            $params    = array(
                $name,
                $description,
                $hidden,
                $secure,
                $additional_fields,
                $id
            );
            DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                        
            $sqlString = 'DELETE FROM cyUserCreator WHERE articleID = ?;
                          DELETE FROM cyGroupCreator WHERE articleID = ?';
            $params    = array(
                $id,
                $id
            );
            DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
            
            if (substr($owner, 0, 1) == 'u') {
                $sqlString = 'INSERT INTO cyUserOwner (userID, articleID) VALUES(?,?)';
                $params    = array(
                    substr($owner, 1),
                    $id
                );
                DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                
            } else {
                $sqlString = 'INSERT INTO cyGroupCreator (userID, articleID) VALUES(?,?)';
                $params    = array(
                    substr($owner, 1),
                    $id
                );
                DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                
            }
            
        }
        
        
        
    }
    
    
    public static function Delete($id)
    {
        
        $sqlString = 'DELETE FROM cyArticle WHERE id = ?;
                      DELETE FROM cyUserCreator WHERE articleID = ?;
                      DELETE FROM cyGroupCreator WHERE articleID = ?;
                      DELETE FROM cyEntry WHERE id IN (SELECT entryID FROM cyArticleEntry WHERE articleID = ?);
                      DELETE FROM cyArticleEntry WHERE articleID = ?';
        
        $params = array(
            $id,
            $id,
            $id,
            $id,
            $id
        );
        
        DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
        
    }
    
    public static function LoadWithValues($id, $name = NULL, $description = NULL, $hidden = 0, $secure = 0, $additional_fields = NULL, $owner = NULL){
        
        $article = new Article();
        $article->setID($id);
        $article->setName($name);
        $article->setDescription($description);
        $article->setHidden($hidden);
        $article->setSecure($secure);
        $article->setAdditionalFields($additional_fields);
        
        
        if (substr($owner, 0, 1) == 'u') {
            
            $user = UserFactory::LoadWithID(substr($owner, 1));
            $article->setUserCreator($user);
            
        } else {

            $user = GroupFactory::LoadWithID(substr($owner, 1));
            $article->setGroupCreator($user);
                
            }


            return $article;
        
        
        
        
    }
    
    
    public static function LoadGroupCreatorOfArticle($article, $idsOnly = 0)
    {
        
        $sqlString    = 'SELECT groupID FROM cyGroupCreator WHERE articleID = ? LIMIT 1';
        $groupCreator = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array(
            $article->id()
        ));
        
        
        
        if ($groupCreator) {
            
            $group = GroupFactory::LoadWithID($groupCreator[0]['groupID'], $idsOnly);
            
            return $group;
            
        }
        
        return NULL;
        
    }
    
    
    public static function GetAllArticles()
    {
        
        $sqlString = 'SELECT * FROM cyArticle ORDER BY name ASC';
        $results   = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
        
        $articles = array();
        
        foreach ($results as $result) {
            
            
            $article = new Article();
            $article->initWithSQLRow($result);
            array_push($articles, $article);
            
            
        }
        
        return $articles;
        
    }
    
    
    public static function LoadUserCreatorOfArticle($article, $idsOnly = 0)
    {
        
        $sqlString   = 'SELECT userID FROM cyUserCreator WHERE articleID =  ? LIMIT 1';
        $userCreator = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array(
            $article->id()
        ));
        
        if ($userCreator) {
            
            return UserFactory::LoadWithID($userCreator[0]['userID'], $idsOnly);
            
            
        }
        
        return NULL;
    }
    
    
    public static function LoadArticlesForGroup($group, $idsOnly = 0)
    {
        
        $sqlString = 'SELECT articleID FROM cyGroupCreator WHERE groupID =?';
        $results   = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array(
            $group->id()
        ));
        
        $articleArray = array();
        
        foreach ($results as $result) {
            array_push($articleArray, $result['articleID']);
        }
        
        if (count($articleArray)) {
            
            $sqlString = 'SELECT * FROM cyArticle WHERE id in (' . implode(',', $articleArray) . ')';
            
            if ($idsOnly) {
                $sqlString = 'SELECT id FROM cyArticle WHERE id in (' . implode(',', $articleArray) . ')';
            }
            
            $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
            
            $articleArray = array();
            foreach ($results as $result) {
                $article = new Article();
                $article->initWithSQLRow($result);
                array_push($articleArray, $article);
            }
            
            return $articleArray;
        }
        
        
        
        
    }
    
    
    public static function LoadWithID($id, $idsOnly = NULL)
    {
        
        $sqlString = 'SELECT * FROM cyArticle WHERE id = ?';
        
        if ($idsOnly) {
            
            $sqlString = 'SELECT id FROM cyArticle WHERE id = ?';
            
        }
        
        $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array(
            $id
        ));
        
        $article = new Article();
        $article->initWithSQLRow($results[0]);
        return $article;
    }
    
    
    public static function LoadGroupArticlesForUser($user, $idsOnly)
    {
        
        $groupArray = array();
        
        if(!is_null($user->groups())){
        
        foreach ($user->groups() as $group) {
            array_push($groupArray, $group->id());
        }
        
        $sqlString = 'SELECT * FROM cyArticle WHERE id in (SELECT articleID FROM cyGroupCreator WHERE groupID in (' . implode(',', $groupArray) . '))';
        
        if ($idsOnly) {
            $sqlString = 'SELECT id FROM cyArticle WHERE id in (SELECT articleID FROM cyGroupCreator WHERE groupID in (' . implode(',', $groupArray) . '))';
        }
        
        $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
        
        $articleArray = array();
        foreach ($results as $result) {
            $article = new Article();
            $article->initWithSQLRow($result);
            array_push($articleArray, $article);
        }
        
        if(count($articleArray)){
        
        return $articleArray;
        
        }
        }
        return NULL;
    
        
    }
    
    
    public static function LoadAdminArticlesForUser($user, $idsOnly = 0)
    {
        
        if (!is_null($user->adminGroups())) {
        
            $groupArray = array();
            foreach ($user->adminGroups() as $group) {
                array_push($groupArray, $group->id());
            }
            
            $sqlString = 'SELECT * FROM cyArticle WHERE id IN (SELECT articleID FROM cyGroupCreator WHERE groupID in (' . implode(',', $groupArray) . '))';
            
            if ($idsOnly) {
                $sqlString = 'SELECT id FROM cyArticle WHERE id IN (SELECT articleID FROM cyGroupCreator WHERE groupID in (' . implode(',', $groupArray) . '))';
            }
            
            $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
            
            $articleArray = array();
            foreach ($results as $result) {
                $article = new Article();
                $article->initWithSQLRow($result);
                array_push($articleArray, $article);
            }
            
            if(count($articleArray)){
            
            return $articleArray;
            }
            
            return NULL;
        }
        
        return NULL;
    }
    
    
    public static function LoadUserArticlesForUser($user, $idsOnly = 0)
    {
        
        $sqlString = 'SELECT * FROM cyArticle WHERE id in (SELECT articleID FROM cyUserCreator WHERE userID = ?)';
        
        if ($idsOnly) {
            $sqlString = 'SELECT id FROM cyArticle WHERE id in (SELECT articleID FROM cyUserCreator WHERE userID = ?)';
        }
        
        $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array(
            $user->id()
        ));
        
        $articleArray = array();
        foreach ($results as $result) {
            $article = new Article();
            $article->initWithSQLRow($result);
            array_push($articleArray, $article);
        }
        
        if(count($articleArray)){
        
        return $articleArray;
        
        }
        
  
    }
    
    
}


?>