<?
class Db {
    // The database connection
    protected static $connection;

    /**
     * Connect to the database
     * 
     * @return bool false on failure / mysqli MySQLi object instance on success
     */
    public function connect() {    
        // Try and connect to the database
        if(!isset(self::$connection)) {
            // Load configuration as an array. Use the actual location of your configuration file
            $config = parse_ini_file('../../config/esr_dbconfig.ini');

			mysqli_report(MYSQLI_REPORT_STRICT);

			try {
				 self::$connection = new mysqli($config['hostname'],$config['username'],$config['password'],$config['dbname']);
			} 
			catch (Exception $e ) {
				 echo "Service unavailable\r\n";
				 echo "message: " . $e->getMessage();   // do not show error in live code
				 trigger_error("Error: " . mysqli_error(), E_USER_ERROR); //do not show error in live code
				 exit;
			}
        }

        return self::$connection;
    }

    /**
     * Query the database
     *
     * @param $query The query string
     * @return mixed The result of the mysqli::query() function
     */
    public function query($query) {
		// Connect to the database
		$connection = $this -> connect();
		
        // Query the database
        $result = $connection -> query($query) or trigger_error("Query Failed! SQL: $query - Error: ".mysqli_error(), E_USER_ERROR); //do not show error in live code

        return $result;
    }

    /**
     * Fetch rows from the database (SELECT query)
     *
     * @param $query The query string
     * @return bool False on failure / array Database rows on success
     */
    public function select($query) {
        $rows = array();
        $result = $this -> query($query);
        if($result === false) {
            return false;
        }
        //while ($row = $result -> fetch_assoc()) {
		while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
	
	    /**
     * Quote and escape value for use in a database query
     *
     * @param string $value The value to be quoted and escaped
     * @return string The quoted and escaped string
     */
    public function quote($value) {
        $connection = $this -> connect();
        return "'" . $connection -> real_escape_string($value) . "'";
    }
	
	public function getLastID() {
        $connection = $this -> connect();
        return $connection -> insert_id;
    }
	
	public function __backup_mysql_database($params)
	{
		$config = parse_ini_file('../esr_dbconfig.ini');
		$mtables = array(); $contents = "-- Database: `".$config['dbname']."` --\n";

		$results = $this -> query("SHOW TABLES");
		
		while($row = $results->fetch_array()){
			if (!in_array($row[0], $params['db_exclude_tables'])){
				$mtables[] = $row[0];
			}
		}

		foreach($mtables as $table){
			$contents .= "-- Table `".$table."` --\n";
			
			$results = $this -> query("SHOW CREATE TABLE ".$table);
			while($row = $results->fetch_array()){
				$contents .= $row[1].";\n\n";
			}

			$results = $this -> query("SELECT * FROM ".$table);
			$row_count = $results->num_rows;
			$fields = $results->fetch_fields();
			$fields_count = count($fields);
			
			$insert_head = "INSERT INTO `".$table."` (";
			for($i=0; $i < $fields_count; $i++){
				$insert_head  .= "`".$fields[$i]->name."`";
					if($i < $fields_count-1){
							$insert_head  .= ', ';
						}
			}
			$insert_head .=  ")";
			$insert_head .= " VALUES\n";        
					
			if($row_count>0){
				$connex = $this -> connect();
				$r = 0;
				while($row = $results->fetch_array()){
					if(($r % 400)  == 0){
						$contents .= $insert_head;
					}
					$contents .= "(";
					for($i=0; $i < $fields_count; $i++){
						$row_content =  str_replace("\n","\\n",$connex->real_escape_string($row[$i]));
						
						switch($fields[$i]->type){
							case 8: case 3:
								$contents .=  $row_content;
								break;
							default:
								$contents .= "'". $row_content ."'";
						}
						if($i < $fields_count-1){
								$contents  .= ', ';
							}
					}
					if(($r+1) == $row_count || ($r % 400) == 399){
						$contents .= ");\n\n";
					}else{
						$contents .= "),\n";
					}
					$r++;
				}
			}
		}

		$backup_file_name = "sql-backup-".date( "d-m-Y--h-i-s").".sql";
			 
		$fp = fopen('db_backups/'.$backup_file_name ,'w+');
		if (($result = fwrite($fp, $contents))) {
			echo "Backup file created '--$backup_file_name' ($result)"; 
		}
		fclose($fp);
	}
}
?>