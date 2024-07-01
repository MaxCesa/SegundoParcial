<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/TiendaController.php';
require_once './controllers/VentaController.php';
require_once './models/ConfirmarPerfil.php';


session_start();

// Instantiate App
$app = AppFactory::create();
$app->setBasePath("/app");
$app->addRoutingMiddleware();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

$middlewareAdmin = new ConfirmarPerfil(["Admin"]);
$middlewareEmpleado = new ConfirmarPerfil(["Admin", "Empleado"]);


// Routes
$app->group('/tienda', function (RouteCollectorProxy $group) {
	$group->post('/alta', \TiendaController::class . ':Alta')->add(new ConfirmarPerfil(["Admin"]));
	$group->post('/consultar', \TiendaController::class . ':Consultar');
});

$app->group('/ventas', function (RouteCollectorProxy $group) {
	$group->post('/alta', \VentaController::class . ':Alta');
	$group->group('/consultar', function (RouteCollectorProxy $group) {
		$group->group('/productos', function (RouteCollectorProxy $group) {
			$group->get('/vendidos', \VentaController::class . ':ConsultarFecha')->add(new ConfirmarPerfil(["Admin", "Empleado"]));
			$group->get('/entreValores', \TiendaController::class . ':ProductosEntreValores')->add(new ConfirmarPerfil(["Admin", "Empleado"]));
			$group->get('/masVendido', \TiendaController::class . ':MasVendido')->add(new ConfirmarPerfil(["Admin", "Empleado"]));
		});
		$group->group('/productos', function (RouteCollectorProxy $group) {
			$group->get('/porUsuario', \VentaController::class . ':ConsultarPorUsuario')->add(new ConfirmarPerfil(["Admin", "Empleado"]));
			$group->get('/porProducto', \VentaController::class . ':ConsultarPorTipo')->add(new ConfirmarPerfil(["Admin", "Empleado"]));
			$group->get('/ingresos', \VentaController::class . ':ConsultarIngresos')->add(new ConfirmarPerfil(["Admin"]));
		});
	});
	$group->put('/modificar', \VentaController::class . ':Modificar')->add(new ConfirmarPerfil(["Admin"]));
});

$app->post('/registro', \UsuarioController::class . ':CargarUno');

$app->post('/login', \LoggerController::class . ':LogIn');



$app->run();