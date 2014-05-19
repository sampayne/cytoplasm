<?php

abstract class TaxonomyFactory extends ModelFactory
{

    

    public static function Create()
        {
     
                 
            
        }


    public static function Edit($taxonomy)
        {/*Taxonomy cannot be Editited*/}


    public static function Delete($id)
        {/*Taxonomy cannot be Deleted*/}


    public static function LoadWithID($id, $idsOnly = NULL)
        {            
            $sqlString = 'SELECT * FROM cyTaxonomy WHERE id = ?';
            
            if($idsOnly){
                
                $sqlString = 'SELECT id,super FROM cyTaxonomy WHERE id = ?';  
            } 
            
            $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString,array($id));
            
            if($results){
            $taxonomy = new Taxonomy();
            $taxonomy->initWithSQLRow($results[0]);
            return $taxonomy;
            }
        }


    public static function LoadWithValues()
        {}


    private static function IDFromName($taxonomyName)
    {

        $levels  = explode("-", $taxonomyName);
        $last_id = -1;

        foreach ($levels as $level)
        {
            $sqlString = "SELECT id FROM cyTaxonomy WHERE name = '$level' AND super = $last_id LIMIT 1";
            $result    = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
            if (!$result)
            {
                $last_id = -1;
            } else
            {
                $last_id = $result[0][0];
            }
        }

        return $last_id;

    }


    public static function GetAllTaxonomies(){
        
        $sqlString = 'SELECT * FROM cyTaxonomy';
        $results    = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
        $taxonomies = array();
         
        foreach($results as $result){
        
            $taxonomy = new Taxonomy();
            $taxonomy->initWithSQLRow($result);
            array_push($taxonomies, $taxonomy);
    
        }
    
        return $taxonomies;
        
    }
    
    public static function LoadWithName($taxonomyName)
    {
        $taxonomy = new Taxonomy();
        $id = TaxonomyFactory::IDFromName($taxonomyName);
        if($id > 0)
        {
            $taxonomy->setID($id);
            $taxonomy->setName($taxonomyName);
            return $taxonomy;
            
        }else{

            return new Error("Taxonomy Does Not Exist");

        }

    }


}


?>