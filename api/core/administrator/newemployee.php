<?php

include '../../config/controller.php';

// Get the posted data
$_data = file_get_contents("php://input");

$_employeeData = json_decode($_data);

$_employeeData->shadow = password_hash($_employeeData->shadow, PASSWORD_DEFAULT);

$admin = new AdministratorController();

$admin->addEmployee($_employeeData);
