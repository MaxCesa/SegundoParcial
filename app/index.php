<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidoController.php';
require_once './middleware/UserMiddleware.php';
require_once './controllers/LoggerController.php';
require_once './controllers/EncuestaController.php';


session_start();

// Instantiate App
$app = AppFactory::create();
$app->setBasePath("/app");
$app->addRoutingMiddleware();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/tienda', function (RouteCollectorProxy $group) {
	$group->post('/alta', \TiendaController::class . ':Alta');
	$group->post('/consultar', \TiendaController::class . ':Consultar');
});

$app->group('/ventas', function (RouteCollectorProxy $group) {
	$group->post('/alta', \VentaController::class . ':Alta');
	$group->group('/consultar', function (RouteCollectorProxy $group) {
		$group->group('/productos', function (RouteCollectorProxy $group) {
			$group->get('/vendidos', \VentaController::class . ':ConsultarFecha');
			$group->get('/entreValores', \TiendaController::class . ':ProductosEntreValores');
			$group->get('/masVendido', \TiendaController::class . ':MasVendido');
		});
		$group->group('/productos', function (RouteCollectorProxy $group) {
			$group->get('/porUsuario', \VentaController::class . ':ConsultarPorUsuario');
			$group->get('/porProducto', \VentaController::class . ':ConsultarPorTipo');
			$group->get('/ingresos', \VentaController::class . ':ConsultarIngresos');
		});
	});
	$group->put('/modificar', \VentaController::class . ':Modificar');
});



$app->run();