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
require_once './controllers/UsuarioController.php';
require_once './controllers/LoggerController.php';
require_once './middleware/VerificarParametros.php';


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
	$group->post('/alta', \TiendaController::class . ':Alta')->add(new VerificarParametros(["nombre", "precio", "tipo", "talla", "color", "stock"], "POST", false))->add(new ConfirmarPerfil(["Admin"]));
	$group->post('/consultar', \TiendaController::class . ':Consultar')->add(new VerificarParametros(["nombre", "tipo", "color"], "POST", false));
});

$app->group('/ventas', function (RouteCollectorProxy $group) {
	$group->post('/alta', \VentaController::class . ':Alta')->add(new VerificarParametros(["nombre", "email", "tipo", "talla", "foto", "stock"], "POST", true));
	$group->group('/consultar', function (RouteCollectorProxy $group) {
		$group->group('/productos', function (RouteCollectorProxy $group) {
			$group->get('/vendidos', \VentaController::class . ':ConsultarFecha')->add(new ConfirmarPerfil(["Admin", "Empleado"]));
			$group->get('/entreValores', \TiendaController::class . ':ProductosEntreValores')->add(new VerificarParametros(["valor1", "valor2"], "GET", false))->add(new ConfirmarPerfil(["Admin", "Empleado"]));
			$group->get('/masVendido', \TiendaController::class . ':MasVendido')->add(new ConfirmarPerfil(["Admin", "Empleado"]));
		});
		$group->group('/productos', function (RouteCollectorProxy $group) {
			$group->get('/porUsuario', \VentaController::class . ':ConsultarPorUsuario')->add(new VerificarParametros(["email"], "GET", false))->add(new ConfirmarPerfil(["Admin", "Empleado"]));
			$group->get('/porProducto', \VentaController::class . ':ConsultarPorTipo')->add(new VerificarParametros(["tipo"], "GET", false))->add(new ConfirmarPerfil(["Admin", "Empleado"]));
			$group->get('/ingresos', \VentaController::class . ':ConsultarIngresos')->add(new ConfirmarPerfil(["Admin"]));
		});
	});
	$group->put('/modificar', \VentaController::class . ':Modificar')->add(new VerificarParametros(["nombre", "email", "tipo", "talla", "nro_pedido", "stock"], "PUT", false))->add(new ConfirmarPerfil(["Admin"]));
	$group->get('/descargar', \VentaController::class . ':DescargarCSV')->add(new ConfirmarPerfil(["Admin"]));
});

$app->post('/registro', \UsuarioController::class . ':CargarUno')->add(new VerificarParametros(["perfil", "email", "usuario", "clave", "foto"], "POST", true));

$app->post('/login', \LoggerController::class . ':LogIn')->add(new VerificarParametros(["username", "pw"], "POST", false));



$app->run();