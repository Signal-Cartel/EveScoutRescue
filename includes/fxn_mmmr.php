<?php
/**
 * Get Mean, Median, Mode, or Range of an array of integers
 * @param array $array - array of integers
 * @param string $output - string specifying 'mean', 'median', 'mode', or 'range' (default = 'mean')
 * @return number $total - mean, median, mode, or range value, as specified
 */
function mmmr($array, $output = 'mean')
{
    if(!is_array($array) || empty($array)){
        return FALSE;
    } 
    else {
		$non_zero_arr = Array();
		foreach ($array as $key => $value) {
		  if ($value != 0) {
			$non_zero_arr[]=$value;
		  }
		}
				
        switch($output){
            case 'mean':
                $total = array_sum($array) / count($array);
            break;

            case 'mean-not0':
                $total = array_sum($non_zero_arr) / count($non_zero_arr);
            break;

			
            case 'median':
			
            	$count = count($array);
            	asort($array);
                // get the mid-point keys (1 or 2 of them)
                $mid  = floor(($count - 1) / 2);
                $keys = array_slice(array_keys($array), $mid, (1 === $count % 2 ? 1 : 2));
                $sum  = 0;
                foreach ($keys as $key) {
                	$sum += $array[$key];
                }
                $total = $sum / count($keys);
				break;

            case 'median-not0':
			
            	$count = count($non_zero_arr);
            	asort($non_zero_arr);
                // get the mid-point keys (1 or 2 of them)
                $mid  = floor(($count - 1) / 2);
                $keys = array_slice(array_keys($non_zero_arr), $mid, (1 === $count % 2 ? 1 : 2));
                $sum  = 0;
                foreach ($keys as $key) {
                	$sum += $non_zero_arr[$key];
                }
                $total = $sum / count($keys);
				break;
				
            case 'mode':
                $v = array_count_values($array);
                arsort($v);
                foreach($v as $k => $v){
					$total = $k; 
					break;
				}
				break;			
            case 'mode-not0':
                $v = array_count_values($array);
                arsort($v);
                foreach($v as $k => $v){
					if ($k > 0){
						$total = $k; 
						break;
					}
				}
				break;

            case 'modecnt':
				//array_count_values() returns an array using the values of array as keys and their frequency in array as values
            	$v = array_count_values($array);
            	arsort($v);//descending sort
            	foreach($v as $k => $v){
						$total = $v; 
						break;
				}
            	break;

			case 'modecnt-not0':
				//array_count_values() returns an array using the values of array as keys and their frequency in array as values
            	$v = array_count_values($array);
            	arsort($v);//descending sort
            	foreach($v as $k => $v){
					if ($k > 0){
						$total = $v; 
						break;
					}
				}
            	break;
				
            case 'range':
                sort($array);
                $sml = $array[0];
                rsort($array);
                $lrg = $array[0];
                $total = $lrg - $sml;
            break;
        }
        return $total;
    }
} 
?>