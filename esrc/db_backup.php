<?php 
//TODO: took this out of a function, so need to make it work as a standalone script
//TODO: also need to update db calls to use new PDO class
$config = parse_ini_file('../esr_dbconfig.ini');
$mtables = array();
$contents = "-- Database: `".$config['dbname']."` --\n";

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

$backup_file_name = "sql-backup-".date( "Y-m-d--h-i-s").".sql";
	 
$fp = fopen('db_backups/'.$backup_file_name ,'w+');
if (($result = fwrite($fp, $contents))) {
	echo "Backup file created '--$backup_file_name' ($result)"; 
}
fclose($fp);
?>