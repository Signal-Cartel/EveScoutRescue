<?php
// use database class
require_once '../class/db.class.php';

class Caches
{
	var $db = null;

	public function __construct()
	{
		// create a new database class instace
		$this->db = new Database();
	}

	/**
	 * Get number of total active caches
	 * @return number of all activ caches
	 */
	public function getActiveCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM cache WHERE Status <> 'Expired'");
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
	}
	
	/**
	 * Get hash with number of all actions
	 */
	public function getActionTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity");
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
	}

	/**
	 * Get hash with number of 'sown' actions
	 */
	public function getSownTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity WHERE EntryType = 'sower'");
		$result = $this->db->single();
	
		$this->db->closeQuery();
	
		return $result['cnt'];
	}
	
	/**
	 * Get hash with number of 'tend' actions
	 */
	public function getTendTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity WHERE EntryType = 'tender'");
		$result = $this->db->single();
	
		$this->db->closeQuery();
	
		return $result['cnt'];
	}
	
	/**
	 * Get hash with number of 'tend' actions
	 */
	public function getRescueTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity WHERE EntryType = 'adjunct'");
		$result = $this->db->single();
	
		$this->db->closeQuery();
	
		return $result['cnt'];
	}
	
	
}
?>