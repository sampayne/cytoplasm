<?php

abstract class GroupFactory extends ModelFactory
{

    public static function Create($group)
        {}


    public static function Edit($group)
        {}


    public static function Delete($id)
        {
            
            
                
            
            
            
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


    public static function LoadWithValues()
        {}


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

            $group = GroupFactory::LoadWithSQLRow($result);
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