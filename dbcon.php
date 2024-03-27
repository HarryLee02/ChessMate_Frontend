<?php

require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;



$factory = (new Factory)
    ->withServiceAccount(__DIR__.'/test-8e2c0-firebase-adminsdk-yn2x7-50469a7b51.json')
    ->withDatabaseUri('https://test-8e2c0-default-rtdb.firebaseio.com/');
    
$database = $factory->createDatabase();

?>