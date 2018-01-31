<?php

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}
require_once '../class/config.class.php';

class Discord
{
	
	/**
	 * Send a message to a discord channel
	 * 
	 * @param unknown $webhook The URL of the channel (including the token)
	 * @param unknown $user The sending user name
	 * @param unknown $alert Alert the online users
	 * @param unknown $message The message to send
	 * @param unknown $skip_the_gif Should the message embed the user image (default: 0)
	 */
    static function sendMessage($webhook, $user, $alert, $message, $skip_the_gif = 0)
    {
    	// link to avatar image
    	$avatar = 'https://c1.staticflickr.com/5/4280/35737512076_e3911ec89f_o.jpg';
    	
    	// notification alert?
        if ($alert == 1) 
        {
        	// yes
            $message = "<@&273926154845552642> " . $message;
        }

        // set default message data
        $data = array("content" => $message, "username" => $user, "avatar_url"=> $avatar);
        
        // check if we skip the user image 
        if ($skip_the_gif != 1)
        {
        	// no, add the user image link as data (we got one - image)
        	$embeds = array();
        	$images = new stdClass();
        	$urls = new stdClass();
        	$urls->url = "https://media.giphy.com/media/xT8qB3utUzMWqmpH20/200.gif";
        	$images->image = $urls;
        	$embeds[] = $images;
        	$data["embeds"] =$embeds;
        }
        
        // prepare the web hook request
        $curl = curl_init($webhook);
        // set POST request method
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        // set the JSon data
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        // get the return status
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // execute the request
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        // close the request afterwards
        curl_close($curl);

        if ($result===false)
        {
        	return false;
        }
        else
        {
        	return "Posted to discord API\n" . $message ."\n" . json_encode($info);
        }
    }
}

?>