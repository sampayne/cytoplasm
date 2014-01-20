<?php
include_once($_SERVER['DOCUMENT_ROOT']."/database.php");

class GroupManager{
	
	private $database;
	
public function __construct(){
	
	$this->database = new Database;
	
	
}


public function getGroup($aGroup=0){
	
	$sqlString = "SELECT * FROM cyGroup WHERE id = $aGroup";
	$result = $this->database->mysqlQuery($sqlString);
	
	if($result[0]['id'] > 0){
		
		
		return $result[0];
		
		
	}
	
	return -1;
	
	
}

public function getGroups($aUser, $admin)
{

$sqlString = "";
	if($admin > 0){
		
		$sqlString  ="SELECT groupID FROM GroupAdmin WHERE userID = " . $aUser;

		
	}else{
		
		$sqlString = "SELECT groupID FROM GroupUser WHERE userID = " . $aUser;
		
		
	}
        $groups     =$this->database->mysqlQuery($sqlString);
    $returnArray=array();
    foreach($groups as $group) {
        $groupID  =$group['groupID'];
        $sqlString="SELECT name FROM cyGroup WHERE id = " . $groupID;
        $name     =$this->database->mysqlQuery($sqlString);
        $name     =$name[0][0];
        array_push($returnArray, array(
            $groupID,
            $name
        ));
        $returnArray=array_merge($returnArray, $this->findSubgroups($groupID, $name));
    }
    return $returnArray;
}
public function findSubgroups($agroupID, $agroupName)
{
    if($agroupID>0) {
        $sqlString  ="SELECT * FROM cyGroup WHERE super = " . $agroupID;
        $subgroups  =$this->database->mysqlQuery($sqlString);
        $returnArray=array();
        foreach($subgroups as $subgroup) {
            $id        =$subgroup['id'];
            $aname     =$subgroup['name'];
            $aname     =$agroupName . "-" . $aname;
            $groupArray=array(
                $id,
                $aname
            );
            array_push($returnArray, $groupArray);
            $returnArray=array_merge($returnArray, $this->findSubgroups($id, $aname));
        }
    }
    return $returnArray;
}

	
	
}



?>