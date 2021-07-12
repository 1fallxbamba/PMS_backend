<?php

include '../../config/controller.php';

$_data = file_get_contents("php://input");

$_credentials = json_decode($_data);

$pmsUser = new PMSController();

$pmsUser->authenticate($_credentials);