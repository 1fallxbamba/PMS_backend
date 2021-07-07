<?php

require_once __DIR__ .'/vendor/autoload.php';

$client = new \MongoDB\Client('mongodb://localhost:27017');

$db = $client->test;

$col = $db->first;

// $result = $col->insertOne([ 'firstName' => 'Ibou', 'lastName' => 'Ndiaye', 'phones' => json_encode(array('SN' => 788888888, 'TN' => 3324557765)) ]);

// echo "Inserted object with id ". $result->getInsertedId();

$result = $col->find([]);

$res = array();

foreach ($result as $document) {
    // print_r($document['firstName']);
    array_push($res, $document);
 }

 echo json_encode($res);



