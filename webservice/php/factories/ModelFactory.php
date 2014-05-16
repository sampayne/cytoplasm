<?php
abstract class ModelFactory{
    
    public    static function Create($model){return -1;}
    protected static function SaveToDatabase($model){return -1;}
    public    static function Edit($model){return -1;}
    public    static function Delete($id){return -1;}
    public    static function LoadWithID($id, $idsOnly = NULL){return -1;}
    
    //Should aslo have Create() and LoadWithValues()
           
}