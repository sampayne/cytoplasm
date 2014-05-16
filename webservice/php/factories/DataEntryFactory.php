<?php 
    
    abstract class DataEntryFactory extends ModelFactory{
            
                public static function Create($dataentry){

                    
                    $sqlString = 'INSERT INTO cyEntry (entryValues, sensorID, transmitterID, reading_date, hidden, userID, taxonomyID) VALUES (?,?,?,?,?,?,?)';
                    $params = array($values,$sensorID, $transmitterID, $readingdate,$hidden, $userID, $taxonomy);
                    DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString,$params);
                    $id = DatabaseFactory::getFactory()->getDatabase()->lastID();
                    
                    if(substr($article, 0, 1) == 'u'){
                        
                        $sqlString ='INSERT INTO cyUserEntry (userID, entryID) VALUES (?,?)';

                        
                    }elseif(substr($article, 0, 1) == 'a'){
                        
                        $sqlString ='INSERT INTO cyArticleEntry (articleID, entryID) VALUES (?,?)';
                        
                    }
                    
                    $params = array(substr($article, 1), $id);
                    
                    DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);
                    
                }
                
                public static function Edit($dataentry){}
                public static function Delete($id){}
                
                public static function LoadWithID($id, $idsOnly = NULL){
                    
                    $sqlString = 'SELECT * FROM cyEntry WHERE id = ?';
                    
                    if($idsOnly){
                        
                        'SELECT id,sensorID,transmitterID, userID FROM cyEntry WHERE id = ?';
                        
                    }
                    
                    $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($id));
                    $dataentry = new DataEntry();
                    $dataentry->initWithSQLRow($results[0]);
                    return $dataentry;
                    
                }
                
                public static function LoadWithValues($values, $article, $sensorID, $transmitterID, $readingdate, $hidden, $userID, $taxonomy){}
                
                public static function LoadPageOfDataForArticleTaxomomy($article,$taxonomy,$page){
                    
                      $sqlString = 'SELECT * 
                               FROM cyEntry
							   INNER JOIN cyArticleEntry
							   ON cyEntry.id = cyArticleEntry.entryID
							   WHERE cyArticleEntry.articleID = ? AND cyEntry.taxonomyID = ? LIMIT '.(($page-1)*500).','.($page)*500;
     
            $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($article->id(), $taxonomy->id() ));
            
            $data = array();
            
            foreach($results as $result){
            
                    $dataentry = new DataEntry();
                    
                    $dataentry->initWithSQLRow($result);
            
                    
                    array_push($data, $dataentry);
                    
                }
                
                return $data;

                    
                    
                }
                
                public static function LoadDataForArticleTaxonomy($article, $taxonomy, $startTime, $endTime){
                    
                                     
                    $sqlString = 'SELECT cyEntry.id, cyEntry.entryValues, cyEntry.reading_date 
                               FROM cyEntry
							   INNER JOIN cyArticleEntry
							   ON cyEntry.id = cyArticleEntry.entryID
							   WHERE cyArticleEntry.articleID = ? AND cyEntry.taxonomyID = ? AND cyEntry.reading_date >= ? AND cyEntry.reading_date <= ? 
							   ORDER BY cyEntry.reading_date ASC';
     
            $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($article->id(), $taxonomy->id(), $startTime, $endTime));
            
            $data = array();
            
            foreach($results as $result){
            
                    $dataentry = new DataEntry();
                    
                    $dataentry->initWithSQLRow($result);
            
                    
                    array_push($data, $dataentry);
                    
                }
                
                return $data;
                
                }
            
          
    }


        
?>