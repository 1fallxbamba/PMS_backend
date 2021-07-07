<?php

include '../../config/controller.php';

$_employeeID = isset($_GET['code']) ? $_GET['code'] : die(json_encode(array('ERROR' => 'No Employee Code provided')));

// Get the posted data
$_data = file_get_contents("php://input");

$newClientData = json_decode($_data);

$woman = new AdministratorController();

$woman->deleteEmployee($_employeeID);
