<?php
class Db extends PDO {
	public function __construct($dbconfig = '../../config/esr_dbconfig.ini') {
		if (!$config = parse_ini_file($dbconfig)) 
			throw new Exception('Unable to open: '.$dbconfig.'.');
		$dsn = 'mysql:host='.$config['hostname']. 
			   ';dbname='.$config['dbname'].
			   ';charset=utf8mb4';
		parent::__construct($dsn, $config['username'], $config['password']);
	}
}