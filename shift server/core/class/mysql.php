<?php
namespace MySQL;

include_once "common.php";

abstract class Database {
	private $pdo_options = [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		\PDO::ATTR_EMULATE_PREPARES => false,
	];
	
	protected $cnf;
	protected $pdo;
	
	public function __construct($env) {
		$this->cnf = new \Common\Config($env);
		
		$dsn = "mysql:host={$this->cnf->cnf_db["servername"]};dbname={$this->cnf ->cnf_db["schema"]};" .
				"charset={$this->cnf->cnf_db["charset"]};port={$this->cnf ->cnf_db["port"]}";
				
		try {
			$this->pdo = new \PDO(
				$dsn
			,	$this->cnf->cnf_db["username"]
			,	$this->cnf->cnf_db["password"]
			,	$this->pdo_options
			);
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}			
	}
	
	public function BeginTran() {
		$this->pdo->beginTransaction();
	}
	
	public function RollbackTran() {
		$this->pdo->rollBack();
	}
	
	public function CommitTran() {
		$this->pdo->commit();
	}
	
	public function __destruct() {
		$this->pdo = null;
	}
}

class Query extends Database {
	public function __construct($env) {
		parent::__construct($env);
	}
	
	public function Execute($sql) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		
		return $stmt->fetchAll();
	}

	public function Run($sql) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		
		return $stmt->rowCount();
	}		
}

