<?php

/*include('database.php');
$database = new Database;

$parameters    = array();
$question      = array();
$recordNumber = 1;

for($i = 1;$i<21;$i++){
	
	$articleID = $i+38;
	$sqlString = "SELECT id FROM cyEntry WHERE sensorID =$i";
	$result = $database->mysqlQuery($sqlString);
	
	foreach($result as $entry){
		
		array_push($question, '(?,?)');
        array_push($parameters, $entry['id']);
        array_push($parameters, $articleID);
        
        if ($recordNumber == 20000) {
            $sqlString      = 'INSERT INTO ArticleEntry (entryID, articleID) VALUES ' . implode(",", $question);
            $database->sqlInsert($sqlString, $parameters);
            $recordNumber = 0;
            $question     = array();
            $parameters   = array();
        }

		
		
		$recordNumber ++;
		
		
	}
	
	
	
	
}


$sqlString      = 'INSERT INTO ArticleEntry (entryID, articleID) VALUES ' . implode(",", $question);
$database->sqlInsert($sqlString, $parameters);*/


?>