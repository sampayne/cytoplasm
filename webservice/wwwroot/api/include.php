<?php

header('X-Frame-Options: DENY'); 


//enforceHTTPS();
configure();
includes();


function includes()
{
    $factoryPath = '/../php/factories/';
    $modelPath   = '/../php/models/';
    
        require_once($_SERVER['DOCUMENT_ROOT'] . $factoryPath . 'ModelFactory.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $factoryPath . 'ArticleFactory.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $factoryPath . 'DatabaseFactory.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $factoryPath . 'DataEntryFactory.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $factoryPath . 'DataStreamFactory.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $factoryPath . 'GroupFactory.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $factoryPath . 'LoginFactory.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . $factoryPath . 'TaxonomyFactory.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $factoryPath . 'UserFactory.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $modelPath . 'Model.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $modelPath . 'Article.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $modelPath . 'Database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $modelPath . 'DataStream.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $modelPath . 'Group.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $modelPath . 'DataEntry.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $modelPath . 'Taxonomy.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $modelPath . 'User.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . $modelPath . 'Error.php');
}
function configure()
{
    date_default_timezone_set('Europe/London');
    error_reporting(-1);
  
    ini_set('session.cookie_httponly', '0');
    ini_set('session.use_only_cookies', '0');
    ini_set('session.cookie_secure', '0');
 


    
}
function enforceHTTPS()
{
    if ($_SERVER['HTTPS'] != "on" || !isset($_SERVER["HTTPS"]))
    {
        header("Status: 301 Moved Permanently");
        header(sprintf('Location: https://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']));
        exit();
    }
}
?>