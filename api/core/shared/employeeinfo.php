<?php

include '../../config/controller.php';

$_employeeID = isset($_GET['code']) ? $_GET['code'] : die(json_encode(array('ERROR' => 'No Employee Code provided')));

$pmsUser = new PMSController();

$pmsUser->fetchEmployeeInfo($_employeeID);

