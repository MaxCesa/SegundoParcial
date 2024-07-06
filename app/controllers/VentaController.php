<?php

require_once "./models/Venta.php";

class VentaController extends Venta
{



    public static function Alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $retorno = Venta::CargarVenta($parametros['email'], $parametros['nombre'], $parametros['tipo'], $parametros['talla'], $parametros['stock'], $_FILES['foto']);
        $payload = json_encode(array("mensaje" => $retorno));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');

    }

    public static function ConsultarFecha($request, $response, $args)
    {
        if (isset($_GET['fecha']) && $_GET['fecha'] != "") {
            $total = Venta::ConsultarProductosVendidos($_GET['fecha']);
            $payload = json_encode(array("mensaje" => "En {$_GET['fecha']} se vendieron {$total} productos."));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } else {
            $total = Venta::ConsultarProductosVendidos();
            $payload = json_encode(array("mensaje" => "Ayer se vendieron {$total} productos."));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }
    }

    public static function ConsultarPorTipo($request, $response, $args)
    {

        $listado = Venta::ConsultarVentasPorTipo($_GET['tipo']);
        $payload = json_encode(array("mensaje" => "Ventas de tipo {$_GET['tipo']}.", "Payload" => $listado));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');

    }

    public static function ConsultarPorUsuario($request, $response, $args)
    {

        $listado = Venta::ConsultarVentasPorUsuario($_GET['email']);
        $payload = json_encode(array("mensaje" => "Ventas de usuario {$_GET['tipo']}.", "Payload" => $listado));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');

    }

    public static function ConsultarIngresos($request, $response, $args)
    {
        if (isset($_GET['fecha']) && $_GET['fecha'] != "") {
            $total = Venta::GetIngresos($_GET['fecha']);
            $payload = json_encode(array("mensaje" => "En {$_GET['fecha']} se ganaron {$total} pesos."));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } else {
            $total = Venta::ConsultarProductosVendidos();
            $payload = json_encode(array("mensaje" => "En total ganamos {$total} pesos."));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }
    }

    public static function Modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $resultado = Venta::ModificarVenta($parametros['nro_pedido'], $parametros['email'], $parametros['nombre'], $parametros['tipo'], $parametros['talla'], $parametros['stock']);
        if ($resultado > 0) {
            $payload = json_encode(array("mensaje" => "Se actualizo el registro de venta."));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(array("mensaje" => "No se encontro el numero de pedido."));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }

    }

    public static function DescargarCSV($request, $response, $args)
    {
        $file = Venta::GuardarProductosCSV();
        $filename = basename("app\csv\ventas.csv");
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize("app\csv\ventas.csv"));


        readfile($file);
        return $response->withHeader();
    }
}