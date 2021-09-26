<?php
// https://dev.evescoutrescue.com/stats/stats_data_crinkle_fest.php
include_once '../class/db.class.php';

//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY

// SHOW ALL SYSTEMS WITH EXPIRED CACHES WITH CLASS AND STATIC INFORMATION
$sql="
SELECT Class, count(CacheID) as number FROM 
	(SELECT 
		c1.*
	FROM
    cache AS c1
	
    INNER JOIN
    
	(SELECT System, MAX(CacheID) AS maxid 
		FROM cache 
		GROUP BY System) c2
		
	ON c1.CacheID = c2.maxid AND c1.System = c2.System AND c1.status = 'Expired') latest
	
	
	INNER JOIN
	wh_systems
	ON
	
	latest.System = wh_systems.System
	
	GROUP BY Class
";


// get database connection

$db = new Database();
$db->query($sql);
$result = $db->resultset();
$db->closeQuery();

print_table($result);


function print_table($result){
		$output = '<h2>No Cache System Classes</h2>';
		$output .= '<table class="table table-bordered"><tr>';
		$table_fields = Array();
		if(count($result) > 0){
			$row = $result[0]; 
			foreach ($row as $field => $value){
					$table_Fields[] = $field;
			}		
			$output .= '
			<table class="table table-bordered">
			<tr>';
			
			foreach ($table_Fields as $title){
				$output .= '<th>' . $title . '</th>';
			}
			$output .= '</tr>';
			
			foreach($result as $row){
				$output .= '<tr>';
				foreach($row as $field => $value)
				{
					$output .= '<td>'. $value .'</td>';
				}	
				$output .= '</tr>';
			}
		}
		else{
		   $output .= '<td align="center">Columns not Found</td></tr>';	
		   
		}
		$output .= '</table>';
		echo $output;
}
?>