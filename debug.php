<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/vendor/autoload.php';

echo "Autoload OK<br>";

use Symfony\Component\HttpFoundation\Request;
use App\Kernel;

$kernel = new Kernel('prod', true); // Mode debug activé
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
