

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


//db settings
$user = 'root';
$password = 'root';
$db = 'eve';
$host = 'localhost';
$port = 3306;
global $link;

$link = mysqli_connect("$host:$port", $user, $password);
$db_selected = mysqli_select_db($link, $db);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


//discord variables

//these are the app keys for dev server, they must be changed to live server
$client_key = '25962414413709313';
$client_secret = 'KWdxtyIM1wEcQUwz_CNzGzcgjlYFYAAR';

//these are the app keys for wildburn's server
$client_key='326084724542144523';
$client_secret='RBGSC9gUJ3558ibEAoFWKLziuXE7mNeN';




//general channel for sc dev server
$channel_id = '325628205014712321';


//general channel for wildburn's server
$channel_id='327982154564763651';




//this is the code for the dev server, it must be changed to live server


//sc dev guild
$guild = '325628205014712321';

//wildburns guild
$guild = '327982154564763650';



//sc dev bot
$botToken='MzI1OTYyNDE0NDEzNzA5MzEz.DC7y-w.fn--Bl-q8y2arxMqhbpxkZBm1x8';


//wildburns bot
$botToken='MzI2MDg0NzI0NTQyMTQ0NTIz.DC9RBg.zMTBD7CRjKYzg4WO3X3G6mglsbQ';

//generate Oath Token


getChan();

function getChan()
{
    global $client_key;
    global $client_secret;
    global $guild;
global $botToken;

    $url = 'https://discordapp.com/api/guilds/' . $guild . "/channels";
    //$url = 'https://discordapp.com/api/gateway';
    $chB = curl_init();
    $post_opts = array(
        'client_id' => $client_key,
        'client_secret' => $client_secret,
        'code' => $guild,
        'redirect_uri' => 'http://www.eve-scout.com/discord_output.php',
        'grant_type' => 'authorization_code',
    );
    $f = fopen('request.txt', 'w');
    $c_opts = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_VERBOSE        => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_STDERR         => $f,

        CURLOPT_HTTPHEADER => array('Authorization :  Bot ' . $botToken ),

    );

    curl_setopt_array($chB, $c_opts);

    $run_now = curl_exec($chB);
    $code = curl_getinfo($chB, CURLINFO_HTTP_CODE);
    fclose($f);
    curl_close($chB);
    print($code);
    var_dump($run_now);
    exit;
    //sendMessage($code['token']);
}


function sendMessage($token)
{
    global $link;
    global $channel_id;
    global $botToken;
    $url = 'https://discordapp.com/api/channels/' . $channel_id . '/messages';

    $ch = curl_init();
    $f = fopen('request.txt', 'w');
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => array('Authorization :  Bot ' . $botToken ),
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_VERBOSE => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_STDERR => $f,
    ));
    $response = curl_exec($ch);
    fclose($f);
    curl_close($ch);


}




?>
