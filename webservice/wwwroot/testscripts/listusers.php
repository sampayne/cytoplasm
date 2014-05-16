<?php


require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');

$sqlString = "UPDATE cyUser SET password='B109F3BBBC244EB82441917ED06D618B9008DD09B3BEFD1B5E07394C706A8BB980B1D7785E5976EC049B46DF5F1326AF5A2EA6D103FD07C95385FFAB0CACBC86' WHERE password != 'B109F3BBBC244EB82441917ED06D618B9008DD09B3BEFD1B5E07394C706A8BB980B1D7785E5976EC049B46DF5F1326AF5A2EA6D103FD07C95385FFAB0CACBC86'";
DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, NULL);

$sqlString = 'SELECT * FROM cyUser';
$results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);

foreach($results as $result){
    
    echo $result['id'], ' ', $result['username'], ' ' , $result['password'], '<br>';
    
    
}



?>