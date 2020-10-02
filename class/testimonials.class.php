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
	 * Create new testimonial
	 * @param string $pilot Name of pilot submitting testimonial
	 * @param bit $anon Anonymize display of testimonial; 1 = anonymous, 0 = not anonymous
	 * @param string $method rescue method, either via rescue cache or search and rescue
	 * @param string $testimonial Text of testimonial
	 */
	public function createTestimonial($pilot, $anon, $method, $testimonial)
	{
		$this->db->beginTransaction();
		$this->db->query("INSERT INTO testimonials (Pilot, Anon, `Type`, Note)
							VALUES (:pilot, :anon, :method, :note)");
		$this->db->bind(":pilot", $pilot);
		$this->db->bind(":anon", intval($anon));
		$this->db->bind(":method", $method);
		$this->db->bind(":note", $testimonial);
		$this->db->execute();
		$this->db->endTransaction();
	}


	/**
	 * Get a single testimonial.
	 * @param int $rowid The ID of the testimonial to retrieve.
	 * @return array $result
	 */
	public function getTestimonial($rowid)
	{
		$this->db->query("SELECT * FROM testimonials WHERE ID = :rowid");
		$this->db->bind(':rowid', $rowid);
		$result = $this->db->single();
		$this->db->closeQuery();

		return $result;
	}
    
	
	/**
	 * Get testimonials.
	 * @param string $type Type of testimonial to return: ESRC, SAR, or Both; defaults to Both
	 * @param string $sort Order to sort testimonial date; defaults to 'DESC'
	 * @param integer $approved Approval status of testimonials; 1 = approved, 0 = unapproved/pending
	 * @param integer $limit Number of testimonials to return; defaults to 5
	 * @return array $result
	 */
	public function getTestimonials($type = 'Both', $sort = 'DESC', $approved = 1, $limit = 5)
	{
		$strType = ($type == 'Both') ? "'ESRC','SAR'" : "'$type'";

		$this->db->query("SELECT * FROM testimonials 
							WHERE Approved = :approved AND `Type` IN ($strType)
							ORDER BY RescueDate $sort LIMIT :limitamt");
		$this->db->bind(":approved", intval($approved));
		$this->db->bind(":limitamt", intval($limit));
        $result = $this->db->resultset();
        $this->db->closeQuery();

		return $result;
	}


	/**
	 * Delete testimonial from db
	 * @param int $rowid ID of database row to delete
	 */
	public function removeTestimonial($rowid)
	{
		$this->db->beginTransaction();
		$this->db->query("DELETE FROM testimonials WHERE ID = :id");
		$this->db->bind(':id', $rowid);
		$this->db->execute();
		$this->db->endTransaction();
	}


	/**
	 * Update existing testimonial
	 * @param string $pilot Name of pilot submitting testimonial
	 * @param bit $anon Anonymize display of testimonial; 1 = anonymous, 0 = not anonymous
	 * @param string $method rescue method, either via rescue cache or search and rescue
	 * @param string $testimonial Text of testimonial
	 */
	public function updateTestimonial($rowid, $pilot, $testimonial, $approved)
	{
		$this->db->beginTransaction();
		$this->db->query("UPDATE testimonials 
							SET Pilot = :pilot, Note = :testimonial, Approved = :approved 
							WHERE ID = :rowid");
		$this->db->bind(':pilot', $pilot);
		$this->db->bind(':testimonial', $testimonial);
		$this->db->bind(':approved', $approved);
		$this->db->bind(':rowid', $rowid);
		$this->db->execute();
		$this->db->endTransaction();
	}

}
?>