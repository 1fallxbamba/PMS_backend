<?php

include '../../config/controller.php';

// Get the posted data
$_data = file_get_contents("php://input");

$projectData = json_decode($_data);

$woman = new SecretaryController();

$woman->addProject($projectData);
