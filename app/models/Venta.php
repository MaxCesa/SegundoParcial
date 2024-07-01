<?php

require_once "./db/AccesoDatos.php";

class Venta
{
    public static function CargarVenta($email, $nombre, $tipo, $talla, $stock, $foto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaTienda = Tienda::ConsultarTienda($nombre, $tipo);
        if ($consultaTienda['Mensaje'] == "Existe." && $consultaTienda['Stock'] >= $stock) {
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ventas (numero_de_pedido,email,nombre,tipo,talla,stock)
                                                            VALUES (:numero,:email,:nombre,:tipo,:talla,:stock);");
            $consulta->bindValue(":numero", self::NuevoNumeroDePedido());
            $consulta->bindValue(":email", $email);
            $consulta->bindValue(":nombre", $nombre);
            $consulta->bindValue(":tipo", $tipo);
            $consulta->bindValue(":talla", $talla);
            $consulta->bindValue(":stock", $stock);
            $consulta->execute();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE tienda
                                                            SET stock = (stock - :stock)
                                                            WHERE id = :id;");
            $consulta->bindValue(":stock", $stock);
            $consulta->bindValue(":id", $consultaTienda['id']);
            $consulta->execute();
            $retorno = "Venta exitosa.";

            $dir_subida = './ImagenesDeVenta/2024/';
            if (!file_exists($dir_subida)) {
                mkdir($dir_subida, 0777, true);
                echo 'Se creó el directorio';
            }
            $fecha = date('Y-m-d');
            $nombrearchivo = $nombre . "-" . $tipo . "-" . $talla . "-" . explode("@", $email)[0] . " " . $fecha;
            if (move_uploaded_file($foto['tmp_name'], $dir_subida . $nombrearchivo . '.jpg')) {
                echo "Se creó correctamente el archivo";
            } else {
                echo "¡Error!\n";
            }
        } else {
            $retorno = "No se pudo llevar a cabo la venta.";
        }
        return $retorno;
    }

    public static function NuevoNumeroDePedido()
    {
        return substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
    }

    public static function ConsultarProductosVendidos($dia = 1)
    {
        if ($dia == 1) {
            $dia = date("Y-m-d", strtotime("-1 days"));
        }
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT SUM(stock) as total from ventas
                                                        WHERE DATE(fecha) = :dia;");
        $consulta->bindValue(":dia", $dia);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function ConsultarVentasPorUsuario($email)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * from ventas
                                                        WHERE email = :email;");
        $consulta->bindValue(":email", $email);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ConsultarVentasPorTipo($tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * from ventas
                                                        WHERE tipo = :tipo;");
        $consulta->bindValue(":tipo", $tipo);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function GetIngresos($fecha = 1)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if ($fecha == 1) {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT SUM(v.cantidad_vendidas * p.precio) AS ingresos
                                                        FROM ventas v
                                                        JOIN productos p ON v.nombre = p.nombre AND v.tipo = p.tipo AND v.talla = p.talla;
                                                        ");
        } else {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT SUM(v.cantidad_vendidas * p.precio) AS ingresos
                                                        FROM ventas v
                                                        JOIN productos p ON v.nombre = p.nombre AND v.tipo = p.tipo AND v.talla = p.talla;
                                                        WHERE v.fecha = :fecha;
                                                        ");
            $consulta->bindValue(":fecha", $fecha);
        }
        $consulta->execute();
        return $consulta->fetch()['ingresos'];
    }

    public static function ModificarVenta($nro_pedido, $email, $nombre, $tipo, $talla, $stock)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE ventas
                                                        SET email = :email, nombre = :nombre, tipo = :tipo, talla = :talla, stock = :stock
                                                        WHERE numero_de_pedido = :nro_pedido");
        $consulta->bindValue(":nro_pedido", $nro_pedido);
        $consulta->bindValue(":email", $email);
        $consulta->bindValue(":nombre", $nombre);
        $consulta->bindValue(":tipo", $tipo);
        $consulta->bindValue(":talla", $talla);
        $consulta->bindValue(":stock", $stock);
        $consulta->execute();
        return $consulta->rowCount();
    }
}