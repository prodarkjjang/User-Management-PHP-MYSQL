<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {

    // call discord api / auth

    if (isset($_GET['submit'])) {
        // $test_guildid = "462296082794151986";
        // $cysm_guildid = "495453270144319488";
        // $bot_token = "NzY5NTA1NjIxODk1NTQ0ODUz.X5P_9A.F8hnTbM2I-IpP0lpfQrQJ3EH2SM";
        // $mytoken = "MTgyMDk1MTAzODYxNTg3OTY4.X7FKgw.dswn_H4lG4JKt9Fw6L6TOfOO_ws";
        // $client_id = "769505621895544853";
        // $client_secret = "JTWUwe0n3s5ucgub0GUPqKQfDqfmuCmJ";

        // $url= "https://discord.com/api/oauth2/authorize?response_type=code";
        // $url.= "&client_id=".$client_id;
        // $url.= "&scope=identify%20guilds";
        // $url.= "&redirect_uri=".urlencode("https://www.google.com");
        // $url.= "&prompt=consent";

        // echo $url;
        // $members_json = file_get_contents($url);
        // $members_array = json_decode($members_json, true);
        // $maps_array['results'][0]['geometry'];
        
      

        // 
        define('OAUTH2_CLIENT_ID', 'REDACTED');
        define('OAUTH2_CLIENT_SECRET', 'REDACTED');

        $guildid = "462296082794151986";

        $authorizeURL = 'https://discord.com/api/oauth2/authorize?client_id=769505621895544853&permissions=0&redirect_uri=https%3A%2F%2Fwww.google.com&response_type=code&scope=identify%20guilds%20bot';
        $tokenURL = 'https://discordapp.com/api/oauth2/token';
        $apiURLBase = 'https://discordapp.com/api/users/@me';
        $apiURLGuilds = 'https://discordapp.com/api/users/@me/guilds';
        $apiURLJoin = 'https://discordapp.com/api/guilds/'. $guildid . '/members/';
        $apiURLGuildsMembers = 'https://discordapp.com/api/guilds/462296082794151986/members';



        $url = $apiURLGuildsMembers;

        $params = 'access_token='.session('access_token');
        
        $ch = curl_init($url);
      
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      
        $bottoken = redacted;
        $headers[] = 'Authorization: Bot ' . $bottoken;
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Content-Length: '.strlen($params);
        
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = curl_exec($ch);
      
        echo 'join guild response <br />';
        var_dump($response);
        
      
        if($http == 201){
          return true;
        } else {
          return false;
        }
        

    };
}
