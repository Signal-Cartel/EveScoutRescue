<?php


class Discord
{


    function sendMessage($audience, $user, $alert, $message)
    {

        if ($alert == 1) {
            $message = "@here " . $message;
        }


        $data = array("content" => $message, "username" => $user);
        $curl = curl_init($audience);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);


        curl_close($curl);


    }

}

?>