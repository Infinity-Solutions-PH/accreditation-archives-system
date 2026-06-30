<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

try {
    $request = Request::create('/accreditation/events/unshare/test/area-x', 'POST');
    $route = Route::getRoutes()->match($request);
    
    echo "Route matched: " . $route->getName() . "\n";
    echo "Parameters before binding: " . json_encode($route->parameters()) . "\n";
    
    // Simulate route model binding
    $app->make('router')->substituteBindings($route);
    echo "Parameters after binding: " . json_encode($route->parameters()) . "\n";
} catch (\Exception $e) {
    echo "Exception caught: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
