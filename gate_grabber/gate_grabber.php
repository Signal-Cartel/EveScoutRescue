<?php
$config = parse_ini_file('../../config/esr_dbconfig.ini');

// check if a config is found
if ($config === FALSE)
{
	echo "<p><b>No DB config found!</b></p>";
	// add error logging here
	exit(1);
}

//define db connection vars
define("DB_HOST", $config['hostname']);
define("DB_USER", $config['username']);
define("DB_PASS", $config['password']);
define("DB_NAME", $config['dbname']);


error_reporting(E_ALL);
ini_set('display_errors', 1);
$user = DB_USER;
$password = DB_PASS;
$db = DB_NAME;
$host = DB_HOST;
$port = 3306;
global $link;

$link = mysqli_connect("$host:$port",$user,$password);
$db_selected = mysqli_select_db($link,$db);


if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

ignore_user_abort(true);

getSystems();

function getSystems()
{
    global $link;

    //get list of systems to run through
    $systems = mysqli_query($link, "SELECT * FROM systems where flag=0 LIMIT 200");
    print_r($systems);
    foreach ($systems as $system) {

        //call system specific cron
        getSysInfo($system['system']);
    }
};

function getSysInfo($system)
{
    global $link;


    $ch = curl_init();
    $request_url = 'https://esi.tech.ccp.is/latest/universe/systems/' . $system . '/';



    curl_setopt($ch, CURLOPT_URL, $request_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);


    $response = curl_exec($ch);

    $data = json_decode($response, true);

    $gateArray = $data['stargates'];

    $sysName = $data['name'];

    //check if gateArray contains gates
    if (count($gateArray) > 0) {
        //get count of gates
        $gateCount=count($gateArray);

        //set gateCount in system for later checking to make sure all gates are captured.
        $gateCountUp = "update systems set gates='" . $gateCount . "' where system='" . $system . "'";


        $systemUp=mysqli_query($link,$gateCountUp) or trigger_error("Query Failed! SQL: $gateCountUp - Error: ".mysqli_error($link), E_USER_ERROR);

//call specific gate infor function for each gate.
        foreach ($gateArray as $key => $gate) {

            $gateCheck="select * from gates where internal_gate_id='" . $gate . "'";
            $gateSetCheck=mysqli_query($link,$gateCheck) or trigger_error("Query Failed! SQL: $gateCheck - Error: ".mysqli_error($link), E_USER_ERROR);
            if($gateSetCheck->num_rows==0) {
                getGateInfo($gate, $system, $sysName);
            }

        }

//check how many gates for the system have been put in db
        $gateSystemCheck="select * from gates where system_id='" . $system . "'";
        $gateCountCheck=mysqli_query($link,$gateSystemCheck) or trigger_error("Query Failed! SQL: $gateSystemCheck - Error: ".mysqli_error($link), E_USER_ERROR);
//make sure gate count is the same as potential gates for the system.
        if($gateCountCheck->num_rows==$gateCount) {
//flag system so its not run again in next batch.
            $systemUpdate = "update systems set flag=1 where system='" . $system . "'";


            $systemUp = mysqli_query($link, $systemUpdate) or trigger_error("Query Failed! SQL: $systemUpdate - Error: " . mysqli_error($link), E_USER_ERROR);

        }

    }
    curl_close($ch);


};


function getGateInfo($gate, $system, $sysName)
{
    global $link;


    $ch2= curl_init();
    $request_url2 = 'https://esi.tech.ccp.is/latest/universe/stargates/' . $gate . '/';


    curl_setopt($ch2, CURLOPT_URL, $request_url2);

    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);


    $response2 = curl_exec($ch2);
    echo $gate . "<br>";



    $data2 = json_decode($response2, true);



    $external_id=$data2['destination']['stargate_id'];

   $external_gate_name=explode(")",explode("(",$data2['name'])[1])[0];
//insert gate specific data into gates table.
    $gateIn = "insert into gates (system_id,system_name,internal_gate_id,external_gate_id,external_gate_name)
values('" . $system . "', '" . $sysName . "','" . $gate . "','" . $external_id . "','" . $external_gate_name . "')";


$gateDown=mysqli_query($link,$gateIn) or trigger_error("Query Failed! SQL: $gateIn - Error: ".mysqli_error($link), E_USER_ERROR);



    curl_close($ch2);


};


?>