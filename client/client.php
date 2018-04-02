<?php

require __DIR__.'/vendor/autoload.php';

$client = new \Twitch\Twirp\Example\HaberdasherClient('docker.for.mac.localhost:8080');

$size = new \Twitch\Twirp\Example\Size();
$size->setInches(-1);

try {
    $hat = $client->MakeHat([], $size);

    var_dump($hat->serializeToJsonString());
} catch (\Twirp\Error $e) {
    fwrite(STDERR, $e->code());
}
