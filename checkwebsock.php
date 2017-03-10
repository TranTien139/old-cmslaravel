<?php
include ('vendor/autoload.php') ;
$client = new Hoa\Websocket\Client(
    new Hoa\Socket\Client('ws://42.112.25.16:8889')
);
$client->setHost('42.112.25.16') ;
$client->on('open', function (Hoa\Event\Bucket $bucket) {
    $bucket->getSource()->send('reload');
    return;
});
$client->connect();
$client->close() ;
              
