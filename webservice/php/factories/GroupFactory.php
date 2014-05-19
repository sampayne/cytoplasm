<?php

abstract class GroupFactory extends ModelFactory
{

    public static function Create($name = NULL, $super = -1, $additional_fields = NULL, $creator = NULL)
        {
            $errors = array();
            
            if(count($errors)){
                
                return $errors;
                  
            }else{
            
                $sqlString = 'INSERT INTO cyGroup (name,super,additional) VALUES (?,?,?)';
                $params = array($name,$super, $additional_fields);
                DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString,$params);
                $id = DatabaseFactory::getFactory()->getDatabase()->lastID();
                
                if($super < 1){
                    
                    $sqlString = 'INSERT INTO cyGroupUser (groupID, userID) VALUES (?,?); INSERT INTO cyGroupAdmin (groupID, userID) VALUES (?,?)';
                    $params = array($id, $creator,$id,$creator);
                    DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString,$params);
                }
            
            }
        }


    public static function Edit($id, $name = NULL, $super = -1, $additional_fields = NULL, $creator = NULL, $oldSuper = -1)
        {
            
             $errors = array();
            
            if($id < 1){
                
                array_push($errors, 'Group Does Not Exist');
                
            }
            
            if(count($errors)){
                
                return $errors;
                
                
            }else{
            
                $sqlString = 'UPDATE cyGroup SET name = ?, super = ?, additional= ? WHERE id = ?';
                $params = array($name,$super, $additional_fields, $id);
                DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString,$params);
                
                if($super < 1 && $oldSuper > 0){
                    
                    $sqlString = 'INSERT INTO cyGroupUser (groupID, userID) VALUES (?,?); INSERT INTO cyGroupAdmin (groupID, userID) VALUES (?,?)';
                    $params = array($id, $creator,$id,$creator);
                    DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString,$params);
                }
            
            }
            
            
        }


    public static function Delete($id)
        {
            //Load group and subgroups and then delete all subgroups
            
            $sqlString = 'SELECT id FROM cyGroup WHERE super = ?';
            $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString,array($id)); 
            
            foreach($results as $result){
                
                GroupFactory::Delete($result['id']);
                
            }
            
            $sqlString = 'SELECT articleID FROM cyGroupCreator WHERE groupID = ?';
            $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString,array($id)); 
            foreach($results as $result){
                
                ArticleFactory::Delete($result['articleID']);
                
            }
            
            $sqlString = 'DELETE FROM cyGroup WHERE id = ?; 
                          DELETE FROM cyGroupUser WHERE groupID = ?;
                          DELETE FROM cyGroupAdmin WHERE groupID = ?';
                          
            $params = array($id,$id,$id);
            DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString,$params); 
            
        }


        public static function LoadAllGroups(){
            
            $sqlString = 'SELECT * FROM cyGroup ORDER BY name ASC';
            $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
            
            $groups = array();
            
            foreach($results as $result){
                
                
                $group = new Group();
                $group->initWithSQLRow($result);
                array_push($groups, $group);
                
                
            }
            
            return $groups;
            
        }

    public static function LoadWithID($id, $idsOnly = 0)
        {
            
            $sqlString = 'SELECT * FROM cyGroup WHERE id = ?';
            $results  = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString, array($id));
            
            if($results[0]){

                $group = new Group();
                $group->initWithSQLRow($results[0]);
                
                
                return $group;
                
            }
            
        }


    public static function LoadWithValues($id, $name = NULL, $super = -1, $additional_fields = NULL)
        {
            
            $group = new Group();
            
            $group->setID($id);
            $group->setName($name);
            $group->setAdditional($additional_fields);
            $superGroup = GroupFactory::LoadWithID($super);
            $group->setSuper($superGroup);
            
            return $group;  
            
        }


        public static function LoadAdminGroupsForUser($user,$idsOnly = 0 ){
            
             $groups = array();

        if
        ($idsOnly)
        {

            $sqlString  = 'SELECT groupID FROM cyGroupAdmin WHERE userID = ' . $user->id();

        }else
        {

            $sqlString  = 'SELECT groupID FROM cyGroupAdmin WHERE userID = ' . $user->id();

        }


        $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
        foreach
        ($results as $result)
        {

            $group = GroupFactory::LoadWithID($result['groupID']);
            array_push($groups, $group);
            $groups = array_merge($groups, GroupFactory::FindSubgroupsOfGroup($group, $idsOnly));

        }

       if(count($groups) > 0){
                 return $groups;

            
        }else{
            
            
            return NULL;
        }

            
            
        }

    public static function LoadGroupsForUser($user, $idsOnly = 0)
    {

        $groups = array();

    

        $sqlString         = 'SELECT groupID FROM cyGroupUser WHERE userID = ' . $user->id();

        $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
        foreach
        ($results as $result)
        {

            if($idsOnly){
            $group = GroupFactory::LoadWithSQLRow($result);
            }else{
                
                $group =GroupFactory::LoadWithID($result['groupID']);
            }
            array_push($groups, $group);
            $groups = array_merge($groups, GroupFactory::FindSubgroupsOfGroup($group, $idsOnly));

        }

        if(count($groups) > 0){
                 return $groups;

            
        }else{
            
            
            return NULL;
        }
        
   
    }

    public static function LoadWithSQLRow($SQLRow)
    {

        $group = new Group();
        $group->initWithSQLRow($SQLRow);
        return $group;


    }



    public static function FindSuperGroupOfGroup($superID, $idsOnly = 0){
        
        $sqlString = '';
        
        if($idsOnly){
        
             $sqlString = 'SELECT id FROM cyGroup WHERE id = '.$superID;
            
        }else{
            
             $sqlString = 'SELECT * FROM cyGroup WHERE id = '.$superID;
            
        }
         
        $supergroup = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
    
        if($supergroup[0]){
            
            return  GroupFactory::LoadWithSQLRow($supergroup[0]);
      
        }   
    }
    
    
    public static function FindSubgroupsOfGroup($aGroup, $idsOnly = 0)
    {
        if
        ($aGroup->id() > 0)
        {
            $sqlString = '';

            if
            ($idsOnly)
            {
                $sqlString = 'SELECT id FROM cyGroup WHERE super = ' . $aGroup->id();
            }else
            {
                $sqlString = 'SELECT * FROM cyGroup WHERE super = ' . $aGroup->id();
            }

            $subgroups = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
            $subs      = array();
            foreach ($subgroups as $subgroup)
            {
                $group = GroupFactory::LoadWithSQLRow($subgroup);
                $subs = GroupFactory::FindSubgroupsOfGroup($group, $idsOnly);
                array_push($subs, $group);
            }
            return $subs;

        }

        return array();

    }


}





?>