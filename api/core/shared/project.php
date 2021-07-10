<?php

include '../../config/controller.php';

$_projectID = isset($_GET['code']) ? $_GET['code'] : die(json_encode(array('ERROR' => 'No Project Code provided')));

$pmsUser = new PMSController();

$pmsUser->fetchProject($_projectID);

