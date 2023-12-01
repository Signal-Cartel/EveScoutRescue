<?php

// check if called from an allowed page
if (!defined('ESRC')) {
	echo "Do not call the script direct!";
	exit ( 1 );
}

include_once '../class/config.class.php';

class Storms
{
	/**
	 * Create new entry into [storm_tracker] table
	 * @param array $arrayStormReport Array of values needed to populate report record
	 */
  public function createStormEntry($character_id, $observation_type, $observed_in_person, $system_name) {
    $data = array(
      "character_id" => intval($character_id),
      "observation_type" => $observation_type,
      "observed_in_person" => $observed_in_person,
      "system_name" => $system_name
    );

    $uri = Config::ES_API_URI . "/v2/private/observations";
    $headers = array(
      "Content-Type: application/json",
      "X-ESRC-Auth: " . Config::ES_API_SECRET,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    switch ($status) {
      case 200:
        $result = true;
        break;
      case 400:
        if ($response) {
          $result = json_decode($response, true);
          $result = $result['error']['name'];
          break;
        }
      default:
        $result = "error";
    }

    return $result;
}

  /**
   * Delete storm report entry from db
   * @param int $rowid ID of database row to delete
   */
  public function removeStormEntry($rowid) {
    $uri = Config::ES_API_URI . "/v2/private/observations/" .
      urldecode(intval($rowid));
    $headers = array(
      "X-ESRC-Auth: " . Config::ES_API_SECRET,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    switch ($status) {
      case 200:
      case 204:
        $result = true;
        break;
      case 400:
        if ($response) {
          $result = json_decode($response, true);
          $result = $result['error']['name'];
          break;
        }
      default:
        $result = "error";
    }

    return $result;
  }

  function sortByCreatedAt($a, $b) {
    $timeA = strtotime($a['created_at']);
    $timeB = strtotime($b['created_at']);
    return $timeB - $timeA;
  }

	/**
	 * Get list of most recent storm reports for each defined storm.
	 * @return array $result
	 */
	public function getRecentReports($scope, $storms_only = true)
	{
    switch ($scope) {
      case "private":
        $uri = Config::ES_API_URI . "/v2/private/observations?latest=true";
        $headers = array(
          "X-ESRC-Auth: " . Config::ES_API_SECRET,
        );
        break;
      case "public":
        $uri = Config::ES_API_URI . "/v2/public/observations";
        $headers = array();
        break;
      default:
        return array();
    }

    $result = array();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if ($response) {
      $result = json_decode($response, true);

      if ($storms_only) {
        // the tracking endpoint returns not only storms but other data as well
        $whitelist = [
          "electric_a",
          "electric_b",
          "exotic_a",
          "exotic_b",
          "gamma_a",
          "gamma_b",
          "plasma_a",
          "plasma_b",
          ];
        $result = array_filter($result, function ($item) use ($whitelist) {
          // Check if the 'observation_type' property is in the whitelist
          return in_array($item['observation_type'], $whitelist);
        });
      }

      usort($result, array('Storms', 'sortByCreatedAt'));
    }
    if (curl_errno($ch)) {
      echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);

    return $result;
	}


	/**
	 * Get name of defined storm based on storm_id.
	 * @return array $result
	 */
	static public function getStormName($id)
	{
		switch ($id) {
			case 1:
      case 'electric_a':
        return 'Electric A';
        break;
			case 2:
      case 'electric_b':
        return 'Electric B';
        break;
			case 3:
      case 'exotic_a':
        return 'Exotic A';
        break;
			case 4:
      case 'exotic_b':
        return 'Exotic B';
        break;
			case 5:
      case 'gamma_a':
        return 'Gamma A';
        break;
      case 'gamma_b':
			case 6:
        return 'Gamma B';
        break;
			case 7:
      case 'plasma_a':
        return 'Plasma A';
        break;
			case 8:
      case 'plasma_b':
        return 'Plasma B';
        break;
      case 'toms_shuttle':
        return 'Space Oddity';
      default:
        return 'unknown';
		}
	}

	
	/**
	 * Get list of storms.
	 * @param string $interval Get storm reports between now and this inteval; defaults to 36 hours
	 * @return array $result
	 */
	public function getStormReports($observation_type)
	{
    $uri = Config::ES_API_URI . "/v2/private/observations?observation_type=" .
        urldecode($observation_type);
    $headers = array(
      "X-ESRC-Auth: " . Config::ES_API_SECRET,
    );

    $result = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if ($response) {
      $result = json_decode($response, true);
    }
    if (curl_errno($ch)) {
      echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);

    usort($result, array('Storms', 'sortByCreatedAt'));

    return $result;
    /*
		switch ($return_type) {
			case 'named':
				$where_clause = 'WHERE storm_id = :storm_id';
			break;

			case 'interval':
				$where_clause = 'WHERE dateobserved >= NOW() - INTERVAL '. $interval;
			break;

			case 'all':
			default:
				$where_clause = 'WHERE 1=1';
		}
		$this->db->query("SELECT st.*, mr.regionName, u.characterid
							FROM storm_tracker st
							INNER JOIN mapSolarSystems mss ON st.evesystem = mss.solarSystemName
							INNER JOIN mapRegions mr ON mss.regionID = mr.regionID
							INNER JOIN `user` u ON st.pilot = u.character_name
							$where_clause
							ORDER BY dateobserved DESC");
		if ($stormid > 0) { $this->db->bind(':storm_id', $stormid); }
        $result = $this->db->resultset();
        $this->db->closeQuery();

		return $result;
    */
	}

	/**
	 * Get most active storm chasing pilot,
		* most recent downtime to prior downtime
		* This is used by discord to award top pilot with icon on name
	 */
	public function getTopStormChaser()
	{
		$sql = "SELECT u.characterid AS `uid`, st.pilot AS Pilot, Count(st.id) As Actions
				FROM storm_tracker st
				INNER JOIN `user` u ON st.pilot = u.character_name			  
				WHERE
					((
						hour(UTC_TIMESTAMP) between 0 and 10 
						and st.dateobserved >= UTC_DATE - interval 37 hour -- 11am two days ago
						and st.dateobserved < UTC_DATE - interval 13 hour -- 11am yesterday
					)
					OR
					(
						hour(UTC_TIMESTAMP) between 11 and 23
						and st.dateobserved >= UTC_DATE - interval 13 hour -- 11am yesterday
						and st.dateobserved < UTC_DATE + interval 11 hour -- today 11am
					))
					# Exclude Discord server mods, because bot does not have permission to rename them
					AND st.pilot NOT IN ('Katia Sae','Thrice Hapus','Johnny Splunk')
				GROUP BY st.pilot
				ORDER BY Actions DESC";

		$this->db->query($sql);
		$result = $this->db->single();		
		$this->db->closeQuery();	

		return $result;
	}
}
