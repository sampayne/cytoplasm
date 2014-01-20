<?php
include "database.php";
$database         = new Database;
$taxonomy         = "health-cardio-heartrate";
$sensorID         = "2";
$transmitterID    = "3";
$article          = $_GET['article'];
$permissions      = $_GET['permissions'];
$entries          = $_GET['entries'];

$validpermissions = uploadPermissions($permissions, $aDatabase);

if ($validpermissions)
{


	$articleID  = uploadArticle($article, $database);
	$taxonomyID = uploadTaxonomy($taxonomy, $database);
	$entryID    = uploadEntry($sensorID, $transmitterID, $taxonomyID, date("Y-m-d H:i:s"), 0, $database);

}

function uploadTaxonomy($aTaxonomy, $aDatabase)
{
	$levels  = explode("-", $aTaxonomy);
	$last_id = -1;
	foreach ($levels as $level)
	{
		$sqlString = "SELECT id FROM cyTaxonomy WHERE name = '$level' AND super = $last_id";
		$result    = $aDatabase->mysqlQuery($sqlString);
		if (!$result)
		{
			$sqlString  = "INSERT INTO cyTaxonomy (name,super) VALUES (?,?)";
			$parameters = array(
				$level,
				$last_id
			);
			$aDatabase->sqlInsert($sqlString, $parameters);
			$last_id = $aDatabase->getNewestID("cyTaxonomy");
		}
		else
		{
			$last_id = $result[0][0];
		}
	}
	return $last_id;
}




function uploadArticle($article, $aDatabase, $permissions)
{

	$result = $aDatabase->checkExists("Article", $article);

	if
	(!$result)
	{

		$sqlString = "INSERT INTO Article (name) VALUE (?)";
		$parameters = array($article);
		$aDatabase->sqlInsert($sqlString, $parameters);

	}



}
function uploadPermissions($permissions, $aDatabase)
{
	$set   = explode(",", $permissions);
	$valid = array();
	foreach ($set as $permission)
	{
		if (!strncmp($permission, "u", 1))
		{
			$user   = substr($permission, 1);
			$exists = $aDatabase->checkExists("User", $user);
			if ($exists)
				array_push($valid, $permission);
		}
		else if (!strncmp($permission, "g", 1))
			{
				$group  = substr($permission, 1);
				$exists = $aDatabase->checkExists("Group", $group);
				if ($exists)
					array_push($valid, $permission);
			}
	}
	return $valid;
}

function uploadEntry($sensorID, $transmitterID, $taxonomyID, $entryDate, $secure, $aDatabase)
{
	$sqlString  = "INSERT INTO cyEntry (sensorID, transmitterID, taxonomyID, reading_date, secure) VALUES(?,?,?,?,?)";
	$parameters = array(
		$sensorID,
		$transmitterID,
		$taxonomyID,
		$entryDate,
		$secure
	);
	$aDatabase->sqlInsert($sqlString, $parameters);
	$id = $aDatabase->getNewestID("cyEntry");
	return $id;
}
?>