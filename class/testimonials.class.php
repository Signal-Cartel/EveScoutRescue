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
	 * Get number of pilot activities within a time frame.
	 * @param unknown $pilot name of the pilot
	 * @param unknown $start time frame start
	 * @param unknown $end time frame end
	 * @return mixed
	 */
	public function getTestimonials($limit = 5)
	{
        $this->db->query("SELECT * FROM testimonials 
                                WHERE Approved = 1 
                                ORDER BY RescueDate DESC
                                LIMIT :limitamt");
        $this->db->bind(":limitamt", $limit);
        $result = $this->db->resultset();
        $this->db->closeQuery();
		
		return $result;
	}

}
?>