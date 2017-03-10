<?php 
include ('vendor/autoload.php') ;
$websocket = new Hoa\Websocket\Server(
    new Hoa\Socket\Server('ws://42.112.25.16:8889')
);
$websocket->on('open', function (Hoa\Event\Bucket $bucket) {
    echo "> open connect" ."\n" ;
    return;
});
$websocket->on('message', function (Hoa\Event\Bucket $bucket) {
    $data = $bucket->getData();
    echo '> message ', $data['message'], "\n";
    $bucket->getSource()->broadcast('reload');
    echo '< echo', "\n";

    return;
});
$websocket->run();

