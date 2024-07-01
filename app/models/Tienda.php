<?php
require_once "Venta.php";
require_once "./db/AccesoDatos.php";
class Tienda
{

    public static function SubirProducto($nombre, $precio, $tipo, $talla, $color, $stock)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id from tienda where nombre = :nombre AND tipo = :tipo");
        $consulta->bindValue(":tipo", $tipo);
        $consulta->bindValue(":nombre", $nombre);
        $consulta->execute();
        if ($consulta->rowCount() > 0) {
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE tienda 
                                                            SET stock += :stock
                                                            WHERE nombre = :nombre AND tipo = :tipo");
            $consulta->bindValue(":tipo", $tipo);
            $consulta->bindValue(":nombre", $nombre);
            $consulta->execute();
        } else {
            $consulta = $objAccesoDatos->prepararConsulta("INSERT into tienda (nombre,precio,tipo,talla,color,stock) VALUES (:nombre,:precio,:tipo,:talla,:color,:stock)");
            $consulta->bindValue(":tipo", $tipo);
            $consulta->bindValue(":nombre", $nombre);
            $consulta->bindValue(":talla", $talla);
            $consulta->bindValue(":precio", $precio);
            $consulta->bindValue(":color", $color);
            $consulta->bindValue(":stock", $stock, PDO::PARAM_INT);
            $consulta->execute();
        }
        return $consulta->rowCount();
    }

    public static function ConsultarTienda($nombre, $tipo, $color = 1)
    {
        $retorno = array();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if ($color == 1) {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id, stock from tienda where nombre = :nombre AND tipo = :tipo");
        } else {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id, stock from tienda where nombre = :nombre AND tipo = :tipo AND color = :color");
            $consulta->bindValue(":color", $color);
        }
        $consulta->bindValue(":tipo", $tipo);
        $consulta->bindValue(":nombre", $nombre);
        $consulta->execute();
        if ($consulta->rowCount() == 0) {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT
                                                            (SELECT COUNT(*) FROM tu_tabla WHERE tipo = :tipo) > 0 AS tipo_existe,
                                                            (SELECT COUNT(*) FROM tu_tabla WHERE nombre = :nombre) > 0 AS nombre_existe;");
            $consulta->bindValue(":tipo", $tipo);
            $consulta->bindValue(":nombre", $nombre);
            $consulta->execute();
            if ($consulta->fetch()['tipo_existe'] == 0 && $consulta->fetch()['nombre_existe'] == 0) {
                $retorno = ["Mensaje" => "No existe el nombre ni el tipo.", "Stock" => 0, "id" => 0];
            } else if ($consulta->fetch()['tipo_existe'] == 0) {
                $retorno = ["Mensaje" => "No existe el tipo.", "Stock" => 0, "id" => 0];
            } else if ($consulta->fetch()['nombre_existe'] == 0) {
                $retorno = ["Mensaje" => "No existe el nombre.", "Stock" => 0, "id" => 0];
            } else {
                $retorno = ["Mensaje" => "No existe el color.", "Stock" => 0, "id" => 0];
            }
        } else {
            $resulta = $consulta->fetch(PDO::FETCH_ASSOC);
            $retorno = ["Mensaje" => "Existe.", "Stock" => $resulta['stock'], "id" => $resulta['id']];
        }
        return $retorno;
    }

    public static function ConsultarProductosEntreValores($valor1, $valor2)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * from tienda
                                                        WHERE precio BETWEEN :valor1 AND :valor2");
        $consulta->bindValue(":valor1", $valor1);
        $consulta->bindValue(":valor2", $valor2);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function GetProductoMasVendido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre, SUM(stock) AS total_vendida
                                                        FROM ventas
                                                        GROUP BY nombre, tipo, talla
                                                        ORDER BY total_vendida DESC
                                                        LIMIT 1;");
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);

    }

}