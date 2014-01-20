<?php 

include_once($_SERVER['DOCUMENT_ROOT']."/database.php");

class TaxonomyManager{
	
	
	private $database;
	
	public function __construct(){
	
	$this->database = new Database;
	
	
	}
	
	
	public function getTaxonomyID($aTaxonomy = ""){
			
    $levels  = explode("-", $aTaxonomy);
    $last_id = -1;
    foreach ($levels as $level)
    {
        $sqlString = "SELECT id FROM cyTaxonomy WHERE name = '$level' AND super = $last_id";
        $result    = $this->database->mysqlQuery($sqlString);
        if (!$result)
        {
        		$last_id = -1;
        
        }
        else
        {
            $last_id = $result[0][0];
        }
    }
    return $last_id;
		
	}
	
	
	
	
}