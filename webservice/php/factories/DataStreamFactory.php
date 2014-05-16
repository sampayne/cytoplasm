<?php 
    
    abstract class DataStreamFactory{
            
             public static function LoadDataStreamForTaxonomyArticle($article, $taxonomy){
                    
                        $data = DataEntryFactory::LoadDataForArticleTaxonomy($article, $taxonomy);
                        return new DataStream($taxonomy,$data);
            }
            
            public static  function LoadAllStreamsForArticle($article, $idsOnly = 1){
                
    
                              $sqlString = 'SELECT COUNT(taxonomyID), taxonomyID 
                               FROM cyEntry
							   INNER JOIN cyArticleEntry
							   ON cyEntry.id = cyArticleEntry.entryID
							   WHERE cyArticleEntry.articleID = ? GROUP BY(taxonomyID)';
                               $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($article->id()));


                               $streams = array();
                
                               foreach($results as $result){
                                   
                                   $taxonomy = TaxonomyFactory::LoadWithID($result['taxonomyID'],0);
                                   
                         
                                   
                                   $data = NULL;
                                   
                                   if(!$idsOnly){
                 
                                        $data = DataEntryFactory::LoadDataForArticleTaxonomy($article, $taxonomy,time(),time()+(60*60));
                                    }
                                   
                                   array_push($streams, new DataStream($taxonomy,$data,$result[0]));


                               }
                                
                                
                            return $streams;

                
            }
            
            public static function LoadDataStreamForPage($article, $taxonomy, $page){
                
                $sqlString = 'SELECT COUNT(cyEntry.id)
                               FROM cyEntry
							   INNER JOIN cyArticleEntry
							   ON cyEntry.id = cyArticleEntry.entryID
							   WHERE cyArticleEntry.articleID = ? AND cyEntry.taxonomyID = ?';
                $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($article->id(), $taxonomy->id()));

                $data = DataEntryFactory::LoadPageOfDataForArticleTaxomomy($article,$taxonomy,$page);

                return new DataStream($taxonomy, $data, $results[0][0]);
                
                
            }
            
            
        
    }

    
?>