<?php

include 'vendor/autoload.php';

function catchException(Throwable $e) {
    echo '[Before] ' . $e::class . ' - ' . $e->getMessage() . PHP_EOL;
}


set_exception_handler('catchException');


$errorHandler = new \ProcessPilot\Client\ErrorHandler((new \ProcessPilot\Client\Factory\PilotClientServiceFactory())());
$errorHandler->register();

class Foo{
    use Bar;
}


trigger_error('lala');
function test(string $a) {return $a;}

//echo test(null);

$result = 1/0;

