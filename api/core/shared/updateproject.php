<?php

include '../../config/controller.php';

$_projectID = isset($_GET['code']) ? $_GET['code'] : die(json_encode(array('ERROR' => 'No Project Code provided')));

$_data = file_get_contents("php://input");

$newProjectData = json_decode($_data);

$pmsUser = new PMSController();

$pmsUser->updateProject($_projectID, $newProjectData);
