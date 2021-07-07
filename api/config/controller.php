<?php

/**
 * PMS Controller v1.0, @author : 1fallxbamba
 */

require_once __DIR__ . '/vendor/autoload.php';

class PMSController
{
	protected static $_db;

	public function __construct()
	{
		$client = new \MongoDB\Client('mongodb://localhost:27017');

		self::$_db = $client->pms;
	}

	public function searchProject($projectName)
	{
		$_collection = self::$_db->projects;
		try {
			$result = $_collection->findOne(['nom' => $projectName]);
			if ($result == null) {
				self::notify("err", "NPF", "No Project Found for the given name");
			} else {
				echo json_encode($result);
			}
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	public function updateProject($projectID, $newData)
	{
		$_collection = self::$_db->projects;
		try {
			$_collection->updateOne(
				['_id' => new MongoDB\BSON\ObjectId($projectID)],
				['$set' => $newData]
			);
			self::notify("s", "PSU", "Project Successfully Updated");
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e);
		}
	}

	public static function notify($type, $code, $message, $data = null)
	{
		if ($type == 'uerr') {
			echo json_encode(array('RESPONSE' => 'Unexpected-Error', 'CODE' => $code, 'DESCRIPTION' => $message));
		} elseif ($type == 'err') {
			echo json_encode(array('RESPONSE' => 'Request-Error', 'CODE' => $code, 'DESCRIPTION' => $message));
		} else {
			if ($data == null) {
				echo json_encode(array('RESPONSE' => 'Success', 'CODE' => $code, 'MESSAGE' => $message));
			} else {
				echo json_encode(array('RESPONSE' => 'Success', 'CODE' => $code, 'MESSAGE' => $message, 'DATA' => $data));
			}
		}
	}
}

class SecretaryController extends PMSController
{
	private $_collection;
	public function __construct()
	{
		parent::__construct();
	}

	public function addProject($projectData)
	{
		$this->_collection = parent::$_db->projects;
		try {
			$this->_collection->insertOne($projectData);
			parent::notify("s", "PSA", "Project Successfully Added");
		} catch (Exception $e) {
			parent::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	// public function searchProject($projectName)
	// {
	// 	$this->_collection = parent::$_db->projects;
	// 	try {
	// 		$result = $this->_collection->findOne(['nom' => $projectName]);
	// 		if ($result == null) {
	// 			parent::notify("err", "NPF", "No Project Found for the given name");
	// 		} else {
	// 			echo json_encode($result);
	// 		}
	// 	} catch (Exception $e) {
	// 		parent::notify("uerr", "UNEX", $e->getMessage());
	// 	}
	// }

	// public function updateProject($projectID, $newData)
	// {
	// 	$this->_collection = parent::$_db->projects;
	// 	try {
	// 		$this->_collection->updateOne(
	// 			['_id' => new MongoDB\BSON\ObjectId($projectID)],
	// 			['$set' => $newData]
	// 		);
	// 		parent::notify("s", "PSU", "Project Successfully Updated");
	// 	} catch (Exception $e) {
	// 		parent::notify("uerr", "UNEX", $e);
	// 	}
	// }

	public function addClient($clientData)
	{
		$this->_collection = parent::$_db->clients;
		try {
			$this->_collection->insertOne($clientData);
			parent::notify("s", "CSA", "Client Successfully Added");
		} catch (Exception $e) {
			parent::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	public function updateClient($clientID, $newData)
	{
		$this->_collection = parent::$_db->clients;
		try {
			$this->_collection->updateOne(
				['_id' => new MongoDB\BSON\ObjectId($clientID)],
				['$set' => $newData]
			);
			parent::notify("s", "CSU", "Client Successfully Updated");
		} catch (Exception $e) {
			parent::notify("uerr", "UNEX", $e);
		}
	}
}
