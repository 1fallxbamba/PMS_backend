<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');

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

	public function authenticate($credentials)
	{
		$_collection = self::$_db->employees;
		try {
			$result = $_collection->findOne(['login' => $credentials->login]);
			if ($result == null) {
				self::notify("err", "UDNE", "User Does Not Exist : the given login does not exist.");
			} else {
				if (password_verify($credentials->passwd, $result['shadow'])) {
					self::notify("s", "USA", "User Successfully Authenticated", array(
						'id' => $result['_id'],
						'profile' => $result['profile'],
						'lName' => $result['lName'],
						'fName' => $result['fName'],
						'email' => $result['email']
					));
				} else {
					self::notify("err", "WPWD", "Wrong Password : The entered password is incorrect.");
				}
			}
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	public function fetchEmployeeInfo($employeeID)
	{
		$_collection = self::$_db->employees;
		try {
			$result = $_collection->findOne(
				['_id' => new MongoDB\BSON\ObjectId($employeeID)],
				['projection' => [
					'name' => 1,
					'fName' => 1,
					'tel' => 1,
					'email' => 1,
					'profile' => 1
				]]
			);
			if ($result == null) {
				self::notify("err", "ENF", "Employee Not Found : the given id may be inexistant.");
			} else {
				self::notify("s", "EDF", "Employee Data Found", $result);
			}
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	public function fetchClientNames()
	{
		$_collection = self::$_db->clients;
		try {
			$result = $_collection->find([], ['projection' => ['name' => 1]]);
			if ($result == null) {
				self::notify("err", "NCF", "No Clients Found.");
			} else {

				$res = array();

				foreach ($result as $document) {
					array_push($res, $document->name);
				}
				self::notify("s", "CF", "Clients Found", $res);
			}
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	public function fetchProjects()
	{
		$_collection = self::$_db->projects;
		try {
			$result = $_collection->find();

			if ($result == null) {
				self::notify("err", "NPF", "No Projects Found.");
			} else {

				$res = array();

				foreach ($result as $document) {
					array_push($res, $document);
				}
				self::notify("s", "PF", "Projects Found", $res);
			}
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	public function fetchProject($projectID)
	{
		$_collection = self::$_db->projects;
		try {
			$result = $_collection->findOne(['_id' => new MongoDB\BSON\ObjectId($projectID)]);

			self::notify("s", "PDF", "Project Data Found", $result);
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	public function searchProject($projectName)
	{
		$_collection = self::$_db->projects;
		try {
			$result = $_collection->find(['name' => new \MongoDB\BSON\Regex($projectName)]);
			if ($result == null) {
				self::notify("err", "NPF", "No Project Found for the given name");
			} else {
				$res = array();

				foreach ($result as $document) {
					array_push($res, $document);
				}

				self::notify("s", "PDF", "Project Data Found", $res);
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

	public function fetchClients()
	{
		$_collection = parent::$_db->clients;
		try {
			$result = $_collection->find();

			if ($result == null) {
				self::notify("err", "NCF", "No Clients Found.");
			} else {

				$res = array();

				foreach ($result as $document) {
					array_push($res, $document);
				}
				self::notify("s", "CF", "Clients Found", $res);
			}
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e->getMessage());
		}
	}

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

class AdministratorController extends PMSController
{
	private $_collection;

	public function __construct()
	{
		parent::__construct();
		$this->_collection = parent::$_db->employees;
	}

	public function addEmployee($employeeData)
	{
		try {
			$this->_collection->insertOne($employeeData);
			parent::notify("s", "ESA", "Employee Successfully Added");
		} catch (Exception $e) {
			parent::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	public function updateEmployee($employeeID, $newData)
	{
		try {
			$this->_collection->updateOne(
				['_id' => new MongoDB\BSON\ObjectId($employeeID)],
				['$set' => $newData]
			);
			parent::notify("s", "USU", "User Successfully Updated");
		} catch (Exception $e) {
			parent::notify("uerr", "UNEX", $e);
		}
	}

	public function fetchEmployees()
	{
		$_collection = parent::$_db->employees;
		try {
			$result = $_collection->find();

			if ($result == null) {
				self::notify("err", "NEF", "No Employee Found.");
			} else {

				$res = array();

				foreach ($result as $document) {
					array_push($res, $document);
				}
				self::notify("s", "EF", "Employees Found", $res);
			}
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e->getMessage());
		}
	}

	public function deleteEmployee($employeeID)
	{
		try {
			$this->_collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($employeeID)]);
			parent::notify("s", "USD", "User Successfully Deleted");
		} catch (Exception $e) {
			parent::notify("uerr", "UNEX", $e);
		}
	}
}

class DirectorController extends PMSController
{

	public function fetchManagersInfo()
	{
		$_collection = parent::$_db->employees;
		try {
			$result = $_collection->find(['profile' => 'Chef de projet'], ['projection' => ['fName' => 1, 'lName' => 1]]);

			if ($result == null) {
				self::notify("err", "NMF", "No Manager Found.");
			} else {

				$res = array();

				foreach ($result as $document) {
					array_push($res, $document);
				}

				self::notify("s", "MDF", "Manager Data Found", $res);

			}
		} catch (Exception $e) {
			self::notify("uerr", "UNEX", $e->getMessage());
		}
	}
}
