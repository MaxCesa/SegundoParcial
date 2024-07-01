<?php

require_once "./db/AccesoDatos.php";
class Usuario
{


    public static function RegistrarUsuario($usuario, $contraseña, $perfil, $email, $foto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, contraseña, perfil, email, foto) VALUES (:usuario, :clave, :perfil, :email, :foto)");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $contraseña);
        $consulta->bindValue(':perfil', $perfil);
        $consulta->bindValue(':email', $email);

        $dir_subida = './ImagenesDeUsuarios/2024/';
        if (!file_exists($dir_subida)) {
            mkdir($dir_subida, 0777, true);
            echo 'Se creó el directorio';
        }
        $fecha = date('Y-m-d');
        $nombrearchivo = $usuario . "-" . $perfil . "-" . $fecha;
        if (move_uploaded_file($foto['tmp_name'], $dir_subida . $nombrearchivo . '.jpg')) {
            echo "Se creó correctamente el archivo";
        } else {
            echo "¡Error!\n";
        }

        $consulta->bindValue(':foto', $nombrearchivo);
        $consulta->execute();



        return $objAccesoDatos->obtenerUltimoId();
    }

}