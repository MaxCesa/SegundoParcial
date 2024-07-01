<?php

require_once "./models/Tienda.php";

class TiendaController extends Tienda
{
    public static function Alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (
            isset($parametros['nombre']) && $parametros['nombre'] != "" &&
            isset($parametros['precio']) && $parametros['precio'] != "" &&
            ($parametros['tipo'] == "Camiseta" || $parametros['tipo'] == "Pantalon") &&
            ($parametros['talla'] == "S" || $parametros['talla'] == "M" || $parametros['talla'] == "L") &&
            isset($parametros['color']) && $parametros['color'] != "" &&
            isset($parametros['stock']) && $parametros['stock'] != ""
        ) {
            $retorno = Tienda::SubirProducto($parametros['nombre'], $parametros['precio'], $parametros['tipo'], $parametros['talla'], $parametros['color'], $parametros['stock']);
            if ($retorno > 0) {
                $payload = json_encode(array("mensaje" => "Tienda actualizada"));
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(array("mensaje" => "Error actualizando tienda"));
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json');
            }
        } else {
            $payload = json_encode(array("mensaje" => "Parametros invalidos"));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }
    }

    public static function Consultar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if (
            isset($parametros['nombre']) && $parametros['nombre'] != "" &&
            ($parametros['tipo'] == "Camiseta" || $parametros['tipo'] == "Pantalon") &&
            isset($parametros['color']) && $parametros['color'] != ""
        ) {
            $payload = json_encode(array("mensaje" => Tienda::ConsultarTienda($parametros['nombre'], $parametros['tipo'], $parametros['color'])["Mensaje"]));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(array("mensaje" => "Parametros invalidos"));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }
    }

    public static function ProductosEntreValores($request, $response, $args)
    {
        if (
            isset($_GET['valor1']) && $_GET['valor1'] != "" &&
            isset($_GET['valor2']) && $_GET['valor2'] != ""
        ) {
            $listado = Tienda::ConsultarProductosEntreValores($_GET['valor1'], $_GET['valor2']);
            $payload = json_encode(array("mensaje" => "Ventas entre {$_GET['valor1']} y {$_GET['valor2']}.", "Payload" => $listado));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } else {
            return Utilities::ParametrosInvalidos($response);
        }
    }

    public static function MasVendido($request, $response, $args)
    {
        $resultado = Tienda::GetProductoMasVendido();
        $payload = json_encode(array("mensaje" => "El producto mas vendido es {$resultado['tipo']} {$resultado['nombre']} {$resultado['talla']}."));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}