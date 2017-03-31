<?php
require_once('auth_functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/secret.php');
session_start();
$useragent="EvE-Scout ESRC agent.";
// Make sure that the secret matches the one set before the redirect.
if (isset($_SESSION['auth_state']) and isset($_GET['state']) and $_SESSION['auth_state']==$_GET['state']) {
    $code=$_GET['code'];
    $state=$_GET['state'];
    //Do the initial check.
    //$url='https://sisilogin.testeveonline.com/oauth/token';
    $url='https://login.eveonline.com/oauth/token';
    //$verify_url='https://sisilogin.testeveonline.com/oauth/verify';
    $verify_url='https://login.eveonline.com/oauth/verify';
    $header='Authorization: Basic '.base64_encode($clientid.':'.$secret);
    $fields_string='';
    $fields=array(
                'grant_type' => 'authorization_code',
                'code' => $code
            );
    foreach ($fields as $key => $value) {
        $fields_string .= $key.'='.$value.'&';
    }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);
    if ($result===false) {
        auth_error(curl_error($ch));
    }
    curl_close($ch);
    $response=json_decode($result);
    $auth_token=$response->access_token;
// Get the Character details from SSO
    $ch = curl_init();
    $header='Authorization: Bearer '.$auth_token;
    curl_setopt($ch, CURLOPT_URL, $verify_url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);
    if ($result===false) {
        auth_error(curl_error($ch));
    }
    curl_close($ch);
    $response=json_decode($result);
    if (!isset($response->CharacterID)) {
        auth_error('No character ID returned');
    }
// Lookup the character details in the DB.
    include_once $_SERVER['DOCUMENT_ROOT'].'/class/class.db.php';
    $db = new Db();
    $sql="select corporationname,corporationticker,user.corporationid,
    alliancename,allianceticker,corporation.allianceid,characterid,characterownerhash,
    user.id
    from user 
    join corporation on user.corporationid=corporation.corporationid
    join alliance on corporation.allianceid=alliance.allianceid
    where
    user.characterid=?
    and characterownerhash=?";
    /*
    $stmt = $db->prepare($sql);
    $stmt->execute([$response->CharacterID, $response->CharacterOwnerHash]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //while ($row = $stmt->fetchObject()) {
    foreach ($rows as $value1) {
        
        foreach ($value1 as $value2) {
            echo $value2;
        }
        
        //$userdetails=$row;
        $userdetails=$rows;
        //echo $userdetails.'<br />';
        //$userid=$row->id;
        $userid=$value1['characterid'];
        //echo $userid.'<br />';
    }
    */
// Fill in character details, if they're not in the DB
    if (!isset($userdetails)) {
        // No database entry for the user. lookup time.
        error_log('Creating user details');
        $ch = curl_init();
        $lookup_url="https://api.eveonline.com/eve/CharacterAffiliation.xml.aspx?ids=".$response->CharacterID;
        curl_setopt($ch, CURLOPT_URL, $lookup_url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result===false) {
            auth_error('No such character on the API');
        }
        $xml=simplexml_load_string($result);
        if (isset($xml->result->rowset->row->attributes()["characterID"])) {
            $corporationID=(string)$xml->result->rowset->row->attributes()["corporationID"];
            $corporationName=(string)$xml->result->rowset->row->attributes()["corporationName"];
            $allianceID=(string)$xml->result->rowset->row->attributes()["allianceID"];
            $allianceName=(string)$xml->result->rowset->row->attributes()["allianceName"];
        } else {
            auth_error("No character details returned from API");
        }
    }
    $_SESSION['auth_characterid']=$response->CharacterID;
    //$_SESSION['auth_id']=$userid;
    $_SESSION['auth_charactername']=$response->CharacterName;
    //$_SESSION['auth_userdetails']=json_encode($userdetails);
    $_SESSION['auth_characterhash']=$response->CharacterOwnerHash;
    $_SESSION['auth_charactercorp'] = $corporationName;
    $_SESSION['auth_characteralliance'] = $allianceName;
    session_write_close();
    header('Location:'. $_SESSION['auth_redirect']);
    exit;
} else {
    echo "State is wrong. Did you make sure to actually hit the login url first?";
    error_log($_SESSION['auth_state']);
    error_log($_GET['state']);
}
?>