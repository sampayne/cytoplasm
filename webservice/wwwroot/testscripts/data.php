<?php

require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');

/*
$sqlString = 'DELETE FROM cyEntry WHERE 1';
DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, NULL);
$sqlString = 'DELETE FROM cyArticleEntry WHERE 1';
DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, NULL);
*/
set_time_limit(0);

$taxonomy     =  TaxonomyFactory::LoadWithName('health-cardio-heartrate');
$taxonomy1    =  TaxonomyFactory::LoadWithName('health-cardio-ecg');
$taxonomy2    =  TaxonomyFactory::LoadWithName('health-general-activity');
$taxonomy3    =  TaxonomyFactory::LoadWithName('health-cardio-bloodpressure');


$sqlString   = "SELECT id FROM cyArticle LIMIT 5";
$articles    = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
$starttime     = time();
$finishtime    = time() + (60*60*24);
$sensorID      = 2;
$transmitterID = 5;

  

        

        
 for ($i = $starttime; $i <= ($finishtime); $i ++) {

        $hearrate = 70+rand(0,10);
        $ecg = 50 + rand(0,10);
        $activity = rand(0,30);
        $bloodpressure = (100 + rand(0,10)).',70';
        
        
        DataEntryFactory::Create($hearrate, 'a'.$articles[2]['id'], $sensorID, $transmitterID, $i, 0 , 15 , $taxonomy->id());
        DataEntryFactory::Create($ecg, 'a'.$articles[2]['id'], $sensorID+ 1, $transmitterID, $i, 0 , 15 , $taxonomy1->id());
        DataEntryFactory::Create($activity, 'a'.$articles[2]['id'], $sensorID+ 2, $transmitterID, $i, 0 , 15 , $taxonomy2->id());
        DataEntryFactory::Create($bloodpressure, 'a'.$articles[2]['id'], $sensorID+ 3, $transmitterID, $i, 0 , 15 , $taxonomy3->id()); 
    }
    
    $sensorID = 4 * $transmitterID;
    $transmitterID++;
    
    
  echo 'Script Complete For ',$articles[2]['id'] ;

?>