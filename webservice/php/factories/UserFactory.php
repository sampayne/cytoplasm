<?php 
    
    abstract class UserFactory extends ModelFactory{
            
                public static function Create ($user){
                    
                    if(UserFactory::checkUsername($username)){
                        
                        return new Error('Username already taken');
                    }
                    
                    $sqlString = 'INSERT INTO cyUser (name, username, password) VALUES (?,?,?)';
                    $params = array($name,$username,$password);
                    DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString,$params);
                    $id = DatabaseFactory::getFactory()->getDatabase()->lastID();
                    
                    if(isset($groupID)){
                        
                        $sqlString ='INSERT INTO cyGroupUser (userID, groupID) VALUES (?,?)';
                        $params = array($id, $groupID);
                        DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                                        
                    }
                    
                }
                
                
                public static function checkUsername($username){
                   
                   $sqlString = 'SELECT COUNT(*) FROM cyUser WHERE username=?';
                   $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString,array($username));
                   
                   if($results[0][0]){
                       
                       
                       return 1;
                       
                   }
                   
                   return 0;

                    
                }
                
                public static function Edit   ($id, $username = NULL, $password = NULL, $name = NULL, $groupID = NULL){
                    
                    
                    
                }
                public static function Delete ($id){
                    
                    $sqlString = 'DELETE FROM cyUser WHERE id=?; 
                                  DELETE FROM cyGroupUser WHERE userID=?;
                                  DELETE FROM cyGroupAdmin WHERE userID=?;
                                  DELETE FROM cyArticle WHERE id IN (SELECT articleID FROM cyUserCreator WHERE userID = ?);
                                  DELETE FROM cyUserCreator WHERE userID = ?;
                                  DELETE FROM cyEntry WHERE id IN (SELECT entryID FROM cyUserEntry WHERE userID = ?);
                                  DELETE FROM cyUserEntry WHERE userID = ?
                                '; 
                                
                     $params = array($id,$id,$id,$id,$id,$id,$id);   
                     
                     DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);        
                    
                }
                public static function LoadWithID($id){
                    
                    
                    
                }
                public static function LoadWithValues($id = NULL, $username = NULL, $password = NULL, $name = NULL, $groupID = NULL){
                    
                    $user = new User();
                    $user->setID($id);
                    $user->setUsername($username);
                    $user->setPassword($password);
                    $user->setName($name);
                    $user->setGroupID($groupID);
                    
                    return $user;                    
                }
            
                public static function LoadFromSQLRow($aSQLROW = ''){
                    
                    
                    $user = new User();
                    $user->setID($aSQLROW['id']);
                    $user->setName($aSQLROW['name']);
                    $user->setAuthKey($aSQLROW['authkey']);
                    return $user;
                    
                    
    
                }
            
                public static function LoadForLogin($id, $name,$authkey, $username){
                    
                    
                    $user = new User();
                    $user->setID($id);
                    $user->setName($name);
                    $user->setAuthKey($authkey);
                    $user->setUsername($username);
                    return $user;
                    
                    
                }
    }

    
?>