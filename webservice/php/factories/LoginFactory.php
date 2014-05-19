<?php
     abstract class LoginFactory {
          public static function login($aUsername, $aPassword) {
               
               $query  = 'SELECT * FROM cyUser WHERE username = ? AND password = ?';
               
               $result = DatabaseFactory::getFactory()->getDatabase()->Query($query, array($aUsername,$aPassword));
               if ($result) {
                        
                        $auth = NULL;
                        
                    if(isset($result[0]['authkey']) && $result[0]['authkey'] != ''){
                        
                        $auth = $result[0]['authkey'];
                        
                    }else{    
                    
                    $auth      = LoginFactory::generateAuth();
                    $sqlString = 'UPDATE cyUser SET authkey = ? WHERE id = ?';
                    DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, array($auth,$result[0][0]));
                    
                    }
                    return UserFactory::LoadForLogin($result[0]['id'], $result[0]['name'], $auth, $aUsername);
               } else {
                    return new Error('Login Incorrect');
               }
          }
          private static function generateAuth() {
               while (-1) {
                    $authSeed = rand();
                    $authKey  = hash('sha512', $authSeed);
                    $query    = 'SELECT COUNT(*) FROM cyUser WHERE authkey = ?';
                    $result   = DatabaseFactory::getFactory()->getDatabase()->Query($query, array($authKey));
                    if ($result[0][0] == 0) {
                         return $authKey;
                    }
               }
          }
          public static function validateLogin($authKey, $username) {
               $query  = 'SELECT * FROM cyUser WHERE authkey = ? AND username = ? LIMIT 1';
               $result = DatabaseFactory::getFactory()->getDatabase()->Query($query,array($authKey,$username));
               if ($result) {
                    return UserFactory::LoadFromSQLRow($result[0]);
               } else {
                    return new Error('Authorisation Failed');
               }
          }
     }