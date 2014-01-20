<?php
/*set_time_limit(0);
include("database.php");
$database    = new Database;
$id1         = uploadTaxonomy("health-cardio-heartrate", $database);
$taxonomies  = array(
    $id1,
    $id2,
    $id3
);
$sqlString   = "SELECT id FROM Article";
$articles    = $database->mysqlQuery($sqlString);
$sqlString   = "SELECT id FROM User WHERE username != 'root'";
$users       = $database->mysqlQuery($sqlString);
$doctorarray = array();
foreach ($users as $user) {
    array_push($doctorarray, $user['id']);
}
$starttime     = 1389484800;
$finishtime    = 1390694400;
$sensorID      = 1;
$transmitterID = 1;
$recordNumber  = 1;
$parameters    = array();
$question      = array();
foreach ($articles as $article) {
    for ($i = $starttime; $i <= ($finishtime); $i += 60) {
        array_push($question, '(?,?,?,?,?,?)');
        array_push($parameters, $id1);
        array_push($parameters, $sensorID);
        array_push($parameters, $transmitterID);
        array_push($parameters, $i);
        array_push($parameters, $doctorarray[array_rand($doctorarray)]);
        array_push($parameters, rand(50, 120));
        if ($recordNumber == 20000) {
            $sqlString      = 'INSERT INTO cyEntry (taxonomyID, sensorID, transmitterID, reading_date, userID, entryValues) VALUES ' . implode(",", $question);
            $database->sqlInsert($sqlString, $parameters);
            $recordNumber = 0;
            $question     = array();
            $parameters   = array();
        }
        $recordNumber++;
    }
    $sensorID++;
    $transmitterID++;
}

$sqlString      = 'INSERT INTO cyEntry (taxonomyID, sensorID, transmitterID, reading_date, userID, entryValues) VALUES ' . implode(",", $question);
$database->sqlInsert($sqlString, $parameters);

function uploadTaxonomy($aTaxonomy, $aDatabase)
{
    $levels  = explode("-", $aTaxonomy);
    $last_id = -1;
    foreach ($levels as $level) {
        $sqlString = "SELECT id FROM cyTaxonomy WHERE name = '$level' AND super = $last_id";
        $result    = $aDatabase->mysqlQuery($sqlString);
        if (!$result) {
            $sqlString  = "INSERT INTO cyTaxonomy (name,super) VALUES (?,?)";
            $parameters = array(
                $level,
                $last_id
            );
            $aDatabase->sqlInsert($sqlString, $parameters);
            $last_id = $aDatabase->getNewestID("cyTaxonomy");
        } else {
            $last_id = $result[0][0];
        }
    }
    return $last_id;
}*/
?>