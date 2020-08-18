<?php

// check if called from an allowed page
if (!defined('ESRC')) {
	echo "Do not call the script direct!";
	exit ( 1 );
}


class Testimonials
{
	var $db= null;
	
	public function __construct($database = NULL)
	{
		if (isset($database))
		{
			$this->db = $database;
		}
		else
		{
			// create a new database class instace
			$this->connectDatabase ();
		}
    }
    
	
	/**
	 * Create a new DB connection.
	 */
	private function connectDatabase() {
		$this->db = new Database ();
    }
    
	
	/**
	 * Get testimonials.
	 * @param integer $limit number of testimonials to return
	 * @return array $result
	 */
	public function getTestimonials($limit = 5)
	{
		if (isset($_SESSION['arrTestimonials'])) {
			return $_SESSION['arrTestimonials'];
		}

		$this->db->query("SELECT * FROM testimonials 
							WHERE Approved = 1 
							ORDER BY RescueDate DESC
							LIMIT :limitamt");
        $this->db->bind(":limitamt", $limit);
        $result = $this->db->resultset();
        $this->db->closeQuery();
		
		$_SESSION['arrTestimonials'] = $result;
		return $result;
	}

}
?>