<?php

include '../../config/controller.php';

$_projectName = isset($_GET['name']) ? $_GET['name'] : die(json_encode(array('ERROR' => 'No Project Name provided')));

$pmsUser = new PMSController();

$pmsUSer->searchProject($_projectName);

