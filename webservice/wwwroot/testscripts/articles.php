<?php 

/*include_once("database.php");
$database = new Database;
$names = array("Dorothy Rutherford","Irene Miller","Carolyn Langdon","Richard James","Ian Glover","Alexander Bailey","Isaac Ellison","Dorothy Young","Sarah Hunter","Zoe Davidson","Sean Ellison","Lauren Grant","Joshua Baker","Robert Paige","Anna Hart","Carl Sanderson","Katherine Langdon","Connor Howard","Victoria Alsop","Jane Bell"); 

$birthdates = array("1956-02-23",
"1958-06-23",
"1958-08-26",
"1965-04-24",
"1965-10-18",
"1966-02-13",
"1968-01-15",
"1968-06-13",
"1975-03-12",
"1982-01-16",
"1982-12-10",
"1983-01-16",
"1983-12-14",
"1991-04-30",
"1993-07-09",
"1993-08-18",
"1997-05-25",
"1999-08-24",
"2008-11-10",
"2010-06-20");

$address = array("88 Priory Street, Tonbridge, Kent TN9 2AH, UK","30 Nell Lane, Manchester, Greater Manchester M21 7SJ, UK",
"11 Cook's Drove, Earith, Huntingdon, Cambridgeshire PE28, UK",
"Pudding Lane, Comrie, Crieff, Perth and Kinross PH6 2DB, UK",
"6 Conifer Close, Saint Leonards, Ringwood, Dorset BH24 2RF, UK",
"45C Creekmoor Lane, Poole BH17 7BW, UK",
"2 Iris Close, Orpington, Greater London BR5 4FE, UK",
"3 Mulgrew Close, Maryport, Cumbria CA15 7DD, UK",
"37 Pensby Road, Heswall, Wirral, Merseyside CH60 7RA, UK",
"B4518, Llanbrynmair, Powys SY19 7BG, UK",
"8 Rifle Street, Blaenavon, Pontypool, Torfaen NP4 9QS, UK",
"49 Aspen Close, Harriseahead, Stoke-on-Trent, Staffordshire ST7 4HD, UK",
"Threadneedle Walk, London EC2N 1DW, UK",
"1 Almshayne Cottages, Uffculme, Cullompton, Devon EX15 3BD, UK",
"12A Green Crescent, Bucklesham, Ipswich, Suffolk IP10 0EA, UK",
"10 Latimer Crescent, Market Harborough, Leicestershire LE16 8AP, UK",
"85 Hinton Road, London SE24 0HT, UK",
"53 Howeth Road, Bournemouth BH10 5DY, UK",
"92 Southfield Street, Nelson, Lancashire BB9 0TB, UK",
"White Way, Cirencester, Gloucestershire GL7, UK");

$description = "NHS Patient";
for($i = 0; $i< 20;$i++){
	
	$sqlString = "INSERT INTO Article (name,description, additional_fields) VALUES (?,?,?)";
	$additional = $birthdates[$i].'/'.$address[$i];
	$parameters = array($names[$i],$description,$additional);
	
	$database->sqlInsert($sqlString,$parameters);
	
	$id = $database->getNewestID("Article");
	
	$sqlString = "INSERT INTO GroupCreator (groupID,articleID) VALUES (?,?)";
	$parameters = array(28,$id);
	$database->sqlInsert($sqlString,$parameters);
	
	

}


*/

?>