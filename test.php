<?php
 
   //Connecting to Redis server on localhost
 
   $redis = new Redis();
 
   $redis->connect('127.0.0.1', 7777);
 
   echo "Connection to server sucessfully";
 
   // Get the stored keys and print it
 
   $arList = $redis->keys("*");
 
   echo "Stored keys in redis:: ";
 
   print_r($arList);
 
?>
