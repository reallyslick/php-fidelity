<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . './lib/fidelity.php';



/*MAIN*/
$fi= new fidelity();

$getParams["stocks"] = 'BABA';
$response = $fi->sendGetRequest($getParams);

$resultArray = json_decode($response);

foreach($resultArray as $row)
{
    echo $row;
}




?>