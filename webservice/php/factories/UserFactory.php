<?php 
    
    abstract class UserFactory extends ModelFactory{
            
                public static function Create ($username = NULL, $password = NULL, $name = NULL, $groupID = NULL){
                    
                    $errors = array();
                    
                    if(UserFactory::checkUsername($username)){
                        
                        array_push($errors, new Error('Username Already Taken'));
                    }
                    
                    
                    
                    if(count($errors)){
                        
                        return $errors;
                        
                    }else{
                        
                        
                    $hashed = hash('sha512', $password);
                    
                    $sqlString = 'INSERT INTO cyUser (name, username, password,creation_date,authkey,signature) VALUES (?,?,?,?,?,?)';
                    
                    $params = array($name,$username,$hashed,time(),'','');
                    DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString,$params);
                    $id = DatabaseFactory::getFactory()->getDatabase()->lastID();
                    
                    if(isset($groupID)){
                        
                        $sqlString ='INSERT INTO cyGroupUser (userID, groupID) VALUES (?,?)';
                        $params = array($id, $groupID);
                        DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                                        
                    }
                        
                        
                    }
                                        
                }
            
                public static function Edit ($id, $username = NULL, $password = NULL, $name = NULL, $groupID = NULL, $oldGroup = NULL){
                   
                   $errors = array();
                   
                    if($id < 1){
                        array_push($errors, new Error('There was a problem, please go back and try again'));
                    }
                            
                   $user =  UserFactory::checkUsername($username);
                    
                    if($user > 0 && $user != $id){
                        
                        array_push($errors,new Error('Username already exists'));
                
                    }
                    
                    
                    if(count($errors)){
                        
                        return $errors;
                        
                    }else{
                        
                        
                                   
                    $sqlString = 'UPDATE cyUser SET name=?, username=?, password=? WHERE id=?';
                    $params = array($name,$username,$password,$id);
                    DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString,$params);
                    
                    if(isset($groupID) && isset($oldGroup) && $groupID != $oldGroup){
                        
                        $sqlString ='DELETE FROM cyGroupUser WHERE userID=? AND groupID =?';
                        $params = array($id, $oldGroup);
                        DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                        
                        $sqlString ='INSERT INTO cyGroupUser (userID, groupID) VALUES (?,?)';
                        $params = array($id, $groupID);
                        DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                         
                        
                    }
                    
                    }
                    
                }
                
                public static function Delete ($id){
                    
                    if($id < 1){
                        
                        return array(new Error('There was a problem, please go back and try again'));
                        
                    }
                    
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
                
                
                public static function LoadWithID($id, $idsOnly = 0){
                    
                    $sqlString = 'SELECT * FROM cyUser WHERE id = ?';
                    $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString,array($id));
                    
                    if($results[0][0]){
                    $user = new User();
                    $user->initWithSQLRow($results[0]);
                    
                    }else{
                        
                        return Error('User Not Found');
                        
                    }
                    
                }
                
                public static function checkUsername($username){
                   
                   $sqlString = 'SELECT id FROM cyUser WHERE username=?';
                   $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString,array($username));
                   
                   if(isset($results[0]['id'])){
                   
                   return $results[0]['id'];
                   
                   }else{
                       
                       return 0;
                   }
                   
                }
                
                public static function LoadFromSQLRow($sqlRow){
                    
                    $user = new User();
                    $user->initWithSQLRow($sqlRow);
                    return $user;
                    
                    
                    
                }
                
                public static function GetAllUsers(){
                    
                    $sqlString = 'SELECT * FROM cyUser';
                    $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
                    
                    $users = array();
                    
                    foreach($results as $result){
                        
                        $user = new User();
                        $user->initWithSQLRow($result);
                        array_push($users, $user);
                        
                    }

                    return $users;
                    
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