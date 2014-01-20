<?php
include_once($_SERVER['DOCUMENT_ROOT']."/database.php");
include_once($_SERVER['DOCUMENT_ROOT']."/group_manager.php");


class ArticleManager{
	
	private $database;
	
public function __construct(){
	
	$this->database = new Database;
	
	
}

public function getFullDetails($aArticle=0){
	
	
	$sqlString = "SELECT * FROM Article WHERE id = $aArticle";
	$result = $this->database->mysqlQuery($sqlString);

	if($result[0]['id'] > 0){
		
			return $result[0];
	}else{
		
		return -1;
		
	}

	
}

public function checkUserValid($aUser=0,$aArticle=0){
	
		$sqlString = "SELECT * FROM Article WHERE id = ".$aArticle;
		$article = $this->database->mysqlQuery($sqlString);

		if($article[0]['hidden'] < 1 && $article[0]['id'] > 0){
		
			return 1;
			
		}else{
			
			$sqlString = "SELECT Count(id) FROM UserOwner WHERE userID=$aUser AND articleID=$aArticle";
			$result = $this->database->mysqlQuery($sqlString);
			
			if($result[0][0] > 0){
				
				return 1;
				
			}
			
			$groupManager = new GroupManager;
			$groups = $groupManager->getGroups($aUser,-1);
		
			foreach($groups as $group){
					
			$articles = $this->getArticlesByGroup($group[0]);
			
			foreach($articles as $article){
				
				if($article[0]==$aArticle){
					
					return 1;
					
				}
			}
			
			
		   }
		   
		   return -1;
		}
}



public function getArticlesByUser($aUser)
{
		$sqlString ="SELECT articleID FROM UserOwner WHERE userID = '" . $aUser."'";
        $articles     =$this->database->mysqlQuery($sqlString);
    $returnArray=array();
    foreach($articles as $article) {
        $id  =$article['articleID'];
        $sqlString="SELECT name FROM Article WHERE id = " . $id;
        $name     =$this->database->mysqlQuery($sqlString);
        $name     =$name[0][0];
        array_push($returnArray, array(
            $id,
            $name
        ));
            }
    return $returnArray;
}

public function getArticlesByGroup($aGroup,$isHidden=0)
{

$sqlString = "";

	if($isHidden ==0){
	 $sqlString = 'SELECT Article.id,Article.name,Article.additional_fields FROM Article
								  INNER JOIN GroupCreator
								  ON Article.id = GroupCreator.ArticleID
								  WHERE GroupCreator.groupID = '.$aGroup;}else{
									  
						$sqlString = 'SELECT Article.id,Article.name,Article.additional_fields FROM Article
								  INNER JOIN GroupCreator
								  ON Article.id = GroupCreator.ArticleID
								  WHERE GroupCreator.groupID = '.$aGroup
								  .' AND Article.hidden !=1';
			  
								  }
								  
						  
								  
		
        $articles     =$this->database->mysqlQuery($sqlString);
    return $articles;
 
}
	
	
}



?>