<?php

include '../../config/controller.php';

$clientID = isset($_GET['code']) ? $_GET['code'] : die(json_encode(array('ERROR' => 'No Client Code provided')));

// Get the posted data
$_data = file_get_contents("php://input");

$newClientData = json_decode($_data);

$woman = new SecretaryController();

$woman->updateClient($clientID,$newClientData);
