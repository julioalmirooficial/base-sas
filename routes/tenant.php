<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
  'api', // web
  InitializeTenancyByDomain::class,
  PreventAccessFromCentralDomains::class,
])->group(function () {

  Route::get('/', function () {
    return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
  });

  Route::get('/status_tenancy', function () {
    return response()->json([
      'status' => 'Inquilino detectado',
      'tenant' => tenant('id'),
      'subdomain' => request()->getHost(), // Subdominio detectado
    ]);
  });

  // Route::post("register", [AuthController::class, 'register']);
  // Route::post("login", [AuthController::class, 'login']);

});
